<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="Content-Type" content="text/html; charset=windows-874" />
        <meta http-equiv="Content-Type" content="text/html; charset=TIS-620" />

        <title>
          <!-- อันนี้ใส่หัวข้อ -->


        </title>

        <!-- Fonts -->
        {{-- <link href="https://fonts.googleapis.com/css?family=Nunito:300,400,400i,600,700,800,900" rel="stylesheet"> --}}
        {{-- <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css"> --}}

        <!-- Styles -->
        <style>
            @font-face {
                font-family: 'THSarabunNew';
                font-style: normal;
                font-weight: normal;
                src: url("{{ public_path('fonts/THSarabunNew.ttf') }}") format('truetype');
            }
            @font-face {
            font-family: 'THSarabunNew';
            font-style: normal;
            font-weight: bold;
            src: url("{{ public_path('fonts/THSarabunNew Bold.ttf') }}") format('truetype');
            }
            @font-face {
            font-family: 'THSarabunNew';
            font-style: italic;
            font-weight: normal;
            src: url("{{ public_path('fonts/THSarabunNew Italic.ttf') }}") format('truetype');
            }
            @font-face {
            font-family: 'THSarabunNew';
            font-style: italic;
            font-weight: bold;
            src: url("{{ public_path('fonts/THSarabunNew BoldItalic.ttf') }}") format('truetype');
            }

            html,body {
                font-family: "THSarabunNew";
                font-size: 24px;
                font-weight: bold;
            }
        </style>

    </head>
    <body>
        <page size="A4">
            <div class="d-flex" style="padding-top: 2rem;">
                <div style="width:30%">
                    <div class="d-flex pt-1rem">
                        <span>ชื่อคู่ค้า {{ $purchases['customer_name'] }}</span>
                        <div class="blankforSignature"></div>
                    </div>
                    <div class="d-flex pt-1rem">
                        <span>ที่อยู่ {{ $purchases['customer_detail'] }}</span>
                        <div class="blankforSignature"></div>
                    </div>
                    <div class="d-flex ">
                        <div class="blankforSignature pt-2rem"></div>
                    </div>
                    <div class="d-flex ">
                        <div class="blankforSignature pt-2rem"></div>
                    </div>
                </div>
                <div style="width:70%;"></div>
            </div>
            <div class="d-block text-center">
                <center><span style="font-size: 30px;">ใบขอซื้อ</span></center>
            </div>
            <div class="d-flex">
                <div style="width:30%">
                    <div class="d-flex pt-1rem">
                        <span>เลขที่ {{ $purchases['purchase_code'] }}</span>
                        <div class="blankforSignature"></div>
                    </div>
                    <div class="d-flex pt-1rem">
                        <span>ผู้ขอซื้อ {{ $purchases['full_name'] }}</span>
                        <div class="blankforSignature"></div>
                    </div>
                    <div class="d-flex pt-1rem">
                        <span>วันที่ขอซื้อ {{ $purchases['purchase_date'] }}</span>
                        <div class="blankforSignature"></div>
                    </div>
                </div>
                <div style="width:70%;"></div>
            </div>
            <table style="width:100%" border="1">
                <tr>
                    <th style="width:100%">รหัสสินค้า</th>
                    <th style="width:100%">ชื่อสินค้า</th>
                    <th style="width:100%">จำนวน</th>
                    <th style="width:100%">ราคาหน่วย (บาท)</th>
                </tr>
                <tr>
                    <td style="width:100%">{{ $purchases['product_code'] }}</td>
                    <td style="width:100%">{{ $purchases['product_name'] }}</td>
                    <td style="width:100%">{{ $purchases['product_number'] }}</td>
                    <td style="width:100%">{{ $purchases['product_price_buy'] }}</td>
                </tr>
                <tr>
                    <td colspan="3" style="width:50%;text-align: right;"><span>รวม</span></td>
                    <td style="width:100%">{{ $purchases['purchase_total'] }}</td>
                    <td style="width:100%">บาท</td>
                </tr>
            </table>
        </page>
    </body>
</html>
