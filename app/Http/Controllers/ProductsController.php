<?php

namespace App\Http\Controllers;
use App\Model\users;
use App\Model\products;
use App\Model\product_file;
use App\Model\stocks;
use App\Model\stock_places;
use App\Model\category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Config;
use Mail;
use Storage;
use Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $user_id = session('uid');
        $products = array();$i=0;
        $query_products = products::join('users','users.user_id','=','products.user_id')
                        ->join('stocks','products.product_id','=','stocks.product_id')
                        ->join('product_file','products.product_id','=','product_file.product_id')
                        ->select('products.product_id','products.product_code','products.product_name',
                        'products.product_price_buy','products.product_price_sell','stocks.stock_id'
                        ,DB::raw('sum(stocks.stock_number) as stock_number'),'product_file.product_file_server')
                        ->where('users.user_id', $user_id)
                        ->where('products.product_status', '1');
        if($request->has('stock')){
            $query_products = $query_products->where('stocks.stock_place_id',$request->stock);
        }
        if($request->has('category')){
            $query_products = $query_products->where('products.category_id',$request->category);
        }
        $query_products=$query_products->groupBy('product_id');
        $query_products=$query_products->get();
        foreach($query_products as $key){
            $products[$i]['order'] = $i+1;
            $products[$i]['product_id'] = $key->product_id;
            $products[$i]['product_code'] = $key->product_code;
            $products[$i]['product_name'] = $key->product_name;
            $products[$i]['product_price_buy'] = $key->product_price_buy;
            $products[$i]['product_price_sell'] = $key->product_price_sell;
            $products[$i]['stock_id'] = $key->stock_id;
            $products[$i]['stock_number'] = $key->stock_number;
            $products[$i]['product_file_server'] = '/assets/files/'.$user_id."/".$key->product_id.'/'.$key->product_file_server;
            $i++;
        }
        return view('products._products')->with(['products' => $products]);
    }


    public function search(Request $request)
    {
        $user_id = session('uid');
        $search = $request->search;
        $products = array();$i=0;
        $query_products = products::join('users','users.user_id','=','products.user_id')
                        ->join('stocks','products.product_id','=','stocks.product_id')
                        ->join('product_file','products.product_id','=','product_file.product_id')
                        ->select('products.product_id','products.product_code','products.product_name',
                        'products.product_price_buy','products.product_price_sell','stocks.stock_id'
                        ,'stocks.stock_number','product_file.product_file_server')
                        ->where('users.user_id', $user_id);
        $query_products = $query_products->where(function($query) use ($search)
                        {
                            $query->where('products.product_code', 'LIKE' , '%'.$search.'%')
                                  ->orWhere('products.product_name', 'LIKE' , '%'.$search.'%');
                        });
        $query_products = $query_products->get();
        foreach($query_products as $key){
            $products[$i]['order'] = $i+1;
            $products[$i]['product_id'] = $key->product_id;
            $products[$i]['product_code'] = $key->product_code;
            $products[$i]['product_name'] = $key->product_name;
            $products[$i]['product_price_buy'] = $key->product_price_buy;
            $products[$i]['product_price_sell'] = $key->product_price_sell;
            $products[$i]['stock_id'] = $key->stock_id;
            $products[$i]['stock_number'] = $key->stock_number;
            $products[$i]['product_file_server'] = '/assets/files/'.$user_id."/".$key->product_id.'/'.$key->product_file_server;
            $i++;
        }
        return view('products._products')->with(['products' => $products]);
    }


    public function delete(Request $request)
    {
        $user_id = session('uid');
        if($user_id == ''){
            return redirect('/');
        }else{
            if($request){
                $query_check_product = products::where('product_id',$request->product_id)->get();
                if(!is_null($query_check_product)){
                    DB::beginTransaction();
                try{
                    $delete_product = products::where('product_id','=',$request->product_id)
                        ->update([
                        'product_status' => '99',
                        ]);
                    DB::commit();
                    return redirect('/products');
                    } catch (\Exception $e) {
                        DB::rollback();
                        return $e;
                        return view('products._products')->with('alert' , "การเพิ่มข้อมูลผิดพลาด".$e );
                    }
                }else{
                    return view('others._notFound');
                }
            }else{
                return view('products._editproduct')->with('alert' , "ชุดข้อมูลไม่ถูกต้อง" );
            }
        }
    }


    public function export(Request $request)
    {
        $user_id = session('uid');
        if($user_id == ''){
            return redirect('/');
        }else{
            $products = array();$i=0;
            $query_products = products::join('users','users.user_id','=','products.user_id')
                        ->join('stocks','products.product_id','=','stocks.product_id')
                        ->join('product_file','products.product_id','=','product_file.product_id')
                        ->select('products.product_id','products.product_code','products.product_name',
                        'products.product_price_buy','products.product_price_sell','stocks.stock_id'
                        ,DB::raw('sum(stocks.stock_number) as stock_number'),
                        'product_file.product_file_server','products.updated_at')
                        ->where('users.user_id', $user_id)
                        ->where('products.product_status', '1');
            $query_products=$query_products->groupBy('product_id');
            $query_products=$query_products->get();
            foreach($query_products as $key){
                $products[$i]['order'] = $i+1;
                $products[$i]['product_id'] = $key->product_id;
                $products[$i]['product_code'] = $key->product_code;
                $products[$i]['product_name'] = $key->product_name;
                $products[$i]['product_price_buy'] = $key->product_price_buy;
                $products[$i]['product_price_sell'] = $key->product_price_sell;
                $products[$i]['stock_id'] = $key->stock_id;
                $products[$i]['stock_number'] = $key->stock_number;
                $products[$i]['updated_at'] = $key->updated_at;
                $i++;
            }
            // Create new Spreadsheet object
            $spreadsheet = new Spreadsheet();
            $worksheet = $spreadsheet->setActiveSheetIndex(0);
            //$worksheet->getColumnDimension('B')->setAutoSize(true);
            // Add some data
            $spreadsheet->getDefaultStyle()->getFont()->setName('Angsana New')->setSize(16);
            // $worksheet->getCell('A1')->setValue("");
            // $worksheet->getStyle('A1')->applyFromArray($styleArrayTitle);
            // $worksheet->setCellValue('A2',  $stat_head);
            // if (Arr::exists($input, 'intcodcat')) {
            //     if ($input['intcodcat']!='') {
            //     $worksheet->setCellValue('A3',  $main_meeting_name);
            // }
            $start = 1;

            // $worksheet->setCellValue('A'.($start+1),  "รายการข้อมูล");
            // // Header starts ///
            //$header2 = array('ลำดับ', 'ประเภทการประชุม', 'ชื่อการประชุม', 'ครั้งที่ประชุม', 'วันที่ประชุม','จำนวนองค์ประชุม', 'จำนวนผู้เข้าร่วม' , 'จำนวนเอกสาร (แผ่น)','จำนวนกระดาษที่ลดได้ (แผ่น)');
            $header2 = array('ลำดับ', 'รหัสสินค้า', 'ชื่อสินค้า', 'ราคาซื้อ','ราคาขาย', 'ยอดคงเหลือ' ,'วันที่แก้ไขล่าสุด');

            $start2 = +$start;
            for ($i = 0; $i < count($header2); $i++) {
                $temp = 66+$i;
                $worksheet->setCellValue(chr($temp).$start2, $header2[$i]);
                $worksheet->getColumnDimension(chr($temp))->setAutoSize(true);
                //$worksheet->getStyle(chr($temp).$start2)->getFont()->setBold(true);
            }
            //// header is over ///////
            //First row of data
            $i=1;
            $col=65;
            foreach($query_products as $key){
            $worksheet->setCellValue(chr($col+1).($start2+$i), $i);
            $worksheet->setCellValue(chr($col+2).($start2+$i),$key["product_code"]);
            $worksheet->setCellValue(chr($col+3).($start2+$i),$key["product_name"]);
            $worksheet->setCellValue(chr($col+4).($start2+$i),$key["product_price_buy"]);
            $worksheet->setCellValue(chr($col+5).($start2+$i),$key["product_price_sell"]);
            $worksheet->setCellValue(chr($col+6).($start2+$i),$key["stock_number"]);
            $worksheet->setCellValue(chr($col+7).($start2+$i),$key["updated_at"]);
            $i++;
             }

             $worksheet->getStyle(chr($col+1).($start2).':'.chr($col+8).($start2+$i-1))
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
                ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('000000'));

            // Rename worksheet
            $spreadsheet->getActiveSheet()->setTitle('Product');

            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $spreadsheet->setActiveSheetIndex(0);

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('Products.xlsx');
            return response()->download('Products.xlsx');


        }
    }




    protected function get_host()
    {
      $domain = url('/');
      $info = parse_url($domain);
      $host = $info['host'];
      return $host;
    }



}
