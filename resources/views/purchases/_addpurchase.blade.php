@extends('layouts._master')
@section('main-content')
           <div class="breadcrumb">
                <h1>เพิ่มรายการซื้อ</h1>
            </div>
            <div class="separator-breadcrumb border-top"></div>

             <!-- ============ form ============= -->
             <div class="row">
                <div class="col-md-12">
                    <div class="card mb-10">
                        <div class="card-body">
                        <form method="POST" class = "main-form" enctype="multipart/form-data" action="{{ url('postpurchase') }}">
                                {{ csrf_field() }}
                                <div class="form-group row">
                                    <label for="inputpurchase_type" class="col-sm-2 col-form-label">ประเภท</label>
                                    <div class="col-sm-6">
                                        <label for="inputpurchase_type" class="col-sm-4 col-form-label">ซื้อสินค้าเข้า</label>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputpurchase_code" class="col-sm-2 col-form-label">รายการเลขที่</label>
                                    <div class="col-sm-4">
                                        <input type="text"  name ="purchase_code" value={{ $purchase_code['purchase_code'] }} class="form-control" id="inputpurchase_code" placeholder="รายการเลขที่" readonly="readonly">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputpurchase_date" class="col-sm-2 col-form-label">วันที่ทำรายการ</label>
                                    <div class="col-sm-4">
                                        <input type="date" name='purchase_date'value={{ $today['today'] }} class="form-control" id="date" name="date">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputpurchase_reference" class="col-sm-2 col-form-label">อ้างอิง</label>
                                    <div class="col-sm-4">
                                        <input type="text"  name ="purchase_reference" class="form-control" id="inputpurchase_reference" >
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="customer_name" class="col-sm-2 col-form-label"><a id ="select-customer" data-toggle="modal"  href="#selectcustomer"><i class="customer i-Cursor-Select"></i></a>ชื่อลูกค้า</label>
                                    <div class="col-sm-4">
                                        <input type="hidden"  name ="customer_id"  value=''  class="form-control" id="inputcustomer_id" >
                                        <input type="text"  name ="customer_name" value='' class="form-control" id="inputcustomer_name" >
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputcustomer_detail" class="col-sm-2 col-form-label">รายละเอียดลูกค้า</label>
                                    <div class="col-sm-4">
                                        <textarea class="form-control" rows = "5" cols = "50" name = "customer_detail" id="inputcustomer_detail"></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputpurchase_code" class="col-sm-2 col-form-label">รายการสินค้า</label>
                                </div>
                                <div class="product-list form-group row">

                                    <label for="inputselect_modal" class="col-sm-1 col-form-label"> <a id ="select-product" data-toggle="modal"  href="#selectproduct"><i class="i-Cursor-Select"></i></a>รหัสสินค้า</label>
                                        <input type="hidden"  name ="product_id" @if( !empty($get_product['product_id'])) value={{ $get_product['product_id'] }} @endif class="form-control" id="inputproduct_id" >
                                    <div class="col-sm-1">
                                        <input type="text"  name ="product_code" @if( !empty($get_product['product_code'])) value={{ $get_product['product_code'] }} @endif class="form-control" id="inputproduct_code" placeholder="รหัสสินค้า" >
                                    </div>
                                    <label for="inputproduct_name" class="col-sm-1 col-form-label">ชื่อสินค้า</label>
                                    <div class="col-sm-2">
                                        <input type="text"  name ="product_name" @if( !empty($get_product['product_name'])) value={{ $get_product['product_name'] }} @endif class="form-control" id="inputproduct_name" placeholder="ชื่อสินค้า" >
                                    </div>
                                    <label for="inputproduct_number" class="col-sm-1 col-form-label">จำนวน</label>
                                    <div class="col-sm-1">
                                        <input type="text"  name ="product_number"  class="form-control" id="inputproduct_number" required>
                                    </div>
                                    <label for="inputproduct_price_buy" class="col-sm-1 col-form-label">มูลค่าต่อหน่วย</label>
                                    <div class="col-sm-1">
                                        <input type="text"  name ="product_price_buy" @if( !empty($get_product['product_price_buy'])) value={{ $get_product['product_price_buy'] }} @endif class="form-control" id="inputproduct_buy" >
                                    </div>
                                    <label for="inputproduct_total" class="col-sm-1 col-form-label">รวม</label>
                                    <div class="col-sm-2">
                                        <input type="text"  name ="product_total"  class="form-control" id="inputproduct_total" placeholder="รวม" >
                                    </div>
                                </div>

                                {{-- <div class="form-group row">
                                    <label for="input" class="col-sm-2 col-form-label">โอนสินค้า</label>
                                    <div class="col-sm-4">
                                        <label class="radio radio-primary">
                                            <input type="radio" name="purchase_status_tranfer" value="0" formControlName="radio">
                                            <span>รอโอนสินค้า</span>
                                            <span class="checkmark"></span>
                                        </label>
                                        <label class="radio radio-primary">
                                            <input type="radio" name="purchase_status_tranfer" value="1" formControlName="radio">
                                            <span>โอนสินค้าทันที</span>
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                </div> --}}
                                <div class="form-group row">
                                    <label for="inputstock_place_id" class="col-sm-2 col-form-label">สินค้าเข้าที่</label>
                                        <div class="col-sm-4">
                                            <select class="form-control"  name ="stock_place_id" id="inputstock_place_id">
                                                    <option value= "0">---กรุณาเลือก---</option>
                                                    @foreach ($stocks as $stock)
                                                    <option value="{{ $stock['stock_place_id'] }}">{{ $stock['stock_place_name'] }}</option>
                                                    @endforeach
                                            </select>
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
                 {{-------------------------------------------select-product--------------------------------------------------------------}}
                 <div class="modal fade bd-modal-lg" id="selectproduct" tabindex="-1" role="dialog" aria-labelledby="selectproductLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalh5">เลือกสินค้า</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card o-hidden mb-4">
                                                    <div class="card-header d-flex align-items-center border-0">
                                                        <h3 class="w-50 float-left card-title m-0">รายการสินค้า</h3>
                                                    </div>

                                                    <div class="">
                                                        <div class="table-responsive">
                                                            <table id="products_table" class="table  text-center">
                                                                <thead>
                                                                    <tr>
                                                                        <th scope="col">#</th>
                                                                        <th scope="col">รหัสสินค้า</th>
                                                                        <th scope="col">ชื่อสินค้า</th>
                                                                        <th scope="col">รูปสินค้า</th>
                                                                        <th scope="col">ราคาซื้อ</th>
                                                                        <th scope="col">ราคาขาย</th>
                                                                        <th scope="col">ยอดคงเหลือ</th>
                                                                        <th scope="col">การจัดการ</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($products as $product)
                                                                    <tr>
                                                                    <td><p id="data-product-id{{ $product['product_id'] }}">{{ $product['product_id'] }}</p></td>
                                                                            <td><p id="data-product-code{{ $product['product_id'] }}">{{ $product['product_code'] }}</p></td>
                                                                            <td><a href="/product/{{ $product['product_id'] }}"><p id ="data-product-name{{ $product['product_id'] }}">{{ $product['product_name'] }}</p></a></td>
                                                                            <td>
                                                                                <img class="rounded-circle m-0 avatar-sm-table " src={{ $product['product_file_server'] }} alt="">
                                                                            </td>
                                                                            <td><p id ="data-product-buy{{ $product['product_id'] }}">{{ $product['product_price_buy'] }}</p></td>
                                                                            <td><p id ="data-product-sell{{ $product['product_id'] }}">{{ $product['product_price_sell'] }}</p></td>
                                                                            <td><p id ="data-product-number">{{ $product['stock_number'] }}<p></td>
                                                                            <td>
                                                                                <button type="submit" id ="addproduct" data-id={{ $product['product_id']}} class="add-product btn btn-primary" data-dismiss="modal">เลือก</button>
                                                                            </td>
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
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-------------------------------------------select-customer--------------------------------------------------------------}}
            <div class="modal fade bd-modal-lg" id="selectcustomer" tabindex="-1" role="dialog" aria-labelledby="selectcustomerLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalh5">เลือกสินค้า</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 col-md-12">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="card o-hidden mb-4">
                                                <div class="card-header d-flex align-items-center border-0">
                                                    <h3 class="w-50 float-left card-title m-0">รายการสินค้า</h3>
                                                </div>
                                                <div class="">
                                                    <div class="table-responsive">
                                                        <table id="products_table" class="table  text-center">
                                                            <thead>
                                                                <tr>
                                                                    <th scope="col">#</th>
                                                                    <th scope="col">ชื่อลูกค้า</th>
                                                                    <th scope="col">รายละเอียดลูกค้า</th>
                                                                    <th scope="col">การจัดการ</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($customers as $customer)
                                                                <tr>
                                                                <td><p id="data-product-id{{ $customer['customer_id'] }}">{{ $customer['customer_id'] }}</p></td>
                                                                        <td><p id="data-customer-name{{ $customer['customer_id'] }}">{{ $customer['customer_name'] }}</p></td>
                                                                        <td><p id ="data-customer-detail{{ $customer['customer_id'] }}">{{ $customer['customer_detail'] }}</p></td>
                                                                        <td>
                                                                            <button type="submit" id ="addcustomer" data-id={{ $customer['customer_id']}} class="add-customer btn btn-primary" data-dismiss="modal">เลือก</button>
                                                                        </td>
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
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
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
<script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>

