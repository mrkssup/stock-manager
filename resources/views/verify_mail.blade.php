<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>
          <!-- อันนี้ใส่หัวข้อ -->


        </title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                /* background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh; */
                margin: 0;
                padding: 0;
            }

            .title{
              font-weight: bold;
              font-size: 18px;
            }

            .link{
              width: 100%;
              margin-top: 20px;
            }

        </style>
    </head>
    <body> <!--style="background: black; color: white"-->


                <div class="title">
                    {!! $title !!}
                </div>

                <div class="content">
                  {!! $content !!}
                </div>

                <div class="link">
                  {!! $link !!}
                </div>

                <div class="content2">
                  {!! $content2 !!}
                </div>

    </body>
</html>
