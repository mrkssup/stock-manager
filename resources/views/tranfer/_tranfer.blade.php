@extends('layouts._master')
@section('main-content')
           <div class="breadcrumb">
                <h1>โอนสินค้า</h1>
            </div>
            <div class="separator-breadcrumb border-top"></div>

             <!-- ============ form ============= -->
             <div class="row">
                <div class="col-md-12">
                    <div class="card mb-10">
                        <div class="card-body">
                            <form method="POST"  enctype="multipart/form-data" action="{{ url('posttranfer') }}">
                                {{ csrf_field() }}
                                <div class="form-group row">
                                    <label for="inputtranfer_code" class="col-sm-2 col-form-label">รายการเลขที่</label>
                                    <div class="col-sm-6">
                                    <input type="text"  name ="tranfer_code"  value = {{ $tranfer_code['tranfer_code'] }} class="form-control" id="inputtranfer_code" placeholder="รหัสรายการ" readonly="readonly">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputtranfer_stock_old" class="col-sm-2 col-form-label">จาก</label>
                                    <div class="col-sm-6">
                                        <select class="form-control"  name ="tranfer_stock_old" id="inputtranfer_stock_old">
                                            <option value= "0">---กรุณาเลือก---</option>
                                            @foreach ($stocks as $stock)
                                            <option value= "{{ $stock['stock_place_id'] }}">{{ $stock['stock_place_name'] }}</option>
                                            @endforeach
                                          </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputtranfer_stock_new" class="col-sm-2 col-form-label">ไป</label>
                                    <div class="col-sm-6">
                                        <select class="form-control"  name ="tranfer_stock_new" id="inputtranfer_stock_new">
                                            <option value= "0">---กรุณาเลือก---</option>
                                            @foreach ($stocks as $stock)
                                            <option value= "{{ $stock['stock_place_id'] }}">{{ $stock['stock_place_name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputcategory_id" class="col-sm-2 col-form-label">วันที่ทำรายการ</label>
                                    <div class="col-sm-6">
                                        <input type="date" name='tranfer_date'value={{ $today['today'] }} class="form-control" id="date" name="date" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <input type="hidden" name="product_id" value= "{{ $product['product_id'] }}" class="form-control">
                                    <label for="inputproduct_code" class="col-sm-2 col-form-label">รหัสสินค้า</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="product_code" value= "{{ $product['product_code'] }}" class="form-control" id="inputproduct_code" placeholder="รหัสสินค้า" readonly="readonly">
                                    </div>
                                    <label for="inputproduct_code" class="col-sm-2 col-form-label">ชื่อสินค้า</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="product_name" value= "{{ $product['product_name'] }}" class="form-control" id="inputproduct_name" placeholder="ชื่อสินค้า" readonly="readonly">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputtranfer_stock_number" class="col-sm-2 col-form-label">จำนวน</label>
                                    <div class="col-sm-2">
                                        <input type="number" name ="tranfer_stock_number" class="form-control" id="inputtranfer_stock_number" placeholder="0" required>
                                    </div>
                                </div>
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
