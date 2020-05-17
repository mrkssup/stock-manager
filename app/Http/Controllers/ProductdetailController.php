<?php

namespace App\Http\Controllers;
use App\Model\users;
use App\Model\products;
use App\Model\product_file;
use App\Model\stocks;
use App\Model\stock_places;
use App\Model\purchase;
use App\Model\po_product;
use App\Model\sell;
use App\Model\so_product;
use App\Model\category;
use App\Model\adjust;
use App\Model\adjust_stock;
use App\Model\tranfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Config;
use Mail;
use Storage;
use Validator;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class ProductdetailController extends Controller
{
    public function index(Request $request,$product_id)
    {
        $user_id = session('uid');
        $products = array();
        $query_products = products::join('users','users.user_id','=','products.user_id')
                        ->join('stocks','products.product_id','=','stocks.product_id')
                        ->join('product_file','products.product_id','=','product_file.product_id')
                        ->join('category','products.category_id','=','category.category_id')
                        ->select('products.product_id','products.product_code','products.product_name',
                        'products.product_price_buy','products.product_price_sell','products.product_unit',
                        'stocks.stock_id',DB::raw('sum(stocks.stock_number) as stock_number'),'product_file.product_file_server','category.category_id',
                        'category.category_name')
                        ->where('products.product_id',$product_id)
                        ->get();
        foreach($query_products as $key){
            $products['product_id'] = $key->product_id;
            $products['product_code'] = $key->product_code;
            $products['product_name'] = $key->product_name;
            $products['product_price_buy'] = $key->product_price_buy;
            $products['product_price_sell'] = $key->product_price_sell;
            $products['stock_id'] = $key->stock_id;
            $products['stock_number'] = $key->stock_number;
            $products['product_unit'] = $key->product_unit;
            $products['category_id'] = $key->category_id;
            $products['category_name'] = $key->category_name;
            $products['product_file_server'] = '/assets/files/'.$user_id."/".$key->product_id.'/'.$key->product_file_server;
        }
        $stocks = array();$i=0;
        $stock = array();
        $stock_number = array();
        $query_stocks = stock_places::select('stock_places.stock_place_id','stock_places.stock_place_code','stock_places.stock_place_name')
                                    ->get();
        foreach($query_stocks as $key){
            $stocks[$i]['order'] = $i+1;
            $stocks[$i]['stock_place_id'] = $key->stock_place_id;
            $stocks[$i]['stock_place_code'] = $key->stock_place_code;
            $stocks[$i]['stock_place_name'] = $key->stock_place_name;
            array_push($stock, $key->stock_place_name);
            $stocks[$i]['stock_number'] = $this->product_stock($product_id,$key->stock_place_id);
            array_push($stock_number, $stocks[$i]['stock_number']['stock_number']);
            $i++;
        }

        $query_stocks = stock_places::select('stock_places.stock_place_id','stock_places.stock_place_code','stock_places.stock_place_name')
                                    ->get();
        $date = Carbon::today()->subDays(5);
        $sells = array();$i=0;
        $sell_date =array();
        $sell_total =array();
        $query_sells = sell::join('users','sell.user_id','=','users.user_id')
                        ->join('so_product','sell.sell_id','=','so_product.sell_id')
                        ->join('products','so_product.product_id','=','products.product_id')
                        ->select('sell.sell_date',DB::raw('sum(sell.sell_total) as sell_total'))
                        ->where('users.user_id', $user_id)
                        ->where('products.product_id', $product_id)
                        ->where('sell.created_at', '>=', $date)
                        ->groupBy('sell.sell_date')
                        ->get();
        foreach($query_sells as $key){
            $sells[$i]['sell_date'] = $this->datethaishort($key->sell_date);
            array_push($sell_date, $sells[$i]['sell_date']);
            $sells[$i]['sell_total'] = $key->sell_total;
            array_push($sell_total, $sells[$i]['sell_total']);
            $i++;
        }
        $stock_cards = array();$i=0;
        $query_purchases = purchase::join('users','purchase.user_id','=','users.user_id')
                        ->join('po_product','purchase.purchase_id','=','po_product.purchase_id')
                        ->join('stocks','po_product.product_id','=','stocks.product_id')
                        ->join('stock_places','purchase.purchase_stock','=','stock_places.stock_place_id')
                        ->join('products','po_product.product_id','=','products.product_id')
                        ->select(DB::raw("'ซื้อ' as type"),'purchase.purchase_status_tranfer as status','purchase.purchase_code as code'
                        ,'po_product.product_number as number','stock_places.stock_place_name as stock_in',DB::raw("'-' as stock_out")
                        ,DB::raw('sum(stocks.stock_number) as stock_number'),'purchase.purchase_date as date')
                        ->where('users.user_id', $user_id)
                        ->where('products.product_id', $product_id)
                        ->groupBy('products.product_id');
        $query_sells = sell::join('users','sell.user_id','=','users.user_id')
                        ->join('so_product','sell.sell_id','=','so_product.sell_id')
                        ->join('stocks','so_product.product_id','=','stocks.product_id')
                        ->join('stock_places','sell.sell_stock','=','stock_places.stock_place_id')
                        ->join('products','so_product.product_id','=','products.product_id')
                        ->select(DB::raw("'ขาย' as type"),'sell.sell_status as status','sell.sell_code as code'
                        ,'so_product.product_number as number',DB::raw("'-' as stock_in"),'stock_places.stock_place_name as stock_out'
                        ,DB::raw('sum(stocks.stock_number) as stock_number'),'sell.sell_date as date')
                        ->where('users.user_id', $user_id)
                        ->where('products.product_id', $product_id)
                        ->groupBy('products.product_id');
        $query_adjusts = adjust::join('users','adjust.user_id','=','users.user_id')
                        ->join('adjust_stock','adjust.adjust_id','=','adjust_stock.adjust_id')
                        ->join('products','adjust.product_id','=','products.product_id')
                        ->join('stocks','products.product_id','=','stocks.product_id')
                        ->join('stock_places','stocks.stock_place_id','=','stock_places.stock_place_id')
                        ->select(DB::raw("'ปรับ' as type"),DB::raw("'สำเร็จ' as status"),'adjust.adjust_code as code'
                        ,'adjust_stock.adjust_stock_new as number',DB::raw("'-' as stock_in"),'stock_places.stock_place_name as stock_out'
                        ,DB::raw('sum(stocks.stock_number) as stock_number'),'adjust.adjust_date as date')
                        ->where('users.user_id', $user_id)
                        ->where('products.product_id', $product_id)
                        ->groupBy('stocks.stock_id');
        $query_tranfers = tranfer::join('users','tranfer.user_id','=','users.user_id')
                        ->join('products','tranfer.product_id','=','products.product_id')
                        ->join('stocks','products.product_id','=','stocks.product_id')
                        ->join('stock_places as sin', 'sin.stock_place_id', '=', 'tranfer.tranfer_stock_old')
                        ->join('stock_places as sout', 'sout.stock_place_id', '=', 'tranfer.tranfer_stock_new')
                        ->select(DB::raw("'โอน' as type"),DB::raw("'สำเร็จ' as status"),'tranfer.tranfer_code as code'
                        ,'tranfer.tranfer_stock_number as number','sin.stock_place_name as stock_in','sout.stock_place_name as stock_out'
                        ,DB::raw('sum(stocks.stock_number) as stock_number'),'tranfer.tranfer_date as date')
                        ->where('users.user_id', $user_id)
                        ->where('products.product_id', $product_id)
                        ->groupBy('tranfer.tranfer_id');

        $result_query = $query_tranfers
                        ->union($query_purchases)
                        ->union($query_sells)
                        ->union($query_adjusts)
                        ->orderBy('date','DESC')
                        ->get();
        foreach($result_query as $key){
            $stock_cards[$i]['order'] = $i+1;
            $stock_cards[$i]['type'] = $key->type;
            $stock_cards[$i]['status'] = $key->status;
            $stock_cards[$i]['code'] = $key->code;
            $stock_cards[$i]['number'] = $key->number;
            $stock_cards[$i]['stock_in'] = $key->stock_in;
            $stock_cards[$i]['stock_out'] = $key->stock_out;
            $stock_cards[$i]['stock_number'] = $key->stock_number;
            $stock_cards[$i]['date'] = $this->datethaishort($key->date);
            $i++;
        }
        return view('products._productdetail')->with(['products' => $products,'stocks'=>$stock
        ,'stock_number'=>$stock_number, 'sell_total'=>$sell_total ,'sell_date'=>$sell_date
        ,'stock_cards'=>$stock_cards]);
    }


    public function store(Request $request)
    {
        //return $request->all();
        $user_id = session('uid');
        if($user_id == ''){
            return redirect('/');
        }else{
            if($request){
                DB::beginTransaction();
                try{
                    $create_product= products::create([
                        'product_code' => $request->product_code,
                        'product_name' => $request->product_name,
                        'product_price_buy' => $request->product_price_buy,
                        'product_price_sell' => $request->product_price_sell,
                        'product_unit' => $request->product_unit,
                        'product_volume' => $request->product_volume,
                        'user_id' => $user_id,
                        'category_id' => $request->category_id
                    ]);
                    if($request->hasFile('file')){    // ถ้ามีการอัพโหลดไฟล์
                        $file =$request->file('file');
                        $file_name = $file->getClientOriginalName();
                        $file_ext = strtolower($file->getClientOriginalExtension());
                        $file_server = strtoupper(uniqid('file_').'-'.time());
                        $upload_storage = Storage::putFileAs('files', $file,'/'.$user_id.'/'.$file_server.'.'.$file_ext);
                        if($upload_storage){
                            product_file::create([
                                'product_file_name' => $file_name,
                                'product_file_server' => $file_server,
                                'product_file_ext' => $file_ext,
                                'product_id' => $create_product['product_id'],
                            ]);
                        }else{
                            return view('products._addproduct')->with('alert' , 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์' );
                        }
                    }
                    $create_stock= stocks::create([
                        'stock_number' => $request->stock_number,
                        'stock_number_sale' => $request->stock_number,
                        'product_id' => $create_product['product_id'],
                        'stock_place_id' => $request->stock_place_id
                    ]);
                    DB::commit();
                    return redirect('/products')->with('alert' , "เพิ่มข้อมูลสำเร็จ");
                } catch (\Exception $e) {
                    DB::rollback();
                    return $e;
                    return view('products._addproduct')->with('alert' , "การเพิ่มข้อมูลผิดพลาด".$e );
                }
            }else{
                return view('products._addproduct')->with('alert' , "ชุดข้อมูลไม่ถูกต้อง" );
            }
        }
    }
    public function export(Request $request,$product_id)
    {
        $user_id = session('uid');
        if($user_id == ''){
            return redirect('/');
        }else{
            $stock_cards = array();$i=0;
            $query_purchases = purchase::join('users','purchase.user_id','=','users.user_id')
                        ->join('po_product','purchase.purchase_id','=','po_product.purchase_id')
                        ->join('stocks','po_product.product_id','=','stocks.product_id')
                        ->join('stock_places','purchase.purchase_stock','=','stock_places.stock_place_id')
                        ->join('products','po_product.product_id','=','products.product_id')
                        ->select(DB::raw("'ซื้อ' as type"),'purchase.purchase_status_tranfer as status','purchase.purchase_code as code'
                        ,'po_product.product_number as number','stock_places.stock_place_name as stock_in',DB::raw("'-' as stock_out")
                        ,DB::raw('sum(stocks.stock_number) as stock_number'),'purchase.purchase_date as date')
                        ->where('users.user_id', $user_id)
                        ->where('products.product_id', $product_id)
                        ->groupBy('products.product_id');
            $query_sells = sell::join('users','sell.user_id','=','users.user_id')
                        ->join('so_product','sell.sell_id','=','so_product.sell_id')
                        ->join('stocks','so_product.product_id','=','stocks.product_id')
                        ->join('stock_places','sell.sell_stock','=','stock_places.stock_place_id')
                        ->join('products','so_product.product_id','=','products.product_id')
                        ->select(DB::raw("'ขาย' as type"),'sell.sell_status as status','sell.sell_code as code'
                        ,'so_product.product_number as number',DB::raw("'-' as stock_in"),'stock_places.stock_place_name as stock_out'
                        ,DB::raw('sum(stocks.stock_number) as stock_number'),'sell.sell_date as date')
                        ->where('users.user_id', $user_id)
                        ->where('products.product_id', $product_id)
                        ->groupBy('products.product_id');
            $query_adjusts = adjust::join('users','adjust.user_id','=','users.user_id')
                        ->join('adjust_stock','adjust.adjust_id','=','adjust_stock.adjust_id')
                        ->join('products','adjust.product_id','=','products.product_id')
                        ->join('stocks','products.product_id','=','stocks.product_id')
                        ->join('stock_places','stocks.stock_place_id','=','stock_places.stock_place_id')
                        ->select(DB::raw("'ปรับ' as type"),DB::raw("'สำเร็จ' as status"),'adjust.adjust_code as code'
                        ,'adjust_stock.adjust_stock_new as number',DB::raw("'-' as stock_in"),'stock_places.stock_place_name as stock_out'
                        ,DB::raw('sum(stocks.stock_number) as stock_number'),'adjust.adjust_date as date')
                        ->where('users.user_id', $user_id)
                        ->where('products.product_id', $product_id)
                        ->groupBy('stocks.stock_id');
            $query_tranfers = tranfer::join('users','tranfer.user_id','=','users.user_id')
                        ->join('products','tranfer.product_id','=','products.product_id')
                        ->join('stocks','products.product_id','=','stocks.product_id')
                        ->join('stock_places as sin', 'sin.stock_place_id', '=', 'tranfer.tranfer_stock_old')
                        ->join('stock_places as sout', 'sout.stock_place_id', '=', 'tranfer.tranfer_stock_new')
                        ->select(DB::raw("'โอน' as type"),DB::raw("'สำเร็จ' as status"),'tranfer.tranfer_code as code'
                        ,'tranfer.tranfer_stock_number as number','sin.stock_place_name as stock_in','sout.stock_place_name as stock_out'
                        ,DB::raw('sum(stocks.stock_number) as stock_number'),'tranfer.tranfer_date as date')
                        ->where('users.user_id', $user_id)
                        ->where('products.product_id', $product_id)
                        ->groupBy('tranfer.tranfer_id');

            $result_query = $query_tranfers
                            ->union($query_purchases)
                            ->union($query_sells)
                            ->union($query_adjusts)
                            ->orderBy('date','DESC')
                            ->get();
            foreach($result_query as $key){
                $stock_cards[$i]['order'] = $i+1;
                $stock_cards[$i]['type'] = $key->type;
                $stock_cards[$i]['status'] = $key->status;
                $stock_cards[$i]['code'] = $key->code;
                $stock_cards[$i]['number'] = $key->number;
                $stock_cards[$i]['stock_in'] = $key->stock_in;
                $stock_cards[$i]['stock_out'] = $key->stock_out;
                $stock_cards[$i]['stock_number'] = $key->stock_number;
                $stock_cards[$i]['date'] = $this->datethaishort($key->date);
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
            $header2 = array('ลำดับ', 'ประเภท', 'สภานะ', 'รายการเลขที่','จำนวน', 'จาก' , 'ไป','คงเหลือ','วันที่ทำรายการ');

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
            foreach($result_query as $key){
            $worksheet->setCellValue(chr($col+1).($start2+$i), $i);
            $worksheet->setCellValue(chr($col+2).($start2+$i),$key["type"]);
            if($key["status"] == '1'){
                $worksheet->setCellValue(chr($col+3).($start2+$i),'สำเร็จ');
            }elseif($key["status"] == '0'){
                $worksheet->setCellValue(chr($col+3).($start2+$i),'รอโอนสินค้า');
            }else{
                $worksheet->setCellValue(chr($col+3).($start2+$i),'สำเร็จ');
            }
            $worksheet->setCellValue(chr($col+4).($start2+$i),$key["code"]);
            $worksheet->setCellValue(chr($col+5).($start2+$i),$key["number"]);
            $worksheet->setCellValue(chr($col+6).($start2+$i),$key["stock_out"]);
            $worksheet->setCellValue(chr($col+7).($start2+$i),$key["stock_in"]);
            $worksheet->setCellValue(chr($col+8).($start2+$i),$key["stock_number"]);
            $worksheet->setCellValue(chr($col+9).($start2+$i),$key["date"]);
            $i++;
             }

             $worksheet->getStyle(chr($col+1).($start2).':'.chr($col+8).($start2+$i-1))
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
                ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('000000'));

            // Rename worksheet
            $spreadsheet->getActiveSheet()->setTitle('Stock_card');

            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $spreadsheet->setActiveSheetIndex(0);

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('Stock_card.xlsx');
            return response()->download('Stock_card.xlsx');


        }
    }



    protected function product_stock($product_id,$stock_place_id)
    {
        $product_stock = array();
        $query_product_stock= products::join('users','users.user_id','=','products.user_id')
                        ->join('stocks','products.product_id','=','stocks.product_id')
                        ->select('products.product_id','products.product_code','products.product_name','stocks.stock_number','stocks.stock_place_id')
                        ->where('stocks.product_id', $product_id)
                        ->where('stocks.stock_place_id', $stock_place_id)
                        ->get();
        foreach($query_product_stock as $key){
            $product_stock['product_id'] = $key->product_id;
            $product_stock['product_code'] = $key->product_code;
            $product_stock['product_name'] = $key->product_name;
            $product_stock['stock_number'] = $key->stock_number;
            $product_stock['stock_place_id'] = $key->stock_place_id;
        }
        return $product_stock;
    }


    protected function datethaishort($strDate)
    {
      $strYear = date("Y",strtotime($strDate))+543;
      //$strYear = substr($strYear, -2);
  		$strMonth= date("n",strtotime($strDate));
  		$strDay= date("j",strtotime($strDate));
  		$strHour= date("H",strtotime($strDate));
  		$strMinute= date("i",strtotime($strDate));
  		$strSeconds= date("s",strtotime($strDate));
      //$strMonthCut = Array("","มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฏาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม");
  		$strMonthCut = Array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
  		$strMonthThai=$strMonthCut[$strMonth];
  		return "$strDay $strMonthThai $strYear"; //, $strHour:$strMinute";
    }

    protected function get_host()
    {
      $domain = url('/');
      $info = parse_url($domain);
      $host = $info['host'];
      return $host;
    }



}
