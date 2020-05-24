<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Stock-manager</title>
        <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800,900" rel="stylesheet">
        <link rel="stylesheet" href="{{asset('assets/styles/css/themes/lite-blue.min.css')}}">
    </head>

    <body>
        <div class="auth-layout-wrap" >
            <div class="auth-content">
                <div class="card o-hidden">
                    <div class="row">
                        <div class="col-md-2">
                        </div>
                        <div class="col-md-8">
                            <div class="p-4">
                                <h1 class="mb-3 text-18">สมัครสมาชิก</h1>
                                <form method="POST" action="postregister">
                                    {{ csrf_field() }}
                                    <div class="form-group">
                                        <label for="username">อีเมล์</label>
                                        <input id="username" type="email"
                                            class="form-control-rounded form-control @error('username') is-invalid @enderror"
                                            name="username" value="{{ old('username') }}" required autocomplete="username">
                                    </div>
                                    <div class="form-group">
                                        <label for="first_name">ชื่อ</label>
                                        <input id="first_name" type="text"
                                            class="form-control-rounded form-control @error('first_name') is-invalid @enderror"
                                            name="first_name" value="{{ old('first_name') }}" required autocomplete="first_name"
                                            autofocus>

                                    </div>
                                    <div class="form-group">
                                        <label for="last_name">นามสกุล</label>
                                        <input id="last_name" type="text"
                                            class="form-control-rounded form-control @error('last_name') is-invalid @enderror"
                                            name="last_name" value="{{ old('last_name') }}" required autocomplete="last_name"
                                            autofocus>

                                    </div>
                                    <div class="form-group">
                                        <label for="tel">เบอร์โทรศัพท์</label>
                                        <input id="tel" type="text"
                                            class="form-control-rounded form-control @error('tel') is-invalid @enderror"
                                            name="tel" value="{{ old('tel') }}" required autocomplete="tel"
                                            autofocus>

                                    </div>
                                    <div class="form-group">
                                        <label for="password">รหัสผ่าน</label>
                                        <input id="password" type="password"
                                            class="form-control-rounded form-control @error('password') is-invalid @enderror"
                                            name="password" required autocomplete="new-password">


                                    </div>
                                    <div class="form-group">
                                        <label for="repassword">ยืนยันรหัสผ่าน</label>
                                        <input id="password-confirm" type="password"
                                            class="form-control-rounded form-control" name="password_confirmation"
                                            required autocomplete="new-password">
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-block btn-rounded mt-3">สมัครสมาชิก</button>
                                </form>
                                <div class="form-group">
                                    <a class="btn btn-primary btn-block btn-rounded mt-3"href="{{ URL::previous() }}">ย้อนกลับ</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="{{asset('assets/js/common-bundle-script.js')}}"></script>

        <script src="{{asset('assets/js/script.js')}}"></script>
    </body>

</html>
