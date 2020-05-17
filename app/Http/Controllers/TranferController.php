<?php

namespace App\Http\Controllers;
use App\Model\users;
use App\Model\products;
use App\Model\product_file;
use App\Model\stocks;
use App\Model\stock_places;
use App\Model\category;
use App\Model\tranfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Config;
use Mail;
use Storage;
use Validator;


class TranferController extends Controller
{
    public function index(Request $request,$product_id)
    {
        $user_id = session('uid');
        $tranfer_code = array();
        $date = date("YmdHis",time());
        $today['today'] = date("Y-m-d",time());
        $tranfer_code['tranfer_code'] ='TF-'.$date;
        $stocks = array();$i=0;
        $query_stocks = stock_places::select('stock_place_id','stock_place_code','stock_place_name')->get();
        foreach($query_stocks as $key){
            $stocks[$i]['order'] = $i+1;
            $stocks[$i]['stock_place_id'] = $key->stock_place_id;
            $stocks[$i]['stock_place_code'] = $key->stock_place_code;
            $stocks[$i]['stock_place_name'] = $key->stock_place_name;
            $i++;
        }
        $product = array();
        $query_product = products::select('product_id','product_code','product_name')
                                ->where('product_id',$product_id)
                                ->get();
        foreach($query_product as $key){
            $product['product_id'] = $key->product_id;
            $product['product_code'] = $key->product_code;
            $product['product_name'] = $key->product_name;
        }
        return view('tranfer._tranfer')->with(['tranfer_code' => $tranfer_code,'stocks' =>$stocks,'today' =>$today,'product'=> $product ]);
    }

    public function tranfer(Request $request)
    {
        $user_id = session('uid');
        if($user_id == ''){
            return redirect('/');
        }else{
            if($request){
                if($request->tranfer_stock_old  == 0||$request->tranfer_stock_new == 0 ){
                    return redirect()->back()->with('alert' , "ชุดข้อมูลไม่ถูกต้อง" );
                }else{
                    $product_stock_old =null;
                    $query_product_stock = products::join('users','users.user_id','=','products.user_id')
                                                ->join('stocks','products.product_id','=','stocks.product_id')
                                                ->select('stocks.stock_number')
                                                ->where('products.product_id',$request->product_id)
                                                ->where('stocks.stock_place_id',$request->tranfer_stock_old)
                                                ->where('users.user_id',$user_id)
                                                ->get();
                    foreach($query_product_stock as $key){
                        $product_stock_old = $key->stock_number;
                    }
                    if($product_stock_old == 0){
                        return redirect()->back()->with('alert' , "จำนวนสินค้าไม่ถูกต้อง" );
                    }else{
                        DB::beginTransaction();
                        try{
                            $create_tranfer= tranfer::create([
                                'tranfer_code' => $request->tranfer_code,
                                'product_id' => $request->product_id,
                                'user_id' => $user_id,
                                'tranfer_date' => $request->tranfer_date,
                                'tranfer_stock_old' => $request->tranfer_stock_old,
                                'tranfer_stock_new' => $request->tranfer_stock_new,
                                'tranfer_stock_number' => $request->tranfer_stock_number,
                            ]);
                            $data_old = $product_stock_old-$request->tranfer_stock_number;
                            if($data_old < 0){
                                return redirect()->back()->with('alert' , "จำนวนสินค้าไม่ถูกต้อง" );
                            }
                            $product_stock_new =array();
                            $query_product_stock_new = products::join('users','users.user_id','=','products.user_id')
                                                ->join('stocks','products.product_id','=','stocks.product_id')
                                                ->select('stocks.stock_number')
                                                ->where('products.product_id',$request->product_id)
                                                ->where('stocks.stock_place_id',$request->tranfer_stock_new)
                                                ->where('users.user_id',$user_id)
                                                ->get();
                            foreach($query_product_stock_new as $key){
                                $product_stock_new = $key->stock_number;
                            }
                            $data_new = $product_stock_new+$request->tranfer_stock_number;
                            $update_stock_old = stocks::where('product_id','=',$request->product_id)
                                                ->where('stock_place_id','=',$request->tranfer_stock_old)
                                                ->update([
                                                    'stock_number' => $data_old,
                                                ]);
                            $update_stock_new = stocks::where('product_id','=',$request->product_id)
                                                ->where('stock_place_id','=',$request->tranfer_stock_new)
                                                ->update([
                                                    'stock_number' => $data_new,
                                                ]);
                            DB::commit();
                            return redirect('/products')->with('alert' , "เพิ่มข้อมูลสำเร็จ");
                        } catch (\Exception $e) {
                            DB::rollback();
                            return $e;
                            return redirect()->back()->with('alert' , "การเพิ่มข้อมูลผิดพลาด".$e );
                        }
                    }

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



}
