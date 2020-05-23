@extends('layouts._master')
@section('main-content')
           <div class="breadcrumb">
                <h1>รายละเอียดรายการขาย</h1>
            </div>
            <div class="separator-breadcrumb border-top"></div>
            <section class="ul-purchase-detail">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-1">
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="ul-purchase-detail-purchase-code mb-4 d-flex align-items-baseline">
                                            <h4 class="font-weight-400 text-primary mb-0 mr-2">รายการเลขที่</h4>
                                            <h5 class="font-weight-200 text-primary mb-0 mr-2">{{ $sells['sell_code'] }}</h5>
                                        </div>
                                        <div class="ul-purchase-detail-purchase-type mb-4 d-flex align-items-baseline">
                                            <h4 class="font-weight-400 text-primary mb-0 mr-2">ประเภทรายการ</h4>
                                            <h5 class="font-weight-200 text-primary mb-0 mr-2">ขายสินค้าออก</h5>
                                        </div>
                                        <div class="ul-purchase-detail-purchase-date mb-4 d-flex align-items-baseline">
                                            <h4 class="font-weight-400 text-primary mb-0 mr-2">วันที่ทำรายการ</h4>
                                            <h5 class="font-weight-200 text-primary mb-0 mr-2">{{ $sells['sell_date'] }}</h5>
                                        </div>
                                        <div class="ul-purchase-detail-purchase-date mb-4 d-flex align-items-baseline">
                                            <h4 class="font-weight-400 text-primary mb-0 mr-2">อ้างอิง</h4>
                                            <h5 class="font-weight-200 text-primary mb-0 mr-2">{{ $sells['sell_reference'] }}</h5>
                                        </div>
                                        <div class="ul-purchase-detail-purchase-status mb-4 d-flex align-items-baseline">
                                            <h4 class="font-weight-400 text-primary mb-0 mr-2">สถานะ</h4>
                                            @if( $sells['sell_status'] == 1)
                                                <h5 class="font-weight-200 text-primary mb-0 mr-2">กำลังดำเนินการ</h5>
                                            @elseif($sells['sell_status'] == 2 )
                                                <h5 class="font-weight-200 text-primary mb-0 mr-2">สำเร็จ</h5>
                                            @elseif($sells['sell_status'] == 9)
                                                <h5 class="font-weight-200 text-primary mb-0 mr-2">ยกเลิก</h5>
                                            @else
                                                <h5 class="font-weight-200 text-primary mb-0 mr-2">รอโอนสินค้า</h5>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-1">
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="ul-purchase-detail-purchase-name mb-4 d-flex align-items-baseline">
                                            <h4 class="font-weight-400 text-primary mb-0 mr-2">ชื่อผู้ทำรายการ</h4>
                                            <h5 class="font-weight-200 text-primary mb-0 mr-2">{{ $sells['sell_user'] }}</h5>
                                        </div>
                                        <div class="ul-purchase-detail-purchase-name mb-4 d-flex align-items-baseline">
                                            <h4 class="font-weight-400 text-primary mb-0 mr-2">ชื่อลูกค้า</h4>
                                            <h5 class="font-weight-200 text-primary mb-0 mr-2">{{ $sells['customer_name'] }}</h5>
                                        </div>
                                        <div class="ul-purchase-detail-purchase-name mb-4 d-flex align-items-baseline">
                                            <h4 class="font-weight-400 text-primary mb-0 mr-2">รายละเอียดลูกค้า</h4>
                                            <h5 class="font-weight-200 text-primary mb-0 mr-2">{{ $sells['customer_detail'] }}</h5>
                                        </div>
                                        <div class="ul-purchase-detail-purchase-name mb-4 d-flex align-items-baseline">
                                            <h4 class="font-weight-400 text-primary mb-0 mr-2">สินค้าออกที่</h4>
                                            <h5 class="font-weight-200 text-primary mb-0 mr-2">{{ $sells['stock_place_name'] }}</h5>
                                        </div>
                                        @if( $sells['sell_status'] == 0)
                                            <div class="ul-product-detail__brand-name mb-4">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-brand dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">การจัดการ</button>
                                                    <div class="dropdown-menu" x-placement="bottom-start">
                                                        <a class="dropdown-item" href="/editsell/{{ $sells['sell_id'] }}">แก้ไข</a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <div class="separator-breadcrumb border-top"></div>
            <div class="col-lg-12 col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card o-hidden mb-4">
                            <div class="card-header d-flex align-items-center border-0">
                                <h3 class="w-50 float-left card-title m-0">รายการสินค้า</h3>
                                <div class="text-right w-50 float-right">
                                </div>
                            </div>

                            <div class="">
                                <div class="table-responsive">
                                    <table id="user_table" class="table  text-center">
                                        <thead>
                                            <tr>
                                                <th scope="col">รหัสสินค้า</th>
                                                <th scope="col">ชื่อสินค้า</th>
                                                <th scope="col">จำนวน</th>
                                                <th scope="col">รวม</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ $sells['product_code'] }}</td>
                                                <td><a href="/product/{{ $sells['product_id'] }}">{{ $sells['product_name'] }}</a></td>
                                                <td>{{ $sells['product_number'] }}</td>
                                                <td>{{ $sells['product_total'] }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>






@endsection


@section('page-js')
    <script>
        var msg = '{{Session::get('alert')}}';
        var exist = '{{Session::has('alert')}}';
        if(exist){
        alert(msg);
         }
    </script>
    <script>
    $(document).ready(function () {
        var ctx = document.getElementById("DoughnutChart");
        var data = {
            labels: ["Mozila", "IE", "Google Chrome", " Edge", "Safari"],
            datasets: [{
                data: [120, 50, 140, 180, 100],
                backgroundColor: ["#455C73", "#9B59B6", "#BDC3C7", "#26B99A", "#3498DB"],
                hoverBackgroundColor: ["#34495E", "#B370CF", "#CFD4D8", "#36CAAB", "#49A9EA"]

            }]
        };

        var DoughnutChart = new Chart(ctx, {
            type: 'doughnut',
            tooltipFillColor: "rgba(51, 51, 51, 0.55)",
            data: data
        });
        var ctx = document.getElementById("BarChart");
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ["Red", "Blue", "Yellow", "Green", "Purple", "Orange", 'olive', 'Teal', 'Magenta'],
            datasets: [{
                label: '# of Votes',
                data: [12, 19, 3, 5, 2, 3, 10, 14, 9],
                backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)', 'rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)', 'rgba(255, 159, 64, 0.2)', 'rgba(128, 128, 0, 0.2)', 'rgb(0, 128, 128,0.2)', 'rgb(255, 0, 255,0.2)'],
                borderColor: ['rgba(255,99,132,1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)', 'rgba(128, 128, 0, 1)', 'rgb(0, 128, 128,1)', 'rgb(255, 0, 255,1)'],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
    });
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>




@endsection

