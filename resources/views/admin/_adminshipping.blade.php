@extends('layouts._master')
@section('main-content')
           <div class="breadcrumb">
                <h1>จัดส่งสินค้า</h1>
            </div>
            <div class="separator-breadcrumb border-top"></div>

             <!-- ============ form ============= -->
             <div class="row">
                <div class="col-md-12">
                    <div class="card mb-10">
                        <div class="card-body">
                            <form method="POST"  enctype="multipart/form-data" action="{{ url('postshipping') }}">
                                {{ csrf_field() }}
                                <div class="form-group row">
                                    <label for="inputsell_courier" class="col-sm-2 col-form-label">ผู้จัดส่ง</label>
                                    <div class="col-sm-4">
                                        <label for="inputsell_courier" class="col-sm-4 col-form-label">Kerry Express</label>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputsell_code" class="col-sm-2 col-form-label">tracking number</label>
                                    <div class="col-sm-4">
                                    <input type="text"  name ="tracking_number"  value ={{ $sells['tracking_number'] }} class="form-control" id="inputtracking_number" placeholder="รหัสรายการ" readonly="readonly">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputsell_code" class="col-sm-2 col-form-label">รายการขายเลขที่</label>
                                    <div class="col-sm-4">
                                    <input type="hidden"  name ="sell_id"  value ={{ $sells['sell_id'] }} class="form-control" >
                                    <input type="hidden"  name ="user_id"  value ={{ $sells['user_id'] }} class="form-control" >
                                    <input type="hidden"  name ="stock_place_id"  value ={{ $sells['stock_place_id'] }} class="form-control" >

                                    <input type="text"  name ="sell_code"  value ={{ $sells['sell_code'] }} class="form-control" id="inputsell_code" placeholder="รหัสรายการ" readonly="readonly">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputproduct_name" class="col-sm-2 col-form-label">ชื่อสินค้า</label>
                                    <div class="col-sm-4">
                                    <input type="text"  name ="product_name"  value = {{ $sells['product_name'] }} class="form-control" id="inputproduct_name" placeholder="ชื่อสินค้า" readonly="readonly">
                                    </div>
                                    <label for="inputproduct_name" class="col-sm-1 col-form-label">จำนวน</label>
                                    <div class="col-sm-1">
                                        <input type="text"  name ="product_number"  value = {{ $sells['product_number'] }} class="form-control" id="inputproduct_name" placeholder="ชื่อสินค้า" readonly="readonly">
                                    </div>
                                    <label for="inputproduct_name" class="col-sm-1 col-form-label">ราคา</label>
                                    <div class="col-sm-2">
                                        <input type="text"  name ="sell_total"  value = {{ $sells['sell_total'] }} class="form-control" id="inputsell_total" placeholder="ราคา" readonly="readonly">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputtranfer_code" class="col-sm-2 col-form-label">ชื่อลูกค้า</label>
                                    <div class="col-sm-4">
                                    <input type="text"  name ="sell_name"  value = {{ $sells['customer_name'] }} class="form-control" id="inputtranfer_code" placeholder="รหัสรายการ" readonly="readonly">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputtranfer_code" class="col-sm-2 col-form-label">รายละเอียดลูกค้า</label>
                                    <div class="col-sm-4">
                                        <textarea class="form-control" rows = "5" cols = "50" name = "customer_detail" id="inputcustomer_detail" readonly="readonly">{{ $sells['customer_detail'] }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputtranfer_code" class="col-sm-2 col-form-label">หมายเหตุ</label>
                                    <div class="col-sm-4">
                                        <textarea class="form-control" rows = "5" cols = "50" name = "shipment_detail" id="inputcustomer_detail"></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-10">
                                        <button type="submit" class="btn btn-primary">ส่งสินค้า</button>
                                        <a class="btn btn-primary" href="{{ URL::previous() }}">ย้อนกลับ</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>


@endsection
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
