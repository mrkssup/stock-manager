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

        <div class="auth-layout-wrap">
            <div class="auth-content">
                <div class="card o-hidden">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="p-4">
                                <div class="auth-logo text-center mb-4">
                                    <img src="{{asset('assets/images/stock-manager.png')}}" alt="">
                                </div>
                                <h1 class="mb-3 text-18">ลงชื่อเข้าใช้</h1>
                                @if ( $errmessage = Session::get('errors'))
                                    <div class="alert alert-info" role="alert">
                                        {{ $errmessage }}
                                    </div>
                                 @endif
                                <form method="POST" action="login">
                                    {{ csrf_field() }}
                                    <div class="form-group">
                                        <label for="username">อีเมล์</label>
                                        <input id="username"
                                            class="form-control form-control-rounded"
                                            name="username" value="{{ old('username') }}" required autocomplete="username"
                                            autofocus>
                                    </div>
                                    <div class="form-group">
                                        <label for="password">รหัสผ่าน</label>
                                        <input id="password" type="password"
                                            class="form-control form-control-rounded"
                                            name="password" required autocomplete="current-password">
                                    </div>

                                    <button  type="summit" class="btn btn-rounded btn-primary btn-block mt-2">ลงชื่อเข้าใช้</button>

                                </form>
                            </div>
                        </div>
                        <div class="col-md-6 text-center "
                            style="background-size: cover;background-image: url({{asset('assets/images/warehouse.png')}}">
                            <div class="pr-3 auth-right">
                                <a class="btn btn-rounded btn-outline-primary btn-outline-email btn-block btn-icon-text"
                                    href="{{ route('signup') }}">
                                    <i class="i-Mail-with-At-Sign"></i> สมัครสมาชิก
                                </a>
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
