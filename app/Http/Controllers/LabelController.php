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
use App\Model\customer;
use App\Model\shipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Config;
use Mail;
use Storage;
use Validator;
use PDF;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class LabelController extends Controller
{
    public function print(Request $request)
    {
        $user_id = session('uid');
        $prints = array();
        $prints['customer_name'] = $request->customer_name;
        $prints['customer_detail'] = $request->customer_detail;
        $prints['product_number'] = $request->product_number;

        $pdf = PDF::loadView('label',['prints'=>$prints]);
        return $pdf->stream();

    }




    public function export(Request $request)
    {
        $user_id = session('uid');
        if($user_id == ''){
            return redirect('/');
        }else{
            $sells = array();$i=0;
            $query_sells = sell::join('users','sell.user_id','=','users.user_id')
                        ->join('so_product','sell.sell_id','=','so_product.sell_id')
                        ->join('products','so_product.product_id','=','products.product_id')
                        ->select('sell.sell_id','sell.sell_code','sell.sell_date'
                        ,'products.product_code','products.product_id','sell.sell_stock'
                        ,'products.product_name','sell.sell_total','sell.sell_status'
                        ,'so_product.product_number','so_product.product_total')
                        ->where('users.user_id', $user_id)
                        ->get();
            foreach($query_sells as $key){
                $sells[$i]['order'] = $i+1;
                $sells[$i]['sell_id'] = $key->sell_id;
                $sells[$i]['sell_code'] = $key->sell_code;
                $sells[$i]['sell_date'] = $this->datethaishort($key->sell_date);
                $sells[$i]['product_id'] = $key->product_id;
                $sells[$i]['product_code'] = $key->product_code;
                $sells[$i]['product_name'] = $key->product_name;
                $sells[$i]['product_number'] = $key->product_number;
                $sells[$i]['product_total'] = $key->product_total;
                $sells[$i]['sell_total'] = $key->sell_total;
                $sells[$i]['sell_stock'] = $key->sell_stock;
                $sells[$i]['sell_status'] = $key->sell_status;
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
            foreach($query_sells as $key){
            $worksheet->setCellValue(chr($col+1).($start2+$i), $i);
            $worksheet->setCellValue(chr($col+2).($start2+$i),'รายการขาย');
            $worksheet->setCellValue(chr($col+3).($start2+$i),$key["sell_code"]);
            $worksheet->setCellValue(chr($col+4).($start2+$i),$key["product_code"]);
            $worksheet->setCellValue(chr($col+5).($start2+$i),$key["product_name"]);
            $worksheet->setCellValue(chr($col+6).($start2+$i),$key["product_number"]);
            $worksheet->setCellValue(chr($col+7).($start2+$i),$key["sell_total"]);
            if($key["sell_status"] == '1'){
                $worksheet->setCellValue(chr($col+8).($start2+$i),'สำเร็จ');
            }elseif($key["sell_status"] == '9'){
                $worksheet->setCellValue(chr($col+8).($start2+$i),'ยกเลิก');
            }else{
                $worksheet->setCellValue(chr($col+8).($start2+$i),'รอโอนสินค้า');
            }
            $worksheet->setCellValue(chr($col+9).($start2+$i),$this->datethaishort($key["sell_date"]));
            $i++;
             }

             $worksheet->getStyle(chr($col+1).($start2).':'.chr($col+9).($start2+$i-1))
                ->getBorders()
                ->getAllBorders()
                ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN)
                ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('000000'));

            // Rename worksheet
            $spreadsheet->getActiveSheet()->setTitle('Sells');

            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $spreadsheet->setActiveSheetIndex(0);

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('Sells.xlsx');
            return response()->download('Sells.xlsx');


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
