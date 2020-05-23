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
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class AdminshippingController extends Controller
{
    public function index(Request $request,$sell_id)
    {
        $user_id = session('uid');
        $sells = array();
        $tracking_number = rand(0000000000,9999999999);
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
                        ,'stock_places.stock_place_name','stock_places.stock_place_id','users.user_id')
                        ->where('sell.sell_status','1')
                        ->where('sell.sell_id',$sell_id)
                        ->orderBy('sell.sell_date','ASC')
                        ->get();
        foreach($query_sells as $key){
            //$sells['order'] = $i+1;
            $sells['sell_id'] = $key->sell_id;
            $sells['sell_code'] = $key->sell_code;
            $sells['sell_date'] = $this->datethaishort($key->sell_date);
            $sells['product_id'] = $key->product_id;
            $sells['product_code'] = $key->product_code;
            $sells['product_name'] = $key->product_name;
            $sells['product_number'] = $key->product_number;
            $sells['product_total'] = $key->product_total;
            $sells['sell_total'] = $key->sell_total;
            $sells['sell_stock'] = $key->sell_stock;
            $sells['sell_status'] = $key->sell_status;
            $sells['customer_id'] = $key->customer_id;
            $sells['customer_name'] = $key->customer_name;
            $sells['customer_detail'] = $key->customer_detail;
            $sells['full_name'] = $key->full_name;
            $sells['stock_place_name'] = $key->stock_place_name;
            $sells['tracking_number'] = $tracking_number;
            $sells['stock_place_id'] = $key->stock_place_id;
            $sells['user_id'] = $key->user_id;
        }
        return view('admin._adminshipping')->with(['sells' => $sells]);
        //return view('admin._adminshipping');

    }

    public function shipping(Request $request)
    {
        $user_id = session('uid');
        //return $request->all();
        if($user_id == ''){
            return redirect('/');
        }else{
            if($request){
                $shipping_data =array (
                    'tracking' =>
                    array (
                      'slug' => 'kerry-logistics',
                      'tracking_number' => $request->tracking_number,
                      'title' => $request->sell_code,
                      'custom_fields' =>
                      array (
                        'product_name' => $request->product_name,
                        'product_price' => $request->sell_total,
                      ),
                    ),
                );
                $json_data = json_encode($shipping_data);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://api.aftership.com/v4/trackings");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
                curl_setopt($ch, CURLOPT_POST, true);
                $headers = array(
                    "Content-Type: application/json",
                    "aftership-api-key: 64cfcd3c-cc28-4448-b0a1-fb8873edcd03",
                    "Content-Type: text/plain"
                 );
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                $result = curl_exec($ch);
                $decode = json_decode($result);
                $data = $decode->data;
                $tracking  = $data->tracking;
                $shipment_id = $tracking->id;
                $tracking_number = $tracking->tracking_number;

                DB::beginTransaction();
                try{
                      $create_shipment= shipment::create([
                            'sell_id' => $request->sell_id,
                            'shipment_id' =>  $shipment_id,
                            'tracking_number' => $tracking_number,
                            'shipment_detail' => $request->shipment_detail,
                        ]);
                      $update_status = sell::where('sell_id','=',$request->sell_id)
                            ->update([
                                'sell_status' => 2
                            ]);
                      $product_id =null;
                      $product_number =null;
                      $query_so_product = so_product::select('so_product.product_id','so_product.product_number')
                      ->where('so_product.sell_id',$request->sell_id)
                      ->get();
                      foreach($query_so_product as $key){
                        $product_id = $key->product_id;
                        $product_number = $key->product_number;
                      }
                      $product_stock = null;
                      $query_product_stock = products::join('users','users.user_id','=','products.user_id')
                        ->join('stocks','products.product_id','=','stocks.product_id')
                        ->select('stocks.stock_number')
                        ->where('products.product_id',$product_id)
                        ->where('stocks.stock_place_id',$request->stock_place_id)
                        ->where('users.user_id',$request->user_id)
                        ->get();
                        foreach($query_product_stock as $key){
                            $product_stock = $key->stock_number;
                        }
                        $data_new = $product_stock-$product_number;
                        if($data_new < 0){
                            return redirect()->back()->with('alert' , "สินค้าไม่พอ" );
                        }
                        $update_stock = stocks::where('product_id','=',$product_id)
                        ->where('stock_place_id','=',$request->stock_place_id)
                        ->update([
                            'stock_number' => $data_new,
                        ]);
                    DB::commit();
                    return redirect('/admin/adminsell')->with('alert' , "เพิ่มข้อมูลสำเร็จ");
                } catch (\Exception $e) {
                    DB::rollback();
                    return $e;
                    return redirect()->back()->with('alert' , "การเพิ่มข้อมูลผิดพลาด".$e );
                }
            }else{
                return redirect()->back()->with('alert' , "ชุดข้อมูลไม่ถูกต้อง" );
            }
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
