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


class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $user_id = '12';
        $category = array();$i=0;
        $query_category = category::join('users','category.user_id','=','users.user_id')
                        ->select('category.category_id','category.category_name')
                        ->where('users.user_id', $user_id)
                        ->where('category.category_status', '1')
                        ->get();
        foreach($query_category as $key){
            $category[$i]['order'] = $i+1;
            $category[$i]['category_id'] = $key->category_id;
            $category[$i]['category_name'] = $key->category_name;
            $i++;
        }
        return view('category._category')->with(['category' => $category]);
    }


    public function search(Request $request)
    {
        $user_id = '12';
        $search = $request->search;
        $category = array();$i=0;
        $query_category = category::select('category_id','category_name',)
                        ->where('user_id', $user_id)
                        ->where('category_name', 'LIKE' , '%'.$search.'%');
        $query_category = $query_category->get();
        foreach($query_category as $key){
            $category[$i]['order'] = $i+1;
            $category[$i]['category_id'] = $key->category_id;
            $category[$i]['category_name'] = $key->category_name;
            $i++;
        }
        return view('category._category')->with(['category' => $category]);
    }

    public function store(Request $request)
    {
        $user_id = '12';
        if($user_id == ''){
            return redirect('/');
        }else{
            $validate = Validator::make($request->all(),[
                'category_name' => 'required|string',
              ]);
            if($validate->fails()){
                $errors = $validate->errors();
                return redirect()->back()->with('message',$errors);
            }else{
                if($request){
                    DB::beginTransaction();
                    try{
                        $create_category= category::create([
                            'category_name' => $request->category_name,
                            'user_id' => $user_id,
                        ]);
                        DB::commit();
                        return redirect('/category')->with('alert' , "เพิ่มข้อมูลสำเร็จ");
                    } catch (\Exception $e) {
                        DB::rollback();
                        return $e;
                        return view('/category')->with('alert' , "การเพิ่มข้อมูลผิดพลาด".$e );
                    }
                }else{
                    return redirect('/category')->with('alert' , "ชุดข้อมูลไม่ถูกต้อง" );
                }
            }

        }
    }

    public function edit(Request $request)
    {
        $user_id = '12';
        if($user_id == ''){
            return redirect('/');
        }else{
            if($request){
                $query_check_category = category::where('category_id',$request->category_id)->get();
                if(!is_null($query_check_category)){
                    DB::beginTransaction();
                try{
                    $update_category = category::where('category_id','=',$request->category_id)
                        ->update([
                        'category_name' => $request->category_name,
                        ]);
                    DB::commit();
                    return redirect('/category');
                    } catch (\Exception $e) {
                        DB::rollback();
                        // return $e;
                        return redirect('/category')->with('alert' , "การแก้ไขข้อมูลผิดพลาด".$e );
                    }
                }else{
                    return view('others._notFound');
                }
            }else{
                return redirect('/category')->with('alert' , "ชุดข้อมูลไม่ถูกต้อง" );
            }
        }
    }


    public function delete(Request $request)
    {
        $user_id = '12';
        if($user_id == ''){
            return redirect('/');
        }else{
            if($request){
                $query_check_category =category::where('category_id',$request->category_id)->get();
                if(!is_null($query_check_category)){
                    DB::beginTransaction();
                try{
                    $delete_category = category::where('category_id','=',$request->category_id)
                        ->update([
                        'category_status' => '99',
                        ]);
                    DB::commit();
                    return redirect('/category');
                    } catch (\Exception $e) {
                        DB::rollback();
                        //return $e;
                        return redirect('/category')->with('alert' , "การแก้ไขข้อมูลผิดพลาด".$e );
                    }
                }else{
                    return view('others._notFound');
                }
            }else{
                return redirect('/category')->with('alert' , "ชุดข้อมูลไม่ถูกต้อง" );
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
