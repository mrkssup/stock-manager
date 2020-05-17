<?php

namespace App\Http\Controllers;
use App\Model\users;
use App\Model\products;
use App\Model\product_file;
use App\Model\stocks;
use App\Model\stock_places;
use App\Model\category;
use App\Model\tranfer;
use App\Model\user_credits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Config;
use Mail;
use Storage;
use Validator;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class CreditsController extends Controller
{
    public function index(Request $request)
    {
        $user_id = session('uid');
        $date = date("YmdHis",time());
        $today['today'] = date("Y-m-d",time());
        $sum_all_price= null;
        $stocks = array();$i=0;
        $query_stocks = stock_places::select('stock_place_id','stock_place_code','stock_place_name')->get();
        foreach($query_stocks as $key){
            $stocks[$i]['order'] = $i+1;
            $stocks[$i]['stock_place_id'] = $key->stock_place_id;
            $stocks[$i]['stock_place_code'] = $key->stock_place_code;
            $stocks[$i]['stock_place_name'] = $key->stock_place_name;
            $stocks[$i]['stock_price'] = $this->product_stock($user_id,$stocks[$i]['stock_place_id'])[0];
            $stocks[$i]['stock_all_price'] = $this->product_stock($user_id,$stocks[$i]['stock_place_id'])[1];
            $sum_all_price+=$this->product_stock($user_id,$stocks[$i]['stock_place_id'])[1];
            $i++;
        }
        return view('credits._credits')->with(['stocks' => $stocks,'sum_all_price' => $sum_all_price]);
    }

    public function cash(Request $request){
        //require_once dirname(__FILE__).'/vendor/autoload.php';
        $user_id = session('uid');
        if($user_id == ''){
            return redirect('/');
        }else{
            define('OMISE_API_VERSION', '2019-05-29 ');
            define('OMISE_PUBLIC_KEY', 'pkey_test_5jwn5xg9pehqxeo3f0g');
            define('OMISE_SECRET_KEY', 'skey_test_5ju0276ptwmb3qojvj0');

            $date = date("YmdHis",time());
            $today['today'] = date("Y-m-d H:i:s",time());
            $sum_all_price= null;
            $stocks = array();$i=0;
            $query_stocks = stock_places::select('stock_place_id','stock_place_code','stock_place_name')->get();
            foreach($query_stocks as $key){
                $stocks[$i]['order'] = $i+1;
                $stocks[$i]['stock_place_id'] = $key->stock_place_id;
                $stocks[$i]['stock_place_code'] = $key->stock_place_code;
                $stocks[$i]['stock_place_name'] = $key->stock_place_name;
                $stocks[$i]['stock_price'] = $this->product_stock($user_id,$stocks[$i]['stock_place_id'])[0];
                $stocks[$i]['stock_all_price'] = $this->product_stock($user_id,$stocks[$i]['stock_place_id'])[1];
                $sum_all_price+=$this->product_stock($user_id,$stocks[$i]['stock_place_id'])[1];
                $i++;
            }
            $charge = \OmiseCharge::create(array(
                'amount' => $sum_all_price.'00',
                'currency' => 'thb',
                'card' => $request->omiseToken
            ));
            if($charge['status'] == 'successful'){
                DB::beginTransaction();
                try{
                    $create_credit= user_credits::create([
                                    'user_id' => $user_id,
                                    'credit_amount' => $sum_all_price,
                                    ]);
                    $update_stock = stocks::join('products','stocks.product_id','=','products.product_id')
                                    ->where('products.user_id','=',$user_id)
                                    ->update([
                                        'updated_at' => $today['today']
                                    ]);
                    DB::commit();
                    return redirect('/credits')->with('alert' , "เพิ่มข้อมูลสำเร็จ");
                } catch (\Exception $e) {
                    DB::rollback();
                    return $e;
                    return redirect()->back()->with('alert' , "การเพิ่มข้อมูลผิดพลาด".$e );
                }

        }else{
            return redirect()->back()->with('alert' , "เกิดข้อผิดพลาดในการชำระเงิน" );
        }

        echo($charge['status']);

        print('<pre>');
        print_r($charge);
        print('</pre>');
        return 'ok';
        }


    }



    protected function product_stock($user_id,$stock_place_id)
    {
        $now = Carbon::now();
        $sum_product= null;
        $product_stock = array();$i=0;
        $query_product_stock= products::join('users','users.user_id','=','products.user_id')
                        ->join('stocks','products.product_id','=','stocks.product_id')
                        ->select('products.product_id','products.product_code','products.product_name'
                        ,'products.product_volume',DB::raw('sum(stocks.stock_number) as stock_number')
                        ,'stocks.stock_place_id','stocks.updated_at')
                        ->where('products.user_id', $user_id)
                        ->where('stocks.stock_place_id', $stock_place_id)
                        ->groupBy('products.product_id')
                        ->get();
        foreach($query_product_stock as $key){
            $product_stock[$i]['product_id'] = $key->product_id;
            $product_stock[$i]['product_code'] = $key->product_code;
            $product_stock[$i]['product_name'] = $key->product_name;
            $product_stock[$i]['product_volume'] = $key->product_volume;
            $product_stock[$i]['stock_number'] = $key->stock_number;
            $product_stock[$i]['updated_at'] = Carbon::parse($key->updated_at);
            $product_stock[$i]['days'] = $product_stock[$i]['updated_at']->diffInDays($now);
            $product_stock[$i]['price'] = $this->sum_product_price($key->product_volume,$key->stock_number,$product_stock[$i]['days']);
            $sum_product+=$this->sum_product_price($key->product_volume,$key->stock_number,$product_stock[$i]['days']);
            $i++;
        }
        return [$product_stock,$sum_product];
    }


    protected function sum_product_price($product_volume,$stock_number,$day)
    {
        $product_price = null;
        $per_day =3;
        $product_price = $product_volume*$stock_number*$day*$per_day;
        return $product_price;
    }


    protected function get_host()
    {
      $domain = url('/');
      $info = parse_url($domain);
      $host = $info['host'];
      return $host;
    }



}
