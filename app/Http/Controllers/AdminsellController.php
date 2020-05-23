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


class AdminsellController extends Controller
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
            $sells[$i]['customer'] = $key->sell_status;
            $sells[$i]['sell_status'] = $key->sell_status;
            $sells[$i]['customer_id'] = $key->customer_id;
            $sells[$i]['customer_name'] = $key->customer_name;
            $sells[$i]['customer_detail'] = $key->customer_detail;
            $sells[$i]['full_name'] = $key->full_name;
            $sells[$i]['stock_place_name'] = $key->stock_place_name;
            $i++;
        }
        return view('admin._adminsell')->with(['sells' => $sells]);
        //return view('admin._adminsell');

    }

    public function search(Request $request)
    {
        $user_id = session('uid');
        $search = $request->search;
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
                        ->where('sell.sell_status','1');
        $query_sells = $query_sells->where(function($query) use ($search)
                        {
                            $query->where('sell.sell_code', 'LIKE' , '%'.$search.'%')
                                  ->orWhere('products.product_code', 'LIKE' , '%'.$search.'%')
                                  ->orWhere('products.product_name', 'LIKE' , '%'.$search.'%');
                        });
        $query_sells= $query_sells->orderBy('sell.sell_date','ASC')
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
            $sells[$i]['customer'] = $key->sell_status;
            $sells[$i]['sell_status'] = $key->sell_status;
            $sells[$i]['customer_id'] = $key->customer_id;
            $sells[$i]['customer_name'] = $key->customer_name;
            $sells[$i]['customer_detail'] = $key->customer_detail;
            $sells[$i]['full_name'] = $key->full_name;
            $sells[$i]['stock_place_name'] = $key->stock_place_name;
            $i++;
        }
        return view('admin._adminsell')->with(['sells' => $sells]);
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
