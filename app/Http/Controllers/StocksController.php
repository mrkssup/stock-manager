<?php

namespace App\Http\Controllers;
use App\Model\users;
use App\Model\products;
use App\Model\product_file;
use App\Model\stocks;
use App\Model\stock_places;
use App\Model\category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Config;
use Mail;
use Storage;
use Validator;


class StocksController extends Controller
{
    public function index(Request $request)
    {
        $user_id = session('uid');
        $stocks = array();$i=0;
        $query_stocks = stock_places::join('stocks','stock_places.stock_place_id','=','stocks.stock_place_id')
                                    ->join('products','stocks.product_id','=','products.product_id')
                                    ->select('stock_places.stock_place_id','stock_places.stock_place_code'
                                    ,'stock_places.stock_place_name',DB::raw('sum(stocks.stock_number) as stock_number'))
                                    ->where('products.user_id', $user_id)
                                    ->groupBy('stock_places.stock_place_id')
                                    ->get();
        foreach($query_stocks as $key){
            $stocks[$i]['order'] = $i+1;
            $stocks[$i]['stock_place_id'] = $key->stock_place_id;
            $stocks[$i]['stock_place_code'] = $key->stock_place_code;
            $stocks[$i]['stock_place_name'] = $key->stock_place_name;
            $stocks[$i]['stock_number'] = $key->stock_number;
            $i++;
        }
        return view('stocks._stocks')->with(['stocks' => $stocks]);
    }


    public function search(Request $request)
    {
        $user_id = '12';
        $search = $request->search;
        $stocks = array();$i=0;
        $query_stocks = stock_places:: select('stock_place_id','stock_place_code','stock_place_name');
        $query_stocks = $query_stocks->where(function($query) use ($search)
                        {
                            $query->where('stock_place_code', 'LIKE' , '%'.$search.'%')
                                  ->orWhere('stock_place_name', 'LIKE' , '%'.$search.'%');
                        });
        $query_stocks = $query_stocks->get();
        foreach($query_stocks as $key){
            $stocks[$i]['order'] = $i+1;
            $stocks[$i]['stock_place_id'] = $key->stock_place_id;
            $stocks[$i]['stock_place_code'] = $key->stock_place_code;
            $stocks[$i]['stock_place_name'] = $key->stock_place_name;
            $i++;
        }
        return view('stocks._stocks')->with(['stocks' => $stocks]);

    }


    public function delete(Request $request)
    {
        $user_id = '12';
        if($user_id == ''){
            return redirect('/');
        }else{
            if($request){
                $query_check_product = products::where('product_id',$request->product_id)->get();
                if(!is_null($query_check_product)){
                    DB::beginTransaction();
                try{
                    $delete_product = products::where('product_id','=',$request->product_id)
                        ->update([
                        'product_status' => '99',
                        ]);
                    DB::commit();
                    return redirect('/products');
                    } catch (\Exception $e) {
                        DB::rollback();
                        return $e;
                        return view('products._products')->with('alert' , "การเพิ่มข้อมูลผิดพลาด".$e );
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
