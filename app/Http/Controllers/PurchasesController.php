<?php

namespace App\Http\Controllers;
use App\Model\users;
use App\Model\products;
use App\Model\product_file;
use App\Model\stocks;
use App\Model\stock_places;
use App\Model\category;
use App\Model\purchase;
use App\Model\po_product;
use App\Model\customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Config;
use Mail;
use PDF;
use Storage;
use Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class PurchasesController extends Controller
{
    public function index(Request $request)
    {
        $user_id = session('uid');
        $purchases = array();$i=0;
        $query_purchases = purchase::join('users','purchase.user_id','=','users.user_id')
                        ->join('po_product','purchase.purchase_id','=','po_product.purchase_id')
                        ->join('products','po_product.product_id','=','products.product_id')
                        ->select('purchase.purchase_id','purchase.purchase_code','purchase.purchase_date'
                        ,'products.product_code','products.product_id','purchase.purchase_stock'
                        ,'products.product_name','purchase.purchase_total','purchase.purchase_status_tranfer'
                        ,'po_product.product_number','po_product.product_total')
                        ->where('users.user_id', $user_id)
                        ->get();
        foreach($query_purchases as $key){
            $purchases[$i]['order'] = $i+1;
            $purchases[$i]['purchase_id'] = $key->purchase_id;
            $purchases[$i]['purchase_code'] = $key->purchase_code;
            $purchases[$i]['purchase_date'] = $this->datethaishort($key->purchase_date);
            $purchases[$i]['product_id'] = $key->product_id;
            $purchases[$i]['product_code'] = $key->product_code;
            $purchases[$i]['product_name'] = $key->product_name;
            $purchases[$i]['product_number'] = $key->product_number;
            $purchases[$i]['product_total'] = $key->product_total;
            $purchases[$i]['purchase_total'] = $key->purchase_total;
            $purchases[$i]['purchase_stock'] = $key->purchase_stock;
            $purchases[$i]['purchase_status_tranfer'] = $key->purchase_status_tranfer;
            $i++;
        }
        return view('purchases._purchases')->with(['purchases' => $purchases]);
    }


    public function search(Request $request)
    {
        $user_id = session('uid');
        $search = $request->search;
        $purchases = array();$i=0;
        $query_purchases = purchase::join('users','purchase.user_id','=','users.user_id')
                        ->join('po_product','purchase.purchase_id','=','po_product.purchase_id')
                        ->join('products','po_product.product_id','=','products.product_id')
                        ->select('purchase.purchase_id','purchase.purchase_code','purchase.purchase_date'
                        ,'products.product_code','products.product_id','purchase.purchase_stock'
                        ,'products.product_name','purchase.purchase_total','purchase.purchase_status_tranfer')
                        ->where('users.user_id', $user_id);
        $query_purchases = $query_purchases->where(function($query) use ($search)
                        {
                            $query->where('purchase.purchase_code', 'LIKE' , '%'.$search.'%')
                                  ->orWhere('products.product_code', 'LIKE' , '%'.$search.'%')
                                  ->orWhere('products.product_name', 'LIKE' , '%'.$search.'%');
                        });
        $query_purchases = $query_purchases->get();
        foreach($query_purchases as $key){
            $purchases[$i]['order'] = $i+1;
            $purchases[$i]['purchase_id'] = $key->purchase_id;
            $purchases[$i]['purchase_code'] = $key->purchase_code;
            $purchases[$i]['purchase_date'] = $this->datethaishort($key->purchase_date);
            $purchases[$i]['product_id'] = $key->product_id;
            $purchases[$i]['product_code'] = $key->product_code;
            $purchases[$i]['product_name'] = $key->product_name;
            $purchases[$i]['purchase_total'] = $key->purchase_total;
            $purchases[$i]['purchase_stock'] = $key->purchase_stock;
            $purchases[$i]['purchase_status_tranfer'] = $key->purchase_status_tranfer;
            $i++;
        }
        return view('purchases._purchases')->with(['purchases' => $purchases]);
    }





    public function put_status(Request $request)
    {
        $user_id = session('uid');
        //return $request->all();
        if($user_id == ''){
            return redirect('/');
        }else{
            if($request){
                $purchases = array();
                $query_check_purchase = purchase::where('purchase_id',$request->purchase_id)->get();
                if(!is_null($query_check_purchase)){
                    DB::beginTransaction();
                try{
                    $update_purchase = purchase::where('purchase_id','=',$request->purchase_id)
                        ->update([
                            'purchase_status_tranfer' => '1',
                        ]);
                    DB::commit();
                    $query_purchases = purchase::join('users','purchase.user_id','=','users.user_id')
                    ->join('po_product','purchase.purchase_id','=','po_product.purchase_id')
                    ->join('stock_places','purchase.purchase_stock','=','stock_places.stock_place_id')
                    ->join('products','po_product.product_id','=','products.product_id')
                    ->join('customer','purchase.customer_id','=','customer.customer_id')
                    ->select('purchase.purchase_id','purchase.purchase_code','purchase.purchase_date'
                    ,'products.product_code','products.product_id','purchase.purchase_stock','stock_places.stock_place_name'
                    ,'products.product_name','purchase.purchase_total','purchase.purchase_reference','purchase.purchase_status_tranfer'
                    ,'po_product.product_number','po_product.product_total','products.product_price_buy'
                    ,'purchase.customer_id','customer.customer_name','customer.customer_detail'
                    ,DB::raw('CONCAT(users.first_name," ",users.last_name) AS fullname'))
                    ->where('users.user_id', $user_id)
                    ->where('purchase.purchase_id', $request->purchase_id)
                    ->get();
                    foreach($query_purchases as $key){
                        $purchases['purchase_id'] = $key->purchase_id;
                        $purchases['purchase_code'] = $key->purchase_code;
                        $purchases['purchase_date'] = $this->datethaishort($key->purchase_date);
                        $purchases['purchase_user'] = $key->fullname;
                        $purchases['purchase_reference'] = $key->purchase_reference;
                        $purchases['customer_id'] = $key->customer_id;
                        $purchases['customer_name'] = $key->customer_name;
                        $purchases['customer_detail'] = $key->customer_detail;
                        $purchases['product_id'] = $key->product_id;
                        $purchases['product_code'] = $key->product_code;
                        $purchases['product_name'] = $key->product_name;
                        $purchases['product_number'] = $key->product_number;
                        $purchases['product_total'] = $key->product_total;
                        $purchases['product_price_buy'] = $key->product_price_buy;
                        $purchases['purchase_total'] = $key->purchase_total;
                        $purchases['full_name'] = $key->fullname;
                        $purchases['stock_place_name'] = $key->stock_place_name;
                        $purchases['purchase_status_tranfer'] = $key->purchase_status_tranfer;
                    }
                        $pdf = PDF::loadView('pr',['purchases'=>$purchases]);
                        //return $pdf->download('invoice.pdf');
                        return  $pdf->stream('pr.pdf',array('Attachment'=>0));
                    } catch (\Exception $e) {
                        DB::rollback();
                        //return $e;
                        return redirect()->route('purchases')->with('alert' , "การแก้ไขข้อมูลผิดพลาด".$e );
                    }
                }else{
                    return view('others._notFound');
                }
            }else{
                return redirect('/purchases')->with('alert' , "ชุดข้อมูลไม่ถูกต้อง" );
            }
        }
    }

    public function cancel_status(Request $request)
    {
        $user_id = session('uid');
        //return $request->all();
        if($user_id == ''){
            return redirect('/');
        }else{
            if($request){
                $query_check_purchase = purchase::where('purchase_id',$request->purchase_id)->get();
                if(!is_null($query_check_purchase)){
                    DB::beginTransaction();
                try{
                    $update_purchase = purchase::where('purchase_id','=',$request->purchase_id)
                        ->update([
                            'purchase_status_tranfer' => '9',
                        ]);
                    DB::commit();
                    return redirect('/purchases');
                    } catch (\Exception $e) {
                        DB::rollback();
                        return redirect()->route('purchases')->with('alert' , "การแก้ไขข้อมูลผิดพลาด".$e );
                    }
                }else{
                    return view('others._notFound');
                }
            }else{
                return redirect('/purchases')->with('alert' , "ชุดข้อมูลไม่ถูกต้อง" );
            }
        }
    }

    public function export(Request $request)
    {
        $user_id = session('uid');
        if($user_id == ''){
            return redirect('/');
        }else{
            $purchases = array();$i=0;
            $query_purchases = purchase::join('users','purchase.user_id','=','users.user_id')
                        ->join('po_product','purchase.purchase_id','=','po_product.purchase_id')
                        ->join('products','po_product.product_id','=','products.product_id')
                        ->select('purchase.purchase_id','purchase.purchase_code','purchase.purchase_date'
                        ,'products.product_code','products.product_id','purchase.purchase_stock'
                        ,'products.product_name','purchase.purchase_total','purchase.purchase_status_tranfer'
                        ,'po_product.product_number','po_product.product_total')
                        ->where('users.user_id', $user_id)
                        ->get();
            foreach($query_purchases as $key){
                $purchases[$i]['order'] = $i+1;
                $purchases[$i]['purchase_id'] = $key->purchase_id;
                $purchases[$i]['purchase_code'] = $key->purchase_code;
                $purchases[$i]['purchase_date'] = $this->datethaishort($key->purchase_date);
                $purchases[$i]['product_id'] = $key->product_id;
                $purchases[$i]['product_code'] = $key->product_code;
                $purchases[$i]['product_name'] = $key->product_name;
                $purchases[$i]['product_number'] = $key->product_number;
                $purchases[$i]['product_total'] = $key->product_total;
                $purchases[$i]['purchase_total'] = $key->purchase_total;
                $purchases[$i]['purchase_stock'] = $key->purchase_stock;
                $purchases[$i]['purchase_status_tranfer'] = $key->purchase_status_tranfer;
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
            $header2 = array('ลำดับ', 'ประเภท','รายการเลขที่', 'รหัสสินค้า', 'ชื่อสินค้า','จำนวน','มูลค่า', 'สถานะ' ,'วันที่ทำรายการ');

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
            foreach($query_purchases as $key){
            $worksheet->setCellValue(chr($col+1).($start2+$i), $i);
            $worksheet->setCellValue(chr($col+2).($start2+$i),'รายการซื้อ');
            $worksheet->setCellValue(chr($col+3).($start2+$i),$key["purchase_code"]);
            $worksheet->setCellValue(chr($col+4).($start2+$i),$key["product_code"]);
            $worksheet->setCellValue(chr($col+5).($start2+$i),$key["product_name"]);
            $worksheet->setCellValue(chr($col+6).($start2+$i),$key["product_number"]);
            $worksheet->setCellValue(chr($col+7).($start2+$i),$key["purchase_total"]);
            if($key["purchase_status_tranfer"] == '1'){
                $worksheet->setCellValue(chr($col+8).($start2+$i),'รอดำเนินการ');
            }elseif($key["purchase_status_tranfer"] == '2'){
                $worksheet->setCellValue(chr($col+8).($start2+$i),'สำเร็จ');
            }elseif($key["purchase_status_tranfer"] == '9'){
                $worksheet->setCellValue(chr($col+8).($start2+$i),'ยกเลิก');
            }else{
                $worksheet->setCellValue(chr($col+8).($start2+$i),'รอคำสั่งซื้อ');
            }
            $worksheet->setCellValue(chr($col+9).($start2+$i),$this->datethaishort($key["purchase_date"]));
            $i++;
             }

             $worksheet->getStyle(chr($col+1).($start2).':'.chr($col+9).($start2+$i-1))
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
                ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('000000'));

            // Rename worksheet
            $spreadsheet->getActiveSheet()->setTitle('Purcahse');

            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $spreadsheet->setActiveSheetIndex(0);

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('Purchase.xlsx');
            return response()->download('Purchase.xlsx');


        }
    }





    protected function get_host()
    {
      $domain = url('/');
      $info = parse_url($domain);
      $host = $info['host'];
      return $host;
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
      $strMonthCut = Array("","มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฏาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม");
  		//$strMonthCut = Array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
  		$strMonthThai=$strMonthCut[$strMonth];
  		return "$strDay $strMonthThai $strYear"; //, $strHour:$strMinute";
    }



}