<script>
    $(document).ready(function(){
        $('#but_add').click(function(){
        // Create clone of <div class='input-form'>
        var newel = $('.product-list:last').clone();
        // Add after last <div class='input-form'>
        $(newel).insertAfter(".product-list:last");

    });
    $("#but_remove").click(function(e) {
        var newer = $('.product-list:last').remove();
        // $(this).closest(".product-list").remove();
        e.preventDefault();
    });


        $(document).on("click", "#addcustomer", function (e) {
            e.preventDefault();
            var _self = $(this);
            var Id = _self.data('id');
            var customer_id = Id;
            var data_name ="#data-customer-name";
            var data_str_name = data_name.concat(Id);
            var data_detail ="#data-customer-detail";
            var data_str_detail = data_detail.concat(Id);
            $("#inputcustomer_id").val(customer_id);
            var customer_name = $(data_str_name).text();
            $("#inputcustomer_name").val(customer_name);
            var customer_detail = $(data_str_detail).text();
            $("#inputcustomer_detail").val(customer_detail);
        });


        $(document).on("click", "#addproduct", function (e) {
        e.preventDefault();
        var _self = $(this);
        var Id = _self.data('id');
        var product_id = Id;
        var data_code ="#data-product-code";
        var data_str_code = data_code.concat(Id);
        var data_name ="#data-product-name";
        var data_str_name = data_name.concat(Id);
        var data_buy ="#data-product-buy";
        var data_str_buy = data_buy.concat(Id);
        $("#inputproduct_id").val(product_id);
        var product_code = $(data_str_code).text();
        $("#inputproduct_code").val(product_code);
        var product_name = $(data_str_name).text();
        $("#inputproduct_name").val(product_name);
        var product_buy = $(data_str_buy).text();
        $("#inputproduct_buy").val(product_buy);

        });
        $(document).ready(function(){
            $('input').keyup(function(){ // run anytime the value changes
                var firstValue = document.getElementById("inputproduct_number").value
                var secondValue = document.getElementById("inputproduct_buy").value
                var total = Number(firstValue) * Number(secondValue);
                document.getElementById('inputproduct_total').value = total;

            });
        });




});
</script>
<script type="text/javascript">
    function show() { document.getElementById('area').style.display = 'block'; }
    function hide() { document.getElementById('area').style.display = 'none'; }
</script>




@section('page-js')

     <script src="{{asset('assets/js/vendor/echarts.min.js')}}"></script>
     <script src="{{asset('assets/js/es5/echart.options.min.js')}}"></script>
     <script src="{{asset('assets/js/es5/dashboard.v1.script.js')}}"></script>

@endsection
