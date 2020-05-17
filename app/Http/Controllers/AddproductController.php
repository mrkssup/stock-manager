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


class AddproductController extends Controller
{
    public function index(Request $request)
    {
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
        return view('products._addproduct')->with(['stock_places' => $stock_places, 'category' => $category] );

    }


    public function store(Request $request)
    {
        $user_id = session('uid');
        if($user_id == ''){
            return redirect('/');
        }else{
            if($request){
                $query_product_code = products::where('product_code',$request->product_code)->get();
                if(count($query_product_code)>0){
                    return view('products._addproduct')->with('alert' , 'รหัสสินค้าซ้ำกัน' );
                }
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
                        $file_server = strtoupper(uniqid('file_').'-'.time()).'.'.$file_ext;
                        $upload_storage = Storage::disk('public')->putFileAs('assets/files', $file,'/'.$user_id.'/'.$create_product['product_id'].'/'.$file_server);
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
                    }else{
                        $copy_storage = Storage::disk('public')->copy('assets/files/default.png','assets/files/'.$user_id.'/'.$create_product['product_id'].'/default.png');
                        product_file::create([
                            'product_file_name' => 'default.png',
                            'product_file_server' => 'default.png',
                            'product_file_ext' => 'png',
                            'product_id' => $create_product['product_id'],
                        ]);
                    }
                    $create_stock= stocks::create([
                        'stock_number' => $request->stock_number,
                        'stock_number_sale' => $request->stock_number,
                        'product_id' => $create_product['product_id'],
                        'stock_place_id' => $request->stock_place_id
                    ]);
                    $query_stocks = stock_places::select('stock_place_id')->whereNotin('stock_place_id',[$request->stock_place_id])->get();
                        foreach($query_stocks as $key){
                            $create_stock= stocks::create([
                                'stock_number' => 0,
                                'stock_number_sale' => 0,
                                'product_id' => $create_product['product_id'],
                                'stock_place_id' => $key->stock_place_id
                            ]);
                        }
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



}
