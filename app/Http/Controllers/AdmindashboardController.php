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


class AdmindashboardController extends Controller
{
    public function index(Request $request)
    {
        $user_id = session('uid');
        $sells = array();$i=0;
        $query_sells = sell::join('users','sell.user_id','=','users.user_id')
                        ->join('so_product','sell.sell_id','=','so_product.sell_id')
                        ->join('products','so_product.product_id','=','products.product_id')
                        ->join('customer','sell.customer_id','=','customer.customer_id')
                        ->join('stock_places','sell.sell_stock','=','stock_places.stock_place_id')
                        ->select('sell.sell_id','sell.sell_code','sell.sell_date'
                        ,'products.product_code','products.product_id','sell.sell_stock'
                        ,'products.product_name','sell.sell_total','sell.sell_status'
                        ,'so_product.product_number','so_product.product_total','customer.customer_id'
                        ,'customer.customer_name','customer.customer_detail',DB::raw('CONCAT(users.first_name," ",users.last_name) AS full_name')
                        ,'stock_places.stock_place_name')
                        ->where('sell.sell_status','1')
                        ->orderBy('sell.sell_date','ASC')
                        ->get();
        $count_sell = count($query_sells);
        $purchases = array();$i=0;
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
                            ->where('purchase.purchase_status_tranfer', 1)
                            ->orderBy('purchase.purchase_date','ASC')
                            ->get();
        $count_purchase = count($query_purchases);

        $users = array();$i=0;
        $query_users = users::where('role','1')->get();
        foreach($query_users as $key){
            $users[$i]['order'] = $i+1;
            $users[$i]['first_name'] = $key->first_name;
            $users[$i]['last_name'] = $key->last_name;
            $users[$i]['email'] = $key->email;
            $users[$i]['tel'] = $key->tel;
            $users[$i]['created_at'] = $key->created_at;
            $i++;
        }

        return view('admin._admindashboard')->with(['count_sell'=> $count_sell
        ,'count_purchase' => $count_purchase,'users' => $users]);


    }




    public function export(Request $request)
    {
        $user_id = session('uid');
        if($user_id == ''){
            return redirect('/');
        }else{
            $users = array();$i=0;
            $query_users = users::where('role','1')->get();
            foreach($query_users as $key){
                $users[$i]['order'] = $i+1;
                $users[$i]['first_name'] = $key->first_name;
                $users[$i]['last_name'] = $key->last_name;
                $users[$i]['email'] = $key->email;
                $users[$i]['tel'] = $key->tel;
                $users[$i]['created_at'] = $key->created_at;
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
            $header2 = array('ลำดับ', 'ชื่อ', 'นามสกุล', 'อีเมล์','เบอร์โทรศัพท์','วันที่สร้าง');

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
            foreach($query_users as $key){
            $worksheet->setCellValue(chr($col+1).($start2+$i), $i);
            $worksheet->setCellValue(chr($col+2).($start2+$i),$key["first_name"]);
            $worksheet->setCellValue(chr($col+3).($start2+$i),$key["last_name"]);
            $worksheet->setCellValue(chr($col+4).($start2+$i),$key["email"]);
            $worksheet->setCellValue(chr($col+5).($start2+$i),$key["tel"]);
            $worksheet->setCellValue(chr($col+6).($start2+$i),$key["created_at"]);
            $i++;
             }

             $worksheet->getStyle(chr($col+1).($start2).':'.chr($col+6).($start2+$i-1))
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
                ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('000000'));

            // Rename worksheet
            $spreadsheet->getActiveSheet()->setTitle('Users');

            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $spreadsheet->setActiveSheetIndex(0);

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('Users.xlsx');
            return response()->download('Users.xlsx');
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
