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


class EditproductController extends Controller
{
    public function index(Request $request,$product_id)
    {
        $user_id = '12';
        $products = array();$i=0;
        $query_products = products::join('users','users.user_id','=','products.user_id')
                        ->join('stocks','products.product_id','=','stocks.product_id')
                        ->join('stock_places','stocks.stock_place_id','=','stock_places.stock_place_id')
                        ->join('category','products.category_id','=','category.category_id')
                        ->join('product_file','products.product_id','=','product_file.product_id')
                        ->select('products.product_id','products.product_code','products.product_name',
                        'products.product_price_buy','products.product_price_sell','products.product_unit'
                        ,'products.product_volume','stocks.stock_id','stocks.stock_number','stocks.stock_place_id','stock_places.stock_place_name',
                        'product_file.product_file_server','product_file.product_file_id'
                        ,'products.category_id','category.category_name')
                        ->where('users.user_id', $user_id)
                        ->where('products.product_id', $product_id)
                        ->get();
        if(count($query_products)>0){
            foreach($query_products as $key){
                $products['product_id'] = $key->product_id;
                $products['product_code'] = $key->product_code;
                $products['product_name'] = $key->product_name;
                $products['product_price_buy'] = $key->product_price_buy;
                $products['product_price_sell'] = $key->product_price_sell;
                $products['product_unit'] = $key->product_unit;
                $products['product_volume'] = $key->product_volume;
                $products['stock_id'] = $key->stock_id;
                $products['stock_number'] = $key->stock_number;
                $products['stock_place_id'] = $key->stock_place_id;
                $products['stock_place_name'] = $key->stock_place_name;
                $products['category_id'] = $key->category_id;
                $products['category_name'] = $key->category_name;
                $products['product_file_id'] = $key->product_file_id;
                $products['product_file_server'] = '/assets/files/'.$user_id."/".$key->product_id.'/'.$key->product_file_server;
            }
            $stock_places = array();$i=0;
            $query_stock_places = stock_places::all();
            foreach($query_stock_places as $key){
                $stock_places[$i]['stock_place_id'] = $key->stock_place_id;
                $stock_places[$i]['stock_place_name'] = $key->stock_place_name;
                $i++;
            }
            $category = array();$i=0;
            $query_category = category::all();
            foreach($query_category as $key){
                $category[$i]['category_id'] = $key->category_id;
                $category[$i]['category_name'] = $key->category_name;
                $i++;
            }
            return view('products._editproduct')->with(['products' => $products ,'stock_places' => $stock_places, 'category' => $category]);
        }else{
            return view('others._notFound');
        }


    }



    public function edit(Request $request)
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
                    $update_product = products::where('product_id','=',$request->product_id)
                        ->update([
                        'product_code' => $request->product_code,
                        'product_name' => $request->product_name,
                        'product_price_buy' => $request->product_price_buy,
                        'product_price_sell' => $request->product_price_sell,
                        'product_unit' => $request->product_unit,
                        'product_volume' => $request->product_volume,
                        'category_id' => $request->category_id
                        ]);
                    if($request->hasFile('file')){    // ถ้ามีการอัพโหลดไฟล์
                        $file =$request->file('file');
                        $file_name = $file->getClientOriginalName();
                        $file_ext = strtolower($file->getClientOriginalExtension());
                        $file_server = strtoupper(uniqid('file_').'-'.time()).'.'.$file_ext;
                        $upload_storage = Storage::disk('public')->putFileAs('assets/files', $file,'/'.$user_id.'/'.$request->product_id.'/'.$file_server);
                        if($upload_storage){
                            product_file::where('product_id','=',$request->product_id)
                            ->update([
                                'product_file_name' => $file_name,
                                'product_file_server' => $file_server,
                                'product_file_ext' => $file_ext,
                            ]);
                        }else{
                            return view('products._editproduct')->with('alert' , 'เกิดข้อผิดพลาดในการอัพโหลดไฟล์' );
                        }
                    }
                    DB::commit();
                    return redirect('/products')->with('alert' , "แก้ไขข้อมูลสำเร็จ");
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
