@extends('layouts._master')
@section('main-content')
           <div class="breadcrumb">
                <h1>ภาพรวมระบบ</h1>
            </div>

            <div class="separator-breadcrumb border-top"></div>

            <div class="row">
                <!-- ICON BG -->
                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                        <div class="card-body text-left">
                            <p class="text-muted text-20 mt-2 mb-0">รายการซื้อที่รอดำเนินการ</p>
                            <div class="content">
                                <p class="text-primary text-20 line-height-2 mb-2">{{ $count_purchase }}</p>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-lg-4 col-md-6 col-sm-6">
                    <div class="card card-icon-bg card-icon-bg-primary o-hidden mb-4">
                        <div class="card-body text-left">
                            <p class="text-muted text-20 mt-2 mb-0">รายการขายที่รอดำเนินการ</p>
                            <div class="content">
                                <p class="text-primary text-20 line-height-2 mb-2">{{ $count_sell }}</p>
                            </div>
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
                                    <h3 class="w-50 float-left card-title m-0">รายชื่อผู้ใช้ในระบบ</h3>
                                    <div class="text-right w-50 float-right">
                                        <a class="btn btn-success btn-icon m-1" href="/export_user" role="button">
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
                                                    <th scope="col">ชื่อ</th>
                                                    <th scope="col">นามสกุล</th>
                                                    <th scope="col">อีเมล์</th>
                                                    <th scope="col">เบอร์โทรศัพท์</th>
                                                    <th scope="col">วันที่สร้าง</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($users as $user)
                                                <tr>
                                                        <td>{{ $user['order'] }}</td>
                                                        <td>{{ $user['first_name'] }}</td>
                                                        <td>{{ $user['last_name'] }}</td>
                                                        <td>{{ $user['email'] }}</td>
                                                        <td>{{ $user['tel'] }}</td>
                                                        <td>{{ $user['created_at'] }}</td>
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
    {{-- <script>
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
    </script> --}}
     <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
     {{-- <script src="{{asset('assets/js/vendor/echarts.min.js')}}"></script> --}}
     {{-- <script src="{{asset('assets/js/es5/echart.options.min.js')}}"></script> --}}
     {{-- <script src="{{asset('assets/js/es5/dashboard.v1.script.js')}}"></script> --}}

@endsection
