<?php

namespace App\Http\Controllers;
use App\Model\users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Arr;
use Validator;
use Mail;
use Config;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'username' => 'required|string',
            'password' => 'required|confirmed|min:8|required|string|min:8',
            'password_confirmation' => 'required|min:8',
          ]);
          if($validate->fails()){
            $errors = $validate->errors();
            return redirect()->back()->with('message',$errors);
          }else{
              $password = md5($request->password);
              $email = $request->username;
              $check_mail = users::where('email','=',$email)->get();
              if(count($check_mail)>0){
                return redirect()->back()->with('errors' , "ข้อมูลอีเมล์ซ้ำ");
              }
              $token = bin2hex(random_bytes(16));
              $request->merge(['password'=>$password]);
              $request->merge(['email'=>$email]);
              $request->merge(['token'=>$token]);
              $create_user = users::create($request->all());
              $link = 'https://'.$this->get_host()."/verify?token=$token";
              $this->verify_email($email,$link);
              return redirect('/')->with('alert', 'ดำเนินการสำเร็จ กรุณาตรวจสอบอีมเล์ของท่านเพื่อยืนยันการสมัครสมาชิก');
          }

    }

    public function verify(Request $request)
    {
      if($request->has('token')){
          $get_token = $request->token;
        if ( $get_token=='') {
          return response()->json(["status"=>"failed","message"=>'missing parameter.'],400);
        }else {
          $token = users::select('token')->where('token','=',$get_token)->first();
          if($token->token == $get_token){
            users::where('token','=',$get_token)->update(['verify' => 1]);
            return redirect('/');
          }else{
            return response()->json(["status"=>"failed","message"=>'token mismatch.'],400);
          }
        }
      }else{
        return response()->json(["status"=>"failed","message"=>'missing parameter.'],400);
      }
    }

    public function signin(Request $request)
    {
        $user_id = session('uid');
        if($user_id == ''){
            return view('sessions._signin');
        }else{
            return redirect('dashboard');
        }

    }

    public function login(Request $request)
    {
      $credentials = $request->only('username', 'password');
      $username = $credentials['username'];
      $password = md5($credentials['password']);
      $authorize = users::where('username','=',$username)->where('password','=',$password)->first();
      if(is_null($authorize)){
        return redirect()->back()->with(['errors' => 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง']);
      }else{
        if($authorize->verify == '0'){
          return redirect()->back()->with(['errors' => 'ชื่อผู้ใช้ยังไม่ได้ยืนยัน']);
        }else{
          session(['uid' => $authorize->user_id]);
          session(['fullname' => $authorize->first_name.' '.$authorize->last_name]);
          session(['role' => $authorize->role]);
          if($authorize->role == 99){
            return redirect('/admin/dashboard');
          }else{
            return redirect('dashboard');
          }

        }
      }
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect('/');
    }



    //------------------------------------------------------------protected function--------------------------------------------

    protected function verify_email($email,$link) {
      $set_config_mail = $this->set_config_email();
      $doc_mail = Mail::send('verify_mail', ['title' => "<p>ยืนยันอีเมล์การสมัครสมาชิก</p>",
      'content' => "ท่านสามารถยืนยันการสมัครสมากชิกได้ด้วยการกดที่ลิงก์ <a href='$link'>กดที่นีเพื่อยีนอีเมล์</a><br>หรือคัดลอกลิงก์ด้านล่างไปวางยังแถบที่อยู่บนเว็บบราวเซอร์ของท่าน", 'link' => $link,
      'content2' => "<br>*ลิงก์มีอายุการใช้งาน 15 นาที หากไม่ได้ทำรายการภายในเวลาที่กำหนด กรุณาแจ้งผู้ดูแลระบบ
                     <br>***ขอความกรุณาอย่าตอบกลับที่อีเมลนี้ เนื่องจากเป็นระบบข้อความอัตโนมัติ"],
      function ($m) use ($email) {
             $m->from('noreply@stock-manager', 'stock-manager');
             $m->to($email)->subject('การยืนยันการสมัครสมาชิก');
         });
      return $doc_mail;
   }

   protected function set_config_email()
    {
      $config_data = array(
           'driver'     => env('MAIL_DRIVER'),
           'host'       => env('MAIL_HOST'),
           'port'       => env('MAIL_PORT'),
           'from'       => array('address' => 'noreply@stock-manager', 'name' => 'stock-manager'),
           'username'   => env('MAIL_USERNAME'),
           'password'   => env('MAIL_PASSWORD'),
           'encryption' => env('MAIL_ENCRYPTION'),
           'sendmail'   => '/usr/sbin/sendmail -bs',
           'pretend'    => false,
           'stream' => [
               'ssl' => [
                   'verify_peer' => false,
                   'verify_peer_name' => false,
                   'allow_self_signed' => true,
               ],
           ]
         );
      Config::set('mail', $config_data);
    }

    protected function get_host()
    {
      $domain = url('/');
      $info = parse_url($domain);
      $host = $info['host'];
      return $host;
    }



}
