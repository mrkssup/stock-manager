@extends('layouts._master')
@section('main-content')
           <div class="breadcrumb">
                <h1>รายละเอียดสินค้า</h1>
            </div>
            <div class="separator-breadcrumb border-top"></div>
            <section class="ul-product-detail">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="ul-product-detail__image">
                                            <img src={{ $products['product_file_server'] }} alt="">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="ul-product-detail__brand-name mb-4">
                                            <h4 class="font-weight-600 text-primary mb-0 mr-2">{{ $products['product_name'] }}</h4>
                                            <h5 class="font-weight-300 text-primary mb-0 mr-2">{{ $products['product_code'] }}</h5>
                                            <span class="text-mute">{{ $products['category_name'] }}</span>
                                        </div>
                                        <div class="ul-product-detail__brand-name mb-4">
                                            <h4 class="font-weight-600 text-primary mb-0 mr-2">ราคาขาย</h4>
                                            <h5 class="heading">{{ $products['product_price_sell'] }} บาท</h5>
                                        </div>
                                        <div class="ul-product-detail__brand-name mb-4">
                                            <h4 class="font-weight-600 text-primary mb-0 mr-2">ราคาซื้อ</h4>
                                            <h5 class="heading">{{ $products['product_price_buy'] }} บาท</h5>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="ul-product-detail__brand-name mb-4">
                                            <h4 class="font-weight-600 text-primary mb-0 mr-2">สินค้าคงเหลือ</h4>
                                            <h5 class="heading">{{ $products['stock_number'] }}</h5>
                                        </div>
                                        <div class="ul-product-detail__brand-name mb-4">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-brand dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">การจัดการ</button>
                                                <div class="dropdown-menu" x-placement="bottom-start">
                                                    <a class="dropdown-item" href="/addpurchase/{{ $products['product_id'] }}">ชื้อสินค้า</a>
                                                    <a class="dropdown-item" href="/addsell/{{ $products['product_id'] }}">ขายสินค้า</a>
                                                    <a class="dropdown-item" href="/tranfer/{{ $products['product_id'] }}">โอนสินค้า</a>
                                                    <a class="dropdown-item" href="/adjust/{{ $products['product_id'] }}">ปรับจำนวน</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item" href="/editproduct/{{ $products['product_id'] }}">แก้ไข</a>
                                                    <a class="dropdown-item" id ="open-dialog"  data-id={{ $products['product_id'] }}  data-toggle="modal"  href="#deleteModal">ลบ</a>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <div class="separator-breadcrumb border-top"></div>
                <div class="row">
                    <div class=" col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="card-title">จำนวนสินค้าคงเหลือรายคลัง</div>
                                <canvas id="DoughnutChart" height="200px"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class=" col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="card-title">ยอดขายห้าวันหลังสุด</div>
                                <canvas id="BarChart" height="200px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="separator-breadcrumb border-top"></div>
            <div class="col-lg-12 col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card o-hidden mb-4">
                            <div class="card-header d-flex align-items-center border-0">
                                <h3 class="w-50 float-left card-title m-0">รายการเคลื่อนไหว</h3>
                                <div class="text-right w-50 float-right">
                                    <a class="btn btn-success btn-icon m-1" href="/export_stockcard/{{ $products['product_id'] }}" role="button">
                                        <span class="ul-btn__icon"><i class="i-Add"></i></span>
                                        <span class="ul-btn__text">export to .xslx</span>
                                    </a>
                                </div>
                            </div>

                            <div class="">
                                <div class="table-responsive">
                                    <table id="user_table" class="table  text-center">
                                        <thead>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">ประเภท</th>
                                                <th scope="col">รายการเลขที่</th>
                                                <th scope="col">สถานะ</th>
                                                <th scope="col">จำนวน</th>
                                                <th scope="col">จาก</th>
                                                <th scope="col">ไป</th>
                                                <th scope="col">คงเหลือ</th>
                                                <th scope="col">วันที่ทำรายการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($stock_cards as $card)
                                            <tr>
                                                    <td>{{ $card['order'] }}</td>
                                                    <td>{{ $card['type'] }}</td>
                                                    <td>{{ $card['code'] }}</td>
                                                    @if( $card['status'] == '1' )
                                                        <td>กำลังดำเนินการ</td>
                                                    @elseif( $card['status'] == '2' )
                                                        <td>สำเร็จ</td>
                                                    @elseif( $card['status'] == '9' )
                                                        <td>ยกเลิก</td>
                                                    @elseif( $card['status'] == '0' )
                                                        <td>รอโอนสินค้า</td>
                                                    @else
                                                       <td>{{ $card['status']}}</td>
                                                    @endif
                                                    <td>{{ $card['number'] }}</td>
                                                    <td>{{ $card['stock_out'] }}</td>
                                                    <td>{{ $card['stock_in'] }}</td>
                                                    <td>{{ $card['stock_number'] }}</td>
                                                    <td>{{ $card['date'] }}</td>
                                            </tr>
                                            @endforeach
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
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
            //labels: ["Mozila", "IE", "Google Chrome", " Edge", "Safari"],
            labels: {!! json_encode($stocks) !!},
            datasets: [{
                data: {!! json_encode($stock_number) !!},
                backgroundColor: ["#455C73", "#9B59B6"],
                hoverBackgroundColor: ["#34495E", "#B370CF"]
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
            //labels: ["Red", "Blue", "Yellow", "Green", "Purple"],
            labels: {!! json_encode($sell_date) !!},
            datasets: [{
                label: '#ยอดขาย',
                data: {!! json_encode($sell_total) !!},
                backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(255, 206, 86, 0.2)', 'rgba(75, 192, 192, 0.2)', 'rgba(153, 102, 255, 0.2)', 'rgba(255, 159, 64, 0.2)', 'rgba(128, 128, 0, 0.2)'],
                borderColor: ['rgba(255,99,132,1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)', 'rgba(128, 128, 0, 1)'],
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

