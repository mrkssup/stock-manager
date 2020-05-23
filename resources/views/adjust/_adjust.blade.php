@extends('layouts._master')
@section('main-content')
           <div class="breadcrumb">
                <h1>ปรับจำนวน</h1>
            </div>
            <div class="separator-breadcrumb border-top"></div>
             <!-- ============ form ============= -->
             <div class="row">
                <div class="col-md-12">
                    <div class="card mb-10">
                        <div class="card-body">
                            <form method="POST"  enctype="multipart/form-data" action="{{ url('postadjust') }}">
                                {{ csrf_field() }}
                                <div class="form-group row">
                                    <label for="inputadjust_code" class="col-sm-1 col-form-label">รายการเลขที่</label>
                                    <div class="col-sm-5">
                                    <input type="text"  name ="adjust_code"  value = {{ $adjust_code['adjust_code'] }} class="form-control" id="inputadjust_code" placeholder="รหัสรายการ" readonly="readonly">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputproduct_code" class="col-sm-1 col-form-label">รหัสสินค้า</label>
                                    <div class="col-sm-4">
                                        <input type="hidden"  name ="product_id" value={{ $product['product_id'] }} class="form-control">
                                    <input type="text"  name ="product_code" value={{ $product['product_code'] }} class="form-control" id="inputproduct_code" placeholder="รหัสรายการ" readonly="readonly">
                                    </div>
                                    <label for="inputproduct_name" class="col-sm-1 col-form-label">ชื่อสินค้า</label>
                                    <div class="col-sm-4">
                                    <input type="text"  name ="product_name" value={{ $product['product_name'] }} class="form-control" id="inputproduct_name" placeholder="รหัสรายการ" readonly="readonly">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputadjust_date" class="col-sm-2 col-form-label">วันที่ทำรายการ</label>
                                    <div class="col-sm-6">
                                        <input type="date" name='adjust_date'value={{ $today['today'] }} class="form-control" id="date" name="date">
                                    </div>
                                </div>
                                @foreach ($stocks as $stock)
                                    <div class="form-group row">
                                        <label class="font-weight-600 text-primary col-sm-2 col-form-label">{{$stock['stock_place_name']}}</label>
                                        <input type="hidden" name="adjust_stock[]" value={{$stock['stock_place_id']}} class="form-control">
                                    </div>
                                @endforeach
                                    @foreach ($stock['stock_number'] as $number)
                                        <div class="form-group row">
                                            <label for="inputstock_places_name" class="col-sm-2 col-form-label">จำนวนคงเหลือ</label>
                                            <div class="col-sm-2">
                                                <input type="number" value={{$number['stock_number']}} name="adjust_stock_old[]" class="form-control" id="inputtranfer_code" placeholder="0" readonly="readonly">
                                            </div>
                                            <label for="inputstock_places_name" class="col-sm-2 col-form-label">จำนวนคงเหลือที่ปรับ</label>
                                            <div class="col-sm-2">
                                                <input type="number" name="adjust_stock_new[]" class="form-control" id="inputtranfer_code" placeholder="0">
                                            </div>
                                        </div>
                                    @endforeach

                                <div class="form-group row">
                                    <div class="col-sm-10">
                                        <button type="submit" class="btn btn-primary">บันทึก</button>
                                        <a class="btn btn-primary" href="{{ URL::previous() }}">ย้อนกลับ</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    var msg = '{{Session::get('alert')}}';
    var exist = '{{Session::has('alert')}}';
    if(exist){
      alert(msg);
    }
</script>

@section('page-js')

     <script src="{{asset('assets/js/vendor/echarts.min.js')}}"></script>
     <script src="{{asset('assets/js/es5/echart.options.min.js')}}"></script>
     <script src="{{asset('assets/js/es5/dashboard.v1.script.js')}}"></script>

@endsection
