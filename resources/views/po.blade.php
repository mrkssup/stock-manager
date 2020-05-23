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
                <div style="width:100%;text-align: center;border-right:2px solid black;background-color: black;color:white;padding:10px">
                    <span class="d-block">ใบสั่งซื้อ</span>
                    <span class="d-block titleHead" style="font-size: 16px;">(Purchase Order)</span>
                </div>
                <div style="width:100%;text-align: center;">
                    <div style="border-bottom: 2px solid black;font-size: 20px;padding: 3px;">
                        <span>
                            เลขที่รายการ
                        </span>
                        <span>
                            P0-20170001
                        </span>
                    </div>
                </div>
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
                </div>
                <div style="width:70%;"></div>
            </div>
            <div class="d-flex">
                <div style="width:30%">
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
                    <th style="width:100%;text-align: center;border-right:2px solid black;background-color: black;color:white;padding:5px">รหัสสินค้า</th>
                    <th style="width:100%;text-align: center;border-right:2px solid black;background-color: black;color:white;padding:5px">ชื่อสินค้า</th>
                    <th style="width:100%;text-align: center;border-right:2px solid black;background-color: black;color:white;padding:5px">จำนวน</th>
                    <th style="width:100%;text-align: center;border-right:2px solid black;background-color: black;color:white;padding:5px">ราคาหน่วย (บาท)</th>
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
            <br><br>
            <div class="row">
                <div class="borderBlack signatur" style="width:33%">
                    <div style="border-bottom: 1px dashed #000;display: block;text-decoration: none;"></div>
                    <span style="display: block;margin-top:2px">ผู้ตรวจสอบ / Approver</span>
                    <div style="display: flex;margin-top:15px">
                        <span>วันที่ / date</span>
                        <div style="border-bottom: 1px dotted #000;display: block;text-decoration: none;flex:1"></div>
                    </div>
                </div>
                <div class="borderBlack signatur" style="width:34%;border-left: 0;"></div>
                <div class="borderBlack signatur" style="width:33%;border-left:0">
                    <div style="border-bottom: 1px dashed #000;display: block;text-decoration: none;"></div>
                    <span style="display: block;margin-top:2px">ผู้มีอำนาจลงนาม / Authorized Signature</span>
                    <div style="display: flex;margin-top:15px">
                        <span>วันที่ / date</span>
                        <div style="border-bottom: 1px dotted #000;display: block;text-decoration: none;flex:1"></div>
                    </div></div>
                </div>
            </div>
        </page>
    </body>
</html>
