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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Config;
use Mail;
use Storage;
use Validator;


class PurchasedetailController extends Controller
{
    public function index(Request $request,$purchase_id)
    {
        $user_id = session('uid');
        $purchases = array();
        $query_purchases = purchase::join('users','purchase.user_id','=','users.user_id')
                        ->join('po_product','purchase.purchase_id','=','po_product.purchase_id')
                        ->join('stock_places','purchase.purchase_stock','=','stock_places.stock_place_id')
                        ->join('products','po_product.product_id','=','products.product_id')
                        ->join('customer','purchase.customer_id','=','customer.customer_id')
                        ->select('purchase.purchase_id','purchase.purchase_code','purchase.purchase_date'
                        ,'products.product_code','products.product_id','purchase.purchase_stock','stock_places.stock_place_name'
                        ,'products.product_name','purchase.purchase_total','purchase.purchase_reference','purchase.purchase_status_tranfer'
                        ,'po_product.product_number','po_product.product_total'
                        ,'purchase.customer_id','customer.customer_name','customer.customer_detail'
                        ,DB::raw('CONCAT(users.first_name," ",users.last_name) AS fullname'))
                        ->where('users.user_id', $user_id)
                        ->where('purchase.purchase_id', $purchase_id)
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
            $purchases['purchase_total'] = $key->purchase_total;
            $purchases['stock_place_name'] = $key->stock_place_name;
            $purchases['purchase_status_tranfer'] = $key->purchase_status_tranfer;
        }
        return view('purchases._purchasedetail')->with(['purchases' => $purchases]);
    }


    public function store(Request $request)
    {
        //return $request->all();
        $user_id = '12';
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
