@extends('layouts._master')
@section('main-content')
           <div class="breadcrumb">
                <h1>เพิ่มสินค้า</h1>
            </div>
            <div class="separator-breadcrumb border-top"></div>

             <!-- ============ form ============= -->
             <div class="row">
                <div class="col-md-12">
                    <div class="card mb-10">
                        <div class="card-body">
                            <form method="POST"  enctype="multipart/form-data" action="postproduct">
                                {{ csrf_field() }}
                                <div class="form-group row">
                                    <label for="inputproduct_code" class="col-sm-2 col-form-label">รหัสสินค้า</label>
                                    <div class="col-sm-6">
                                        <input type="text"  name ="product_code" class="form-control" id="inputproduct_code" placeholder="รหัสสินค้า">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputproduct_name" class="col-sm-2 col-form-label">ชื่อสินค้า</label>
                                    <div class="col-sm-6">
                                        <input type="text" name ="product_name" class="form-control" id="inputproduct_name" placeholder="ชื่อสินค้า">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputcategory_id" class="col-sm-2 col-form-label">หมวดหมู่</label>
                                    <div class="col-sm-6">
                                        <select class="form-control"  name ="category_id" id="inputcategory_id">
                                            <option value= "0">---กรุณาเลือก---</option>
                                            @foreach ($category as $cat)
                                            <option value= "{{ $cat['category_id'] }}">{{ $cat['category_name'] }}</option>
                                            @endforeach
                                          </select>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputproduct_price_buy" class="col-sm-2 col-form-label">ราคาซื้อ</label>
                                    <div class="col-sm-2">
                                        <input type="text" name ="product_price_buy" class="form-control" id="inputproduct_price_buy" placeholder="0.00">
                                    </div>
                                    <label for="inputproduct_price_sell" class="col-sm-2 col-form-label">ราคาขาย</label>
                                    <div class="col-sm-2">
                                        <input type="text" name ="product_price_sell" class="form-control" id="inputproduct_price_sell" placeholder="0.00">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputproduct_unit" class="col-sm-2 col-form-label">หน่วยสินค้า</label>
                                    <div class="col-sm-4">
                                        <input type="text" name ="product_unit" class="form-control" id="inputproduct_unit" placeholder="หน่วยสินค้า">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputproduct_volume" class="col-sm-2 col-form-label">ขนาดสินค้าต่อชิ้น</label>
                                    <div class="col-sm-4">
                                        <input type="text" name ="product_volume" class="form-control" id="inputproduct_volume" placeholder="0.00">
                                    </div>
                                    <label for="inputproduct_volume" class="col-sm-2 col-form-label">ลูกบาศก์เมตร</label>
                                </div>
                                <div class="form-group row">
                                    <label for="inputproduct_images" class="col-sm-2 col-form-label">รูปภาพสินค้า</label>
                                    <div class="col-sm-6">
                                        <div class="fallback">
                                            <input name="file" type="file"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputstock_number" class="col-sm-2 col-form-label">ยอดยกมา</label>
                                    <div class="col-sm-2">
                                        <input type="text" name ="stock_number" class="form-control" id="inputstock_number" placeholder="0">
                                    </div>
                                    <label for="inputstock_place_id" class="col-sm-2 col-form-label">สินค้าเข้าที่</label>
                                    <div class="col-sm-6">
                                        <div class="col-sm-6">
                                            <select class="form-control"  name ="stock_place_id" id="inputstock_place_id">
                                                    <option value= "0">---กรุณาเลือก---</option>
                                                    @foreach ($stock_places as $stock_place)
                                                    <option value="{{ $stock_place['stock_place_id'] }}">{{ $stock_place['stock_place_name'] }}</option>
                                                    @endforeach
                                            </select>
                                        </div>
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
