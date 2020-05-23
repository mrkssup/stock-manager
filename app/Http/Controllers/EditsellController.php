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


class EditsellController extends Controller
{
    public function index(Request $request,$sell_id)
    {
        $user_id = session('uid');
        $sells = array();
        $query_sell = sell::join('users','sell.user_id','=','users.user_id')
                        ->join('so_product','sell.sell_id','=','so_product.sell_id')
                        ->join('stock_places','sell.sell_stock','=','stock_places.stock_place_id')
                        ->join('products','so_product.product_id','=','products.product_id')
                        ->join('customer','sell.customer_id','=','customer.customer_id')
                        ->select('sell.sell_id','sell.sell_code','sell.sell_date'
                        ,'products.product_code','products.product_id','sell.sell_stock','stock_places.stock_place_name'
                        ,'products.product_name','products.product_price_sell','sell.sell_total','sell.sell_status'
                        ,'sell_reference','so_product.so_product_id','so_product.product_number','so_product.product_total'
                        ,'customer.customer_id','customer.customer_name','customer.customer_detail',DB::raw('CONCAT(users.first_name," ",users.last_name) AS fullname'))
                        ->where('users.user_id', $user_id)
                        ->where('sell.sell_id', $sell_id)
                        ->get();
        if(count($query_sell)>0){
            foreach($query_sell as $key){
                $sells['sell_id'] = $key->sell_id;
                $sells['sell_code'] = $key->sell_code;
                $sells['sell_date'] = $key->sell_date;
                $sells['sell_user'] = $key->fullname;
                $sells['sell_reference'] = $key->sell_reference;
                $sells['customer_id'] = $key->customer_id;
                $sells['customer_name'] = $key->customer_name;
                $sells['customer_detail'] = $key->customer_detail;
                $sells['sell_stock'] = $key->sell_stock;
                $sells['product_id'] = $key->product_id;
                $sells['so_product_id'] = $key->so_product_id;
                $sells['product_code'] = $key->product_code;
                $sells['product_name'] = $key->product_name;
                $sells['product_number'] = $key->product_number;
                $sells['product_price_sell'] = $key->product_price_sell;
                $sells['product_total'] = $key->product_total;
                $sells['sell_total'] = $key->sell_total;
                $sells['stock_place_name'] = $key->stock_place_name;
                $sells['sell_status'] = $key->sell_status;
            }
            $stock_places = array();$i=0;
            $query_stock_places = stock_places::all();
            foreach($query_stock_places as $key){
                $stock_places[$i]['stock_place_id'] = $key->stock_place_id;
                $stock_places[$i]['stock_place_name'] = $key->stock_place_name;
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
                        ->where('products.product_status', '1')->groupBy('product_id')->get();
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
            $customer = array();$i=0;
            $query_customer = customer::all();
                foreach($query_customer as $key){
                    $customer[$i]['customer_id'] = $key->customer_id;
                    $customer[$i]['customer_name'] = $key->customer_name;
                    $customer[$i]['customer_detail'] = $key->customer_detail;
                    $i++;
                }
            return view('sell._editsell')->with(['sells' => $sells,'stock_places' => $stock_places
            ,'products' => $products,'customers' => $customer]);
        }else{
            return view('others._notFound');
        }


    }


    public function edit(Request $request)
    {
        $user_id = session('uid');
        //return $request->all();
        if($user_id == ''){
            return redirect('/');
        }else{
            if($request){
                $query_check_sell = sell::where('sell_id',$request->sell_id)->get();
                if(!is_null($query_check_sell)){
                    DB::beginTransaction();
                    try{
                        $check_customer = sell::select('sell.customer_id')->where('sell_id','=',$request->sell_id)->first();
                        $old_customer =  $check_customer->customer_id;
                        if($old_customer == $request->customer_id){
                            $update_sell = sell::where('sell_id','=',$request->sell_id)
                            ->update([
                                'sell_date' => $request->sell_date,
                                'sell_reference' => $request->sell_reference,
                                'customer_id' => $request->customer_id,
                                'sell_total' => $request->product_total,
                                'sell_stock' => $request->stock_place_id,
                                'sell_status' => 0
                            ]);
                            $update_so_product = so_product::where('so_product_id','=',$request->so_product_id)
                            ->update([
                                'product_id' => $request->product_id,
                                'product_number' => $request->product_number,
                                'product_total' => $request->product_total,
                            ]);
                            $update_customer = customer::where('customer_id','=',$request->customer_id)
                            ->update([
                                'customer_name' => $request->customer_name,
                                'customer_detail' => $request->customer_detail,
                            ]);
                        }else{
                            $update_sell = sell::where('sell_id','=',$request->sell_id)
                            ->update([
                                'sell_date' => $request->sell_date,
                                'sell_reference' => $request->sell_reference,
                                'customer_id' => $request->customer_id,
                                'sell_total' => $request->product_total,
                                'sell_stock' => $request->stock_place_id,
                                'sell_status' => 0
                            ]);
                            $update_so_product = so_product::where('so_product_id','=',$request->so_product_id)
                            ->update([
                                'product_id' => $request->product_id,
                                'product_number' => $request->product_number,
                                'product_total' => $request->product_total,
                            ]);
                            $update_customer = customer::where('customer_id','=',$request->customer_id)
                            ->update([
                                'customer_name' => $request->customer_name,
                                'customer_detail' => $request->customer_detail,
                            ]);

                        }
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
                            ->where('users.user_id',$user_id)
                            ->get();
                        foreach($query_product_stock as $key){
                            $product_stock = $key->stock_number;
                        }
                        $data_new = $product_stock-$product_number;
                        if($data_new < 0){
                            return redirect('/editsell/'.$request->sell_id)->with('alert' , "สินค้าคงเหลือในคลังที่เลือกไม่พอ" );
                        }
                    DB::commit();
                    return redirect('/sells')->with('alert' , "แก้ไขข้อมูลสำเร็จ");
                    } catch (\Exception $e) {
                        DB::rollback();
                        return $e;
                        return view('sell._sells')->with('alert' , "การเพิ่มข้อมูลผิดพลาด".$e );
                    }
                }else{
                    return view('others._notFound');
                }
            }else{
                return view('sell._editsell')->with('alert' , "ชุดข้อมูลไม่ถูกต้อง" );
            }
        }
    }


    public function deleteimage(Request $request,$product_file_id)
    {
        $user_id = '12';
        if($user_id == ''){
            return redirect('/');
        }else{
            if($request){
                $query_check_file = product_file::where('product_file_id',$product_file_id)->get();
                if(!is_null($query_check_file)){
                    $product_id = null;
                    $product_file_server = null;
                    foreach($query_check_file as $key){
                        $product_id = $key->product_id;
                        $product_file_server = $key->product_file_server;
                    }

                    $delete_storage = Storage::disk('public')->delete('assets/files/'.$user_id.'/'.$product_id.'/'.$product_file_server);
                    if($delete_storage){
                    $copy_storage = Storage::disk('public')->copy('assets/files/default.png','assets/files/'.$user_id.'/'.$product_id.'/default.png');
                        DB::beginTransaction();
                        try{
                            $update_default = product_file::where('product_file_id','=',$product_file_id)
                                ->update([
                                    'product_file_name' => 'default.png',
                                    'product_file_server' => 'default.png',
                                    'product_file_ext' => 'png',
                                    'product_id' => $product_id,
                            ]);
                        DB::commit();
                        return redirect('/editproduct/'.$product_id)->with('alert' , "แก้ไขข้อมูลสำเร็จ");
                    } catch (\Exception $e) {
                        DB::rollback();
                        return $e;
                        return view('products._editproduct')->with('alert' , "การเพิ่มข้อมูลผิดพลาด".$e );
                    }
                    }else{
                        return view('products._editproduct')->with('alert' , "เกิดข้อผิดพลาด" );
                    }
                }else{
                    return view('others._notFound');
                }
            }else{
                return view('products._editproduct')->with('alert' , "ชุดข้อมูลไม่ถูกต้อง" );
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
