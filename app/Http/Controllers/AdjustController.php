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


class AdjustController extends Controller
{
    public function index(Request $request,$product_id)
    {
        $user_id = session('uid');
        $adjust_code = array();
        $date = date("YmdHis",time());
        $today['today'] = date("Y-m-d",time());
        $adjust_code['adjust_code'] ='AD-'.$date;
        $stocks = array();$i=0;
        $query_stocks = stock_places::select('stock_place_id','stock_place_code','stock_place_name')->get();
        foreach($query_stocks as $key){
            $stocks[$i]['order'] = $i+1;
            $stocks[$i]['stock_place_id'] = $key->stock_place_id;
            $stocks[$i]['stock_place_code'] = $key->stock_place_code;
            $stocks[$i]['stock_place_name'] = $key->stock_place_name;
            $stocks[$i]['stock_number'] = $this->product_stock($product_id);
            $i++;
        }
        $product = array();
        $query_product= products::join('users','users.user_id','=','products.user_id')
                        ->select('products.product_id','products.product_code','products.product_name')
                        ->where('products.product_id', $product_id)
                        ->where('users.user_id', $user_id)
                        ->get();
        foreach($query_product as $key){
            $product['product_id'] = $key->product_id;
            $product['product_code'] = $key->product_code;
            $product['product_name'] = $key->product_name;
        }
        return view('adjust._adjust')->with(['adjust_code' => $adjust_code,'stocks' =>$stocks,'today' =>$today,'product' =>$product ]);
    }


    public function adjust(Request $request)
    {
        $user_id = session('uid');
        if($user_id == ''){
            return redirect('/');
        }else{
            if($request){
                $adjust_stock = $request->adjust_stock;
                $adjust_stock_old = $request->adjust_stock_old;
                $adjust_stock_new = $request->adjust_stock_new;
                DB::beginTransaction();
                try{
                            $create_adjust= adjust::create([
                                'adjust_code' => $request->adjust_code,
                                'product_id' => $request->product_id,
                                'user_id' => $user_id,
                                'adjust_date' => $request->adjust_date,
                            ]);
                            for($i=0;$i<count($adjust_stock);$i++){
                                if($adjust_stock_new[$i]!=null){
                                $create_adjust_stock= adjust_stock::create([
                                    'adjust_id' => $create_adjust['adjust_id'],
                                    'stock_place_id' => $adjust_stock[$i],
                                    'adjust_stock_old' => $adjust_stock_old[$i],
                                    'adjust_stock_new' => $adjust_stock_new[$i]
                                ]);
                            $update_stock = stocks::where('product_id','=',$request->product_id)
                                                ->where('stock_place_id','=',$adjust_stock[$i])
                                                ->update([
                                                    'stock_number' => $adjust_stock_new[$i]
                                                ]);
                            }
                    }
                    DB::commit();
                    return redirect('/products')->with('alert' , "เพิ่มข้อมูลสำเร็จ");
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

    protected function product_stock($product_id)
    {
        $product_stock = array();$i=0;
        $query_product_stock= products::join('users','users.user_id','=','products.user_id')
                        ->join('stocks','products.product_id','=','stocks.product_id')
                        ->select('products.product_id','products.product_code','products.product_name','stocks.stock_number','stocks.stock_place_id')
                        ->where('stocks.product_id', $product_id)
                        ->get();
        foreach($query_product_stock as $key){
            $product_stock[$i]['product_id'] = $key->product_id;
            $product_stock[$i]['product_code'] = $key->product_code;
            $product_stock[$i]['product_name'] = $key->product_name;
            $product_stock[$i]['stock_number'] = $key->stock_number;
            $product_stock[$i]['stock_place_id'] = $key->stock_place_id;
            $i++;
        }
        return $product_stock;
    }






    protected function get_host()
    {
      $domain = url('/');
      $info = parse_url($domain);
      $host = $info['host'];
      return $host;
    }



}
