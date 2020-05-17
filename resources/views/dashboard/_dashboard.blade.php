@extends('layouts._master')
@section('main-content')
           <div class="breadcrumb">
                <h1>ภาพรวม</h1>
            </div>

            <div class="separator-breadcrumb border-top"></div>

            <div class="row">
                <!-- ICON BG -->
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                        <div class="card-body text-center">
                            <i class="i-Money-2"></i>
                            <div class="content">
                                <p class="text-muted mt-2 mb-0">ยอดซื้อทั้งหมด</p>
                                <p class="text-primary text-24 line-height-1 mb-2">{{ $sum_all['sum_purchase'] }} บาท</p>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                        <div class="card-body text-center">
                            <i class="i-Financial"></i>
                            <div class="content">
                                <p class="text-muted mt-2 mb-0">ยอดขายทั้งหหมด</p>
                                <p class="text-primary text-24 line-height-1 mb-2">{{ $sum_all['sum_sell'] }} บาท</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                        <div class="card-body text-center">
                            <i class="i-Financial"></i>
                            <div class="content">
                                <p class="text-muted mt-2 mb-0">จำนวนสินค้าทั้งหมด</p>
                                <p class="text-primary text-24 line-height-1 mb-2">{{ $sum_all['sum_stock'] }} ชิ้น</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class=" col-lg-8 col-md-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="card-title">ยอดขายล่าสุด</div>
                            <canvas id="BarChart2" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-sm-12">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="card-title">จำนวนสินค้ารายคลัง</div>
                            <canvas id="PieChart" height="300px"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card o-hidden mb-4">
                                <div class="card-header d-flex align-items-center border-0">
                                    <h3 class="w-50 float-left card-title m-0">รายการเคลื่อนไหว</h3>
                                    <div class="text-right w-50 float-right">
                                        <a class="btn btn-success btn-icon m-1" href="/export_all" role="button">
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
                                                    <th scope="col">สถานะ</th>
                                                    <th scope="col">รายการเลขที่</th>
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
                                                        @if( $card['status'] == '1' ){
                                                            <td>สำเร็จ</td>
                                                        }@elseif( $card['status'] == '0' ){
                                                            <td>รอโอนสินค้า</td>
                                                        }@else{
                                                           <td>{{ $card['status']}}</td>
                                                        }
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
            </div>


@endsection

@section('page-js')
    <script>
        $(document).ready(function () {
            var ctx = document.getElementById("BarChart2");
            var mybarChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    //labels: ["January", "February", "March", "April", "May", "June", "July"],
                    labels: {!! json_encode($month) !!},
                    datasets: [{
                    label: '# ยอดซื้อ',
                    backgroundColor: "#4BC0C0",
                    //data: [51, 30, 40, 28, 92, 50, 45]
                    data: {!! json_encode($purchase) !!}
                    }, {
                    label: '# ยอดขาย',
                    backgroundColor: "#36A2EB",
                    //data: [41, 56, 25, 48, 72, 34, 12]
                    data: {!! json_encode($sell) !!}
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
            var ctx = document.getElementById("PieChart");
            var data = {
                // labels: ["Mozila","IE","Google Chrome","Edge","Safari"],
                labels: {!! json_encode($stocks_name) !!},
                datasets: [{
                    // data: [120, 50, 140, 180, 100],
                    data: {!! json_encode($stocks_number) !!},
                    backgroundColor: [
                        "#455C73",
                        "#9B59B6",
                        "#BDC3C7",
                        "#26B99A",
                        "#3498DB"
                    ],
                    hoverBackgroundColor: [
                        "#34495E",
                        "#B370CF",
                        "#CFD4D8",
                        "#36CAAB",
                        "#49A9EA"
                     ]

                }]
            };
            var PieChart = new Chart(ctx, {
                type: 'pie',
                tooltipFillColor: "rgba(51, 51, 51, 0.55)",
                data: data
    });
        });
    </script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
     {{-- <script src="{{asset('assets/js/vendor/echarts.min.js')}}"></script> --}}
     {{-- <script src="{{asset('assets/js/es5/echart.options.min.js')}}"></script> --}}
     {{-- <script src="{{asset('assets/js/es5/dashboard.v1.script.js')}}"></script> --}}

@endsection
