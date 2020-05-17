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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Config;
use Mail;
use Storage;
use Validator;


class AddsellController extends Controller
{
    public function index(Request $request,$product_id=null)
    {
        $user_id = session('uid');
        $sell_code = array();
        $date = date("YmdHis",time());
        $today['today'] = date("Y-m-d",time());
        $sell_code['sell_code'] ='SO-'.$date;
        $stocks = array();$i=0;
        $query_stocks = stock_places::all();
        foreach($query_stocks as $key){
            $stocks[$i]['stock_place_id'] = $key->stock_place_id;
            $stocks[$i]['stock_place_code'] = $key->stock_place_id;
            $stocks[$i]['stock_place_name'] = $key->stock_place_name;
            $i++;
        }
        $products = array();$i=0;
        $query_products = products::join('users','users.user_id','=','products.user_id')
                        ->join('stocks','products.product_id','=','stocks.product_id')
                        ->join('product_file','products.product_id','=','product_file.product_id')
                        ->select('products.product_id','products.product_code','products.product_name',
                        'products.product_price_buy','products.product_price_sell','stocks.stock_id'
                        ,DB::raw('sum(stocks.stock_number) as stock_number'),'product_file.product_file_server')
                        ->where('users.user_id', $user_id)
                        ->where('products.product_status', '1')
                        ->groupBy('product_id')->get();
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
        if($product_id!=null){
            $get_product = array();
            $query_get_products = products::join('users','users.user_id','=','products.user_id')
                                  ->join('stocks','products.product_id','=','stocks.product_id')
                                  ->join('product_file','products.product_id','=','product_file.product_id')
                                  ->select('products.product_id','products.product_code','products.product_name',
                                  'products.product_price_buy','products.product_price_sell','stocks.stock_id'
                                  ,'stocks.stock_number','product_file.product_file_server')
                                  ->where('users.user_id', $user_id)
                                  ->where('products.product_id', $request->product_id)
                                  ->where('products.product_status', '1')
                                  ->get();
             foreach($query_get_products as $key){
                $get_product['product_id'] = $key->product_id;
                $get_product['product_code'] = $key->product_code;
                $get_product['product_name'] = $key->product_name;
                $get_product['product_price_buy'] = $key->product_price_buy;
                $get_product['product_price_sell'] = $key->product_price_sell;
                $get_product['stock_id'] = $key->stock_id;
                $get_product['stock_number'] = $key->stock_number;
                $get_product['product_file_server'] = '/assets/files/'.$user_id."/".$key->product_id.'/'.$key->product_file_server;
            }
            return view('sell._addsell')->with(['sell_code' => $sell_code,'today' =>$today, 'stocks' =>$stocks,'products' => $products,'get_product' => $get_product]);
        }

        return view('sell._addsell')->with(['sell_code' => $sell_code,'today' =>$today, 'stocks' =>$stocks,'products' => $products]);



    }


    public function store(Request $request)
    {
        $user_id = session('uid');
        //return $request->all();
        if($user_id == ''){
            return redirect('/');
        }else{
            if($request){
                DB::beginTransaction();
                try{
                    $create_sell= sell::create([
                        'user_id' => $user_id,
                        'sell_code' => $request->sell_code,
                        'sell_date' => $request->sell_date,
                        'sell_reference' => $request->sell_reference,
                        'sell_detail' => $request->sell_detail,
                        'sell_total' => $request->product_total,
                        'sell_stock' => $request->stock_place_id,
                        'sell_status' => $request->sell_status
                    ]);
                    $create_so_product= so_product::create([
                        'sell_id' => $create_sell['sell_id'],
                        'product_id' => $request->product_id,
                        'product_number' => $request->product_number,
                        'product_total' => $request->product_total,
                    ]);
                    if($request->sell_status == '1'){
                        $product_id =null;
                        $product_number =null;
                        $query_so_product = so_product::select('so_product.product_id','so_product.product_number')
                        ->where('so_product.sell_id',$create_sell['sell_id'])
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
                        ->where('users.user_id',$user_id)
                        ->get();
                        foreach($query_product_stock as $key){
                            $product_stock = $key->stock_number;
                        }
                        $data_new = $product_stock-$product_number;
                        $update_stock = stocks::where('product_id','=',$product_id)
                        ->where('stock_place_id','=',$request->stock_place_id)
                        ->update([
                            'stock_number' => $data_new,
                        ]);
                    }
                    DB::commit();
                    return redirect('/sells')->with('alert' , "เพิ่มข้อมูลสำเร็จ");
                } catch (\Exception $e) {
                    DB::rollback();
                    //return $e;
                    return view('sell._addsell')->with('alert' , "การเพิ่มข้อมูลผิดพลาด".$e );
                }
            }else{
                return view('sell._addsell')->with('alert' , "ชุดข้อมูลไม่ถูกต้อง" );
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



}
