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


class EditpurchaseController extends Controller
{
    public function index(Request $request,$purchase_id)
    {
        $user_id = session('uid');
        $purchases = array();
        $query_purchases = purchase::join('users','purchase.user_id','=','users.user_id')
                        ->join('po_product','purchase.purchase_id','=','po_product.purchase_id')
                        ->join('stock_places','purchase.purchase_stock','=','stock_places.stock_place_id')
                        ->join('products','po_product.product_id','=','products.product_id')
                        ->select('purchase.purchase_id','purchase.purchase_code','purchase.purchase_date'
                        ,'products.product_code','products.product_id','purchase.purchase_stock','stock_places.stock_place_name'
                        ,'products.product_name','products.product_price_buy','purchase.purchase_total','purchase.purchase_status_tranfer'
                        ,'purchase_reference','po_product.po_product_id','po_product.product_number','po_product.product_total'
                        ,'purchase.purchase_detail',DB::raw('CONCAT(users.first_name," ",users.last_name) AS fullname'))
                        ->where('users.user_id', $user_id)
                        ->where('purchase.purchase_id', $purchase_id)
                        ->get();
        if(count($query_purchases)>0){
            foreach($query_purchases as $key){
                $purchases['purchase_id'] = $key->purchase_id;
                $purchases['purchase_code'] = $key->purchase_code;
                $purchases['purchase_date'] = $key->purchase_date;
                $purchases['purchase_user'] = $key->fullname;
                $purchases['purchase_reference'] = $key->purchase_reference;
                $purchases['purchase_detail'] = $key->purchase_detail;
                $purchases['purchase_stock'] = $key->purchase_stock;
                $purchases['product_id'] = $key->product_id;
                $purchases['po_product_id'] = $key->po_product_id;
                $purchases['product_code'] = $key->product_code;
                $purchases['product_name'] = $key->product_name;
                $purchases['product_number'] = $key->product_number;
                $purchases['product_price_buy'] = $key->product_price_buy;
                $purchases['product_total'] = $key->product_total;
                $purchases['purchase_total'] = $key->purchase_total;
                $purchases['stock_place_name'] = $key->stock_place_name;
                $purchases['purchase_status_tranfer'] = $key->purchase_status_tranfer;
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
            return view('purchases._editpurchase')->with(['purchases' => $purchases,'stock_places' => $stock_places,'products' => $products]);
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
                $query_check_purchase = purchase::where('purchase_id',$request->purchase_id)->get();
                if(!is_null($query_check_purchase)){
                    DB::beginTransaction();
                try{
                    $update_purchase = purchase::where('purchase_id','=',$request->purchase_id)
                        ->update([
                            'purchase_date' => $request->purchase_date,
                            'purchase_reference' => $request->purchase_reference,
                            'purchase_detail' => $request->purchase_detail,
                            'purchase_total' => $request->product_total,
                            'purchase_stock' => $request->stock_place_id,
                            'purchase_status_tranfer' => $request->purchase_status_tranfer
                        ]);
                    $update_po_product = po_product::where('po_product_id','=',$request->po_product_id)
                        ->update([
                            'product_id' => $request->product_id,
                            'product_number' => $request->product_number,
                            'product_total' => $request->product_total,
                        ]);
                    if($request->purchase_status_tranfer == '1'){
                        $product_id =null;
                        $product_number =null;
                        $query_po_product = po_product::select('po_product.product_id','po_product.product_number')
                        ->where('po_product.purchase_id',$request->purchase_id)
                        ->get();

                        foreach($query_po_product as $key){
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
                        $data_new = $product_stock+$product_number;
                        $update_stock = stocks::where('product_id','=',$product_id)
                            ->where('stock_place_id','=',$request->stock_place_id)
                            ->update([
                                'stock_number' => $data_new,
                            ]);
                    }
                    DB::commit();
                    return redirect('/purchases')->with('alert' , "แก้ไขข้อมูลสำเร็จ");
                    } catch (\Exception $e) {
                        DB::rollback();
                        return $e;
                        return view('purchases._purchases')->with('alert' , "การเพิ่มข้อมูลผิดพลาด".$e );
                    }
                }else{
                    return view('others._notFound');
                }
            }else{
                return view('products._editproduct')->with('alert' , "ชุดข้อมูลไม่ถูกต้อง" );
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
