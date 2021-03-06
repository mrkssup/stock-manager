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
use App\Model\sell;
use App\Model\so_product;
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


class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user_id = session('uid');
        $sum_all = array();
        $query_sum_purchase = purchase::select(DB::raw('sum(purchase.purchase_total) as sum_purchase'))
                            ->where('purchase.purchase_status_tranfer', '2')
                            ->where('purchase.user_id', $user_id)
                            ->get();
        foreach($query_sum_purchase as $key){
            $sum_all['sum_purchase'] = $key->sum_purchase;
        }
        $query_sum_sell = sell::select(DB::raw('sum(sell.sell_total) as sum_sell'))
                            ->where('sell.sell_status', '2')
                            ->where('sell.user_id', $user_id)
                            ->get();
        foreach($query_sum_sell as $key){
            $sum_all['sum_sell'] = $key->sum_sell;
        }
        $query_sum_stock = stocks::join('products','stocks.product_id','=','products.product_id')
                            ->select(DB::raw('sum(stocks.stock_number) as sum_stock'))
                            ->where('products.user_id', $user_id)
                            ->get();
        foreach($query_sum_stock as $key){
            $sum_all['sum_stock'] = $key->sum_stock;
        }
        $purchase_6_month = array();$i=0;
        $purchase_month = array();
        $purchase = array();
        $query_purchase_6_month   = purchase::select(DB::raw('MONTH(purchase_date) AS month'),DB::raw('sum(purchase.purchase_total) as total'))
                                            ->where('purchase_date','>=',Carbon::now()->subMonths(6)->format('m'))
                                            ->where('purchase.purchase_status_tranfer', '2')
                                            ->groupBy(DB::raw('YEAR(purchase_date) DESC, MONTH(purchase_date) ASC'))->get();

        foreach($query_purchase_6_month as $key){
            $purchase_6_month[$i]['month'] = $key->month;
            //array_push($purchase_month, $this->monththaishort($purchase_6_month[$i]['month']));
            array_push($purchase_month, $key->month);
            $purchase_6_month[$i]['total'] = $key->total;
            array_push($purchase, $purchase_6_month[$i]['total']);
            $i++;
        }


        $sell_6_month = array();$i=0;
        $sell_month = array();
        $sell = array();
        $query_sell_6_month   = sell::select(DB::raw('MONTH(sell_date) AS month'),DB::raw('sum(sell.sell_total) as total'))
                                            ->where('sell_date','>=',Carbon::now()->subMonths(6)->format('m'))
                                            ->where('sell.sell_status', '2')
                                            ->groupBy(DB::raw('YEAR(sell_date) DESC, MONTH(sell_date) ASC'))->get();


        foreach($query_sell_6_month as $key){
            $sell_6_month[$i]['month'] = $key->month;
            //array_push($sell_month, $this->monththaishort($sell_6_month[$i]['month']));
            array_push($sell_month, $key->month);
            $sell_6_month[$i]['total'] = $key->total;
            array_push($sell, $sell_6_month[$i]['total']);
            $i++;
        }

        $get_month = array_unique(array_merge($purchase_month, $sell_month));
        $month =array();
        foreach($get_month as $value){
            array_push($month, $value);
        }
        sort($month);
        $str_month =array();
        for($i=0;$i<count($month);$i++){
            array_push($str_month,$this->monththaishort($month[$i]));
        }


        $stocks = array();$i=0;
        $stocks_name = array();
        $stocks_number = array();
        $query_stocks = stock_places::join('stocks','stock_places.stock_place_id','=','stocks.stock_place_id')
                                    ->select('stock_places.stock_place_id','stock_places.stock_place_code'
                                    ,'stock_places.stock_place_name',DB::raw('sum(stocks.stock_number) as stock_number'))
                                    ->groupBy('stock_places.stock_place_id')
                                    ->get();
        foreach($query_stocks as $key){
            $stocks[$i]['order'] = $i+1;
            $stocks[$i]['stock_place_id'] = $key->stock_place_id;
            $stocks[$i]['stock_place_code'] = $key->stock_place_code;
            $stocks[$i]['stock_place_name'] = $key->stock_place_name;
            array_push($stocks_name, $stocks[$i]['stock_place_name']);
            $stocks[$i]['stock_number'] = $key->stock_number;
            array_push($stocks_number, $stocks[$i]['stock_number']);
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
                        ,DB::raw('sum(stocks.stock_number) as stock_number'),'purchase.purchase_date as date','products.product_code')
                        ->where('users.user_id', $user_id)
                        ->groupBy('purchase.purchase_id');
        $query_sells = sell::join('users','sell.user_id','=','users.user_id')
                        ->join('so_product','sell.sell_id','=','so_product.sell_id')
                        ->join('stocks','so_product.product_id','=','stocks.product_id')
                        ->join('stock_places','sell.sell_stock','=','stock_places.stock_place_id')
                        ->join('products','so_product.product_id','=','products.product_id')
                        ->select(DB::raw("'ขาย' as type"),'sell.sell_status as status','sell.sell_code as code'
                        ,'so_product.product_number as number',DB::raw("'-' as stock_in"),'stock_places.stock_place_name as stock_out'
                        ,DB::raw('sum(stocks.stock_number) as stock_number'),'sell.sell_date as date','products.product_code')
                        ->where('users.user_id', $user_id)
                        ->groupBy('sell.sell_id');
        $query_adjusts = adjust::join('users','adjust.user_id','=','users.user_id')
                        ->join('adjust_stock','adjust.adjust_id','=','adjust_stock.adjust_id')
                        ->join('products','adjust.product_id','=','products.product_id')
                        ->join('stocks','products.product_id','=','stocks.product_id')
                        ->join('stock_places','stocks.stock_place_id','=','stock_places.stock_place_id')
                        ->select(DB::raw("'ปรับ' as type"),DB::raw("'สำเร็จ' as status"),'adjust.adjust_code as code'
                        ,'adjust_stock.adjust_stock_new as number',DB::raw("'-' as stock_in"),'stock_places.stock_place_name as stock_out'
                        ,DB::raw('sum(stocks.stock_number) as stock_number'),'adjust.adjust_date as date','products.product_code')
                        ->where('users.user_id', $user_id)
                        ->groupBy('adjust.adjust_id');
        $query_tranfers = tranfer::join('users','tranfer.user_id','=','users.user_id')
                        ->join('products','tranfer.product_id','=','products.product_id')
                        ->join('stocks','products.product_id','=','stocks.product_id')
                        ->join('stock_places as sin', 'sin.stock_place_id', '=', 'tranfer.tranfer_stock_old')
                        ->join('stock_places as sout', 'sout.stock_place_id', '=', 'tranfer.tranfer_stock_new')
                        ->select(DB::raw("'โอน' as type"),DB::raw("'สำเร็จ' as status"),'tranfer.tranfer_code as code'
                        ,'tranfer.tranfer_stock_number as number','sin.stock_place_name as stock_in','sout.stock_place_name as stock_out'
                        ,DB::raw('sum(stocks.stock_number) as stock_number'),'tranfer.tranfer_date as date','products.product_code')
                        ->where('users.user_id', $user_id)
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
            $stock_cards[$i]['product_code'] = $key->product_code;
            $stock_cards[$i]['number'] = $key->number;
            $stock_cards[$i]['stock_in'] = $key->stock_in;
            $stock_cards[$i]['stock_out'] = $key->stock_out;
            $stock_cards[$i]['stock_number'] = $key->stock_number;
            $stock_cards[$i]['date'] = $this->datethaishort($key->date);
            $i++;
        }
        return view('dashboard._dashboard')->with(['sum_all'=> $sum_all,'month' => $str_month
        ,'purchase' => $purchase,'sell'=> $sell,'stocks_name'=> $stocks_name ,
        'stocks_number'=> $stocks_number,'stock_cards'=>$stock_cards]);

    }






    public function export(Request $request)
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
                        ,DB::raw('sum(stocks.stock_number) as stock_number'),'purchase.purchase_date as date','products.product_code')
                        ->where('users.user_id', $user_id)
                        ->groupBy('purchase.purchase_id');
        $query_sells = sell::join('users','sell.user_id','=','users.user_id')
                        ->join('so_product','sell.sell_id','=','so_product.sell_id')
                        ->join('stocks','so_product.product_id','=','stocks.product_id')
                        ->join('stock_places','sell.sell_stock','=','stock_places.stock_place_id')
                        ->join('products','so_product.product_id','=','products.product_id')
                        ->select(DB::raw("'ขาย' as type"),'sell.sell_status as status','sell.sell_code as code'
                        ,'so_product.product_number as number',DB::raw("'-' as stock_in"),'stock_places.stock_place_name as stock_out'
                        ,DB::raw('sum(stocks.stock_number) as stock_number'),'sell.sell_date as date','products.product_code')
                        ->where('users.user_id', $user_id)
                        ->groupBy('sell.sell_id');
        $query_adjusts = adjust::join('users','adjust.user_id','=','users.user_id')
                        ->join('adjust_stock','adjust.adjust_id','=','adjust_stock.adjust_id')
                        ->join('products','adjust.product_id','=','products.product_id')
                        ->join('stocks','products.product_id','=','stocks.product_id')
                        ->join('stock_places','stocks.stock_place_id','=','stock_places.stock_place_id')
                        ->select(DB::raw("'ปรับ' as type"),DB::raw("'สำเร็จ' as status"),'adjust.adjust_code as code'
                        ,'adjust_stock.adjust_stock_new as number',DB::raw("'-' as stock_in"),'stock_places.stock_place_name as stock_out'
                        ,DB::raw('sum(stocks.stock_number) as stock_number'),'adjust.adjust_date as date','products.product_code')
                        ->where('users.user_id', $user_id)
                        ->groupBy('adjust.adjust_id');
        $query_tranfers = tranfer::join('users','tranfer.user_id','=','users.user_id')
                        ->join('products','tranfer.product_id','=','products.product_id')
                        ->join('stocks','products.product_id','=','stocks.product_id')
                        ->join('stock_places as sin', 'sin.stock_place_id', '=', 'tranfer.tranfer_stock_old')
                        ->join('stock_places as sout', 'sout.stock_place_id', '=', 'tranfer.tranfer_stock_new')
                        ->select(DB::raw("'โอน' as type"),DB::raw("'สำเร็จ' as status"),'tranfer.tranfer_code as code'
                        ,'tranfer.tranfer_stock_number as number','sin.stock_place_name as stock_in','sout.stock_place_name as stock_out'
                        ,DB::raw('sum(stocks.stock_number) as stock_number'),'tranfer.tranfer_date as date','products.product_code')
                        ->where('users.user_id', $user_id)
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
                $stock_cards[$i]['product_code'] = $key->product_code;
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
            $header2 = array('ลำดับ', 'ประเภท', 'สภานะ', 'รายการเลขที่','รหัสสินค้า','จำนวน', 'จาก' , 'ไป','คงเหลือ','วันที่ทำรายการ');

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
                $worksheet->setCellValue(chr($col+3).($start2+$i),'รอคำสั่งซื้อ/ขาย');
            }elseif($key["status"] == '2'){
                $worksheet->setCellValue(chr($col+3).($start2+$i),'สำเร็จ');
            }elseif($key["status"] == '9'){
                $worksheet->setCellValue(chr($col+3).($start2+$i),'ยกเลิก');
            }elseif($key["status"] == '0'){
                $worksheet->setCellValue(chr($col+3).($start2+$i),'กำลังดำเนินการ');
            }else{
                $worksheet->setCellValue(chr($col+3).($start2+$i),'สำเร็จ');
            }
            $worksheet->setCellValue(chr($col+4).($start2+$i),$key["code"]);
            $worksheet->setCellValue(chr($col+5).($start2+$i),$key["product_code"]);
            $worksheet->setCellValue(chr($col+6).($start2+$i),$key["number"]);
            $worksheet->setCellValue(chr($col+7).($start2+$i),$key["stock_out"]);
            $worksheet->setCellValue(chr($col+8).($start2+$i),$key["stock_in"]);
            $worksheet->setCellValue(chr($col+9).($start2+$i),$key["stock_number"]);
            $worksheet->setCellValue(chr($col+10).($start2+$i),$key["date"]);
            $i++;
             }

             $worksheet->getStyle(chr($col+1).($start2).':'.chr($col+10).($start2+$i-1))
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
                ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('000000'));

            // Rename worksheet
            $spreadsheet->getActiveSheet()->setTitle('All_stock_card');

            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $spreadsheet->setActiveSheetIndex(0);

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('All_stock_card.xlsx');
            return response()->download('All_stock_card.xlsx');
        }
    }


    protected function monththaishort($strDate)
    {
    //   $strYear = date("Y",strtotime($strDate))+543;
    //   //$strYear = substr($strYear, -2);
  	// 	$strMonth= date("n",strtotime($strDate));
  	// 	$strDay= date("j",strtotime($strDate));
  	// 	$strHour= date("H",strtotime($strDate));
  	// 	$strMinute= date("i",strtotime($strDate));
  	// 	$strSeconds= date("s",strtotime($strDate));
      //$strMonthCut = Array("","มกราคม","กุมภาพันธ์","มีนาคม","เมษายน","พฤษภาคม","มิถุนายน","กรกฏาคม","สิงหาคม","กันยายน","ตุลาคม","พฤศจิกายน","ธันวาคม");
  		$strMonthCut = Array("","ม.ค.","ก.พ.","มี.ค.","เม.ย.","พ.ค.","มิ.ย.","ก.ค.","ส.ค.","ก.ย.","ต.ค.","พ.ย.","ธ.ค.");
  		$strMonthThai=$strMonthCut[$strDate];
        //return "$strDay $strMonthThai $strYear"; //, $strHour:$strMinute";
  		return "$strMonthThai"; //, $strHour:$strMinute";

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
