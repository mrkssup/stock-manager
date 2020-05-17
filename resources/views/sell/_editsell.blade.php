@extends('layouts._master')
@section('main-content')
           <div class="breadcrumb">
                <h1>แก้ไขรายการขาย</h1>
            </div>
            <div class="separator-breadcrumb border-top"></div>

             <!-- ============ form ============= -->
             <div class="row">
                <div class="col-md-12">
                    <div class="card mb-10">
                        <div class="card-body">
                        <form method="POST" class = "main-form" enctype="multipart/form-data" action="{{ url('editsell') }}">
                                {{ csrf_field() }}
                                <div class="form-group row">
                                    <input type="hidden" id="method" name="_method" value="PUT">
                                    <input type="hidden" id="sell_id" name="sell_id" value={{ $sells['sell_id'] }}>
                                    <input type="hidden" id="so_product_id" name="so_product_id" value={{ $sells['so_product_id'] }}>
                                    <label for="inputpurchase_type" class="col-sm-2 col-form-label">ประเภท</label>
                                    <div class="col-sm-6">
                                        <label for="inputsell_type" class="col-sm-4 col-form-label">ขายสินค้าออก</label>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputsell_code" class="col-sm-2 col-form-label">รายการเลขที่</label>
                                    <div class="col-sm-4">
                                        <input type="text"  name ="sell_code" value={{ $sells['sell_code'] }} class="form-control" id="inputsell_code" placeholder="รายการเลขที่" readonly="readonly">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputsell_date" class="col-sm-2 col-form-label">วันที่ทำรายการ</label>
                                    <div class="col-sm-4">
                                        <input type="date" name='sell_date'value={{ $sells['sell_date'] }} class="form-control" id="date" name="date">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputsell_reference" class="col-sm-2 col-form-label">อ้างอิง</label>
                                    <div class="col-sm-4">
                                        <input type="text"  name ="sell_reference"  value={{ $sells['sell_reference'] }} class="form-control" id="inputsell_reference" >
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputsell_detail" class="col-sm-2 col-form-label">รายละเอียดคู่ค้า</label>
                                    <div class="col-sm-4">
                                        <textarea class="form-control" rows = "5" cols = "50" name ="sell_detail" >{{ $sells['sell_detail'] }}</textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputpurchase_code" class="col-sm-2 col-form-label">รายการสินค้า</label>
                                </div>
                                <div class="product-list form-group row">
                                    <label for="inputselect_modal" class="col-sm-1 col-form-label"> <a id ="select-product" data-toggle="modal"  href="#selectproduct"><i class="i-Cursor-Select"></i></a>รหัสสินค้า</label>
                                        <input type="hidden"  name ="product_id" value={{ $sells['product_id'] }} class="form-control" id="inputproduct_id" >
                                    <div class="col-sm-1">
                                        <input type="text"  name ="product_code" value={{ $sells['product_code'] }} class="form-control" id="inputproduct_code" placeholder="รหัสสินค้า" >
                                    </div>
                                    <label for="inputproduct_name" class="col-sm-1 col-form-label">ชื่อสินค้า</label>
                                    <div class="col-sm-2">
                                        <input type="text"  name ="product_name"  value={{ $sells['product_name'] }} class="form-control" id="inputproduct_name" placeholder="ชื่อสินค้า" >
                                    </div>
                                    <label for="inputproduct_number" class="col-sm-1 col-form-label">จำนวน</label>
                                    <div class="col-sm-1">
                                        <input type="text"  name ="product_number" value={{ $sells['product_number'] }} class="form-control" id="inputproduct_number" >
                                    </div>
                                    <label for="inputproduct_price_sell" class="col-sm-1 col-form-label">ราคาขาย</label>
                                    <div class="col-sm-1">
                                        <input type="text"  name ="product_price_sell" value={{ $sells['product_price_sell'] }} class="form-control" id="inputproduct_sell" >
                                    </div>
                                    <label for="inputproduct_total" class="col-sm-1 col-form-label">รวม</label>
                                    <div class="col-sm-2">
                                        <input type="text"  name ="product_total"  value={{ $sells['product_total'] }} class="form-control" id="inputproduct_total" placeholder="รวม" >
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label for="input" class="col-sm-2 col-form-label">โอนสินค้า</label>
                                    <div class="col-sm-4">
                                        <label class="radio radio-primary">
                                            <input type="radio" name="sell_status" value="0" formControlName="radio"  @if( $sells['sell_status'] == 0)checked @endif>
                                            <span>รอโอนสินค้า</span>
                                            <span class="checkmark"></span>
                                        </label>
                                        <label class="radio radio-primary">
                                            <input type="radio" name="sell_status" value="1" formControlName="radio" @if( $sells['sell_status'] == 1)checked @endif>
                                            <span>โอนสินค้าทันที</span>
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="inputstock_place_id" class="col-sm-2 col-form-label">สินค้าเข้าที่</label>
                                        <div class="col-sm-4">
                                            <select class="form-control"  name ="stock_place_id" id="inputstock_place_id">
                                                    <option value= "0">---กรุณาเลือก---</option>
                                                    @foreach ($stock_places as $stock)
                                                        @if ($stock['stock_place_id'] == $sells['sell_stock'])
                                                            <option value= "{{ $stock['stock_place_id'] }}" selected='selected' >{{ $stock['stock_place_name'] }}</option>
                                                        @else
                                                            <option value= "{{ $stock['stock_place_id'] }}">{{ $stock['stock_place_name'] }}</option>
                                                        @endif
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
                {{---------------------------------------------select-product----------------------------------------------------------------}}
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
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                            </div>
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


        $(document).on("click", "#addproduct", function (e) {
        e.preventDefault();
        var _self = $(this);
        var Id = _self.data('id');
        var product_id = Id;
        var data_code ="#data-product-code";
        var data_str_code = data_code.concat(Id);
        var data_name ="#data-product-name";
        var data_str_name = data_name.concat(Id);
        var data_sell ="#data-product-sell";
        var data_str_sell = data_sell.concat(Id);
        console.log(data_str_sell);
        $("#inputproduct_id").val(product_id);
        var product_code = $(data_str_code).text();
        $("#inputproduct_code").val(product_code);
        var product_name = $(data_str_name).text();
        $("#inputproduct_name").val(product_name);
        var product_sell = $(data_str_sell).text();
        $("#inputproduct_sell").val(product_sell);

        });
        $(document).ready(function(){
            $('input').keyup(function(){ // run anytime the value changes
                var firstValue = document.getElementById("inputproduct_number").value
                var secondValue = document.getElementById("inputproduct_sell").value
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
