<?php

namespace App\Http\Controllers;
use App\Model\users;
use App\Model\products;
use App\Model\product_file;
use App\Model\stocks;
use App\Model\stock_places;
use App\Model\category;
use App\Model\adjust;
use App\Model\adjust_stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Config;
use Mail;
use Storage;
use Validator;


class NotifyController extends Controller
{
    public static function notify()
    {
        $user_id = session('uid');
        $products = array();$i=0;
        $detect = 10;
        $query_products = products::join('users','users.user_id','=','products.user_id')
                        ->join('stocks','products.product_id','=','stocks.product_id')
                        ->select('products.product_id','products.product_code','products.product_name'
                        ,DB::raw('sum(stocks.stock_number) as stock_number'))
                        ->where('products.product_status', '1')
                        ->where('users.user_id', $user_id)
                        ->groupBy('products.product_id')
                        ->get();
        foreach($query_products as $key){
            if($key->stock_number < $detect ){
                $products[$i]['data'] = 'รหัสสินค้า '.$key->product_code.'('.$key->product_name.')'.$key->stock_number.' สินค้าเหลือน้อยแล้ว!!';
                $i++;
            }
        }
        return  $products;
    }

    public static function count()
    {
        $user_id = session('uid');
        $products = array();$i=0;
        $detect = 10;
        $query_products = products::join('users','users.user_id','=','products.user_id')
                        ->join('stocks','products.product_id','=','stocks.product_id')
                        ->select('products.product_id','products.product_code','products.product_name'
                        ,DB::raw('sum(stocks.stock_number) as stock_number'))
                        ->where('products.product_status', '1')
                        ->where('users.user_id', $user_id)
                        ->groupBy('products.product_id')
                        ->get();
        foreach($query_products as $key){
            if($key->stock_number < $detect ){
                $products[$i]['data'] = 'รหัสสินค้า '.$key->product_code.'('.$key->product_name.')'.$key->stock_number.' สินค้าเหลือน้อยแล้ว!!';
                $i++;
            }
        }
        $count = count($products);
        return  $count;
    }









    protected function get_host()
    {
      $domain = url('/');
      $info = parse_url($domain);
      $host = $info['host'];
      return $host;
    }



}
