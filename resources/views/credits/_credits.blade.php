@extends('layouts._master')
@section('main-content')
           <div class="breadcrumb">
                <h1>ค่าใช้บริการ</h1>
            </div>
            <div class="separator-breadcrumb border-top"></div>
             <!-- ============ form ============= -->
             <div class="row">
                <div class="col-md-12">
                    <div class="card mb-10">
                        <div class="card-body">
                            <form method="POST"  enctype="multipart/form-data" action="{{ url('postadjust') }}">
                                {{ csrf_field() }}
                                @foreach ($stocks as $stock)
                                    <div class="form-group row">
                                        <label class="font-weight-600 text-primary col-sm-2 col-form-label">{{$stock['stock_place_name']}}</label>
                                        <input type="hidden" name="stock_place_id[]" value={{$stock['stock_place_id']}} class="form-control">
                                    </div>
                                    @foreach ($stock['stock_price'] as $price)
                                        <div class="form-group row">
                                            <label for="inputstock_places_name" class="col-sm-1 col-form-label">ชื่อสินค้า</label>
                                            <div class="col-sm-2">
                                                <input type="text" name="product_name[]" value="{{$price['product_name']}}"  class="form-control" i readonly="readonly">
                                            </div>
                                            <label for="inputstock_places_name" class="col-sm-1 col-form-label">ปริมาตร</label>
                                            <div class="col-sm-1">
                                                <input type="text" name="product_volume[]" value="{{$price['product_volume']}}"class="form-control" id="inputtranfer_code" placeholder="0" readonly="readonly">
                                            </div>
                                            <label for="inputstock_places_name" class="col-sm-1 col-form-label">จำนวน</label>
                                            <div class="col-sm-1">
                                                <input type="text" name="stock_number[]" value="{{$price['stock_number']}}"class="form-control" id="inputtranfer_code" placeholder="0" readonly="readonly">
                                            </div>
                                            <label for="inputstock_places_name" class="col-sm-1 col-form-label">จำนวนวันที่เก็บ(วัน)</label>
                                            <div class="col-sm-1">
                                                <input type="text" name="stock_number[]" value="{{$price['days']}}"class="form-control" id="inputtranfer_code" placeholder="0" readonly="readonly">
                                            </div>
                                            <label for="inputstock_places_name" class="col-sm-1 col-form-label">รวม(บาท)</label>
                                            <div class="col-sm-1">
                                                <input type="text" name="stock_number[]" value="{{$price['price']}}"class="form-control" id="inputtranfer_code" placeholder="0" readonly="readonly">
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="form-group row">
                                        <label class="font-weight-600 text-primary col-sm-2 col-form-label">ราคารวมต่อคลัง(บาท)</label>
                                        <div class="col-sm-1">
                                        <input type="text" name="stock_place_id[]" value={{$stock['stock_all_price']}} class="form-control">
                                        </div>
                                    </div>
                                @endforeach
                                <div class="form-group row">
                                    <label class="font-weight-600 text-primary col-sm-2 col-form-label">ราคารวมทั้งหมด(บาท)</label>
                                    <div class="col-sm-1">
                                    <input type="text" name="sum_all_price" value={{$sum_all_price}} class="form-control" readonly="readonly">
                                    </div>
                                </div>
                            </form>
                                <div class="form-group row">
                                    <div class="col-sm-10">
                                        <a class="btn btn-primary" href="{{ URL::previous() }}">ย้อนกลับ</a>
                                    </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card mb-10">
                        <div class="card-body">
                            <form name="checkoutForm" method="POST" action="{{ url('cash') }}">
                                <script type="text/javascript" src="https://cdn.omise.co/omise.js"
                                  data-key="pkey_test_5jwn5xg9pehqxeo3f0g"
                                  data-image="{{asset('assets/images/stock-manager.png')}}"
                                  data-frame-label="Stock-manager"
                                  data-button-label="ชำระเงิน"
                                  data-submit-label="Submit"
                                  data-location="no"
                                  data-amount="{{$sum_all_price}}00"
                                  data-currency="thb"
                                  >
                                </script>
                                <!--the script will render <input type="hidden" name="omiseToken"> for you automatically-->
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
