@extends('layouts._master')
@section('main-content')
           <div class="breadcrumb">
                <h1>รายการซื้อรอดำเนินการ</h1>

            </div>
            <div class="separator-breadcrumb border-top"></div>


            <!-- ============ Search ============= -->
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="md-form active-cyan">
                        <form method="POST" action="{{ url('searchadminbuy') }}">
                        {{ csrf_field() }}
                            <input class="form-control"  type="search"  name ="search" placeholder="ค้นหารายการซื้อ" aria-label="ค้นหารายการซื้อ">
                    </div>
                </div>
                <div class="col-lg-2 col-md-2">
                    <div class="md-form active-cyan">
                        <button type="submit" class="btn btn-primary">ค้นหา</button>
                        </form>
                    </div>
                </div>
            </div>

             <!-- ============ table ============= -->
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card o-hidden mb-4">
                                <div class="card-header d-flex align-items-center border-0">
                                    <h3 class="w-50 float-left card-title m-0">รายการซื้อรอดำเนินการ</h3>
                                    {{-- <div class="text-right w-50 float-right">
                                        <a class="btn btn-success btn-icon m-1" href="/export_adminsell" role="button">
                                            <span class="ul-btn__icon"><i class="i-Add"></i></span>
                                            <span class="ul-btn__text">export to .xslx</span>
                                        </a>
                                    </div> --}}
                                </div>

                                <div class="">
                                    <div class="table-responsive">
                                        <table id="adminsell_table" class="table  text-center">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">รายการเลขที่</th>
                                                    <th scope="col">รหัสสินค้า</th>
                                                    <th scope="col">จำนวน</th>
                                                    <th scope="col">ผู้ขอทำรายการ</th>
                                                    <th scope="col">สินค้าเข้าที่</th>
                                                    <th scope="col">วันที่ทำรายการ</th>
                                                    <th scope="col">การจัดการ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($purchases as $purchase)
                                                <tr>
                                                        <td>{{ $purchase['order'] }}</td>
                                                        <td>{{ $purchase['purchase_code'] }}</td>
                                                        <td>{{ $purchase['product_code'] }}</td>
                                                        <td>{{ $purchase['product_number'] }}</td>
                                                        <td>{{ $purchase['full_name'] }}</td>
                                                        <td>{{ $purchase['stock_place_name'] }}</td>
                                                        <td>{{ $purchase['purchase_date'] }}</td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <button type="button" class="btn bg-white _r_btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    <span class="_dot _r_block-dot bg-success"></span>
                                                                    <span class="_dot _r_block-dot bg-success"></span>
                                                                    <span class="_dot _r_block-dot bg-success"></span>
                                                                </button>
                                                                <div class="dropdown-menu" x-placement="bottom-start">
                                                                    <a class="dropdown-item" href="/admin/po/{{ $purchase['purchase_id'] }}">จัดซื้อสินค้า</a></i>
                                                                    {{-- <a class="dropdown-item" id ="print-lebel" data-id={{ $sell['sell_id'] }} data-name={{ $sell['customer_name'] }} data-detail={{ $sell['customer_detail'] }} data-number={{ $sell['product_number'] }} data-toggle="modal"  href="#printlabel">พิมใบแปะกล่อง</a></i> --}}
                                                                </div>
                                                            </div>
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
                    {{---------------------------------------------editpurchase----------------------------------------------------------------}}
                <div class="modal fade" id="editsell" tabindex="-1" role="dialog" aria-labelledby="editsellLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalh5">โอนสินค้าออก</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="{{ url('statussell') }}">
                                    {{ csrf_field() }}
                                    <div class="form-group row">
                                        <label for="inputsell_code_name" class="col-sm-4 col-form-label">รายการเลขที่</label>
                                        <div class="col-sm-8">
                                            <input type="hidden" id="put" name="_method" value="PUT" />
                                            <input type="hidden" id="sell_stock"  name="stock_place_id"  class="form-control" value="">
                                            <input type="hidden" id="sell_id"  name="sell_id"  class="form-control" value="">
                                            <input type="text" id="sell_code"  name="sell_code"  class="form-control" value="">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputcategory_name" class="col-sm-8 col-form-label"> ท่านต้องการโอนสินค้าออกจากคลังที่เลือกไว้ใช่หรือไม่</label>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                                <button type="submit" class="btn btn-primary">ยืนยัน</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                 {{---------------------------------------------printlebel----------------------------------------------------------------}}
                 <div class="modal fade" id="printlabel" tabindex="-1" role="dialog" aria-labelledby="printLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalh5">ปริ้นใบแปะกล่องสินค้า</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="{{ url('printlebel') }}">
                                    {{ csrf_field() }}
                                    <div class="form-group row">
                                        <label for="inputsell_code_name" class="col-sm-4 col-form-label">ชื่อลูกค้า</label>
                                        <div class="col-sm-8">
                                            <input type="hidden" id="print_sell_id"  name="sell_id"  class="form-control" value="">
                                            <input type="text" id="print_customer_name"  name="customer_name"  class="form-control" value="">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputsell_code_name" class="col-sm-4 col-form-label">รายละเอียดลูกค้า</label>
                                        <div class="col-sm-8">
                                            <textarea  id="print_customer_detail" class="form-control" rows = "5" cols = "50" name = "customer_detail"></textarea>
                                            {{-- <input type="text" id="print_customer_detail"  name="customer_detail"  class="form-control" value=""> --}}
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputsellproduct_number" class="col-sm-4 col-form-label">จำนวน</label>
                                        <div class="col-sm-2">
                                            <input type="text" id="print_product_number"  name="product_number"  class="form-control" value="">
                                        </div>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                                <button type="submit" class="btn btn-primary">ปริ้น</button>
                                </form>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).on("click", "#print-lebel", function (e) {
    e.preventDefault();
    var _self = $(this);
    var Id = _self.data('id');
    var Name = _self.data('name');
    var Detail = _self.data('detail');
    var pNumber = _self.data('number');
    $("#print_sell_id").val(Id);
    $("#print_customer_name").val(Name);
    $("#print_customer_detail").val(Detail);
    $("#print_product_number").val(pNumber);
    });
</script>

@section('page-js')

     <script src="{{asset('assets/js/vendor/echarts.min.js')}}"></script>
     <script src="{{asset('assets/js/es5/echart.options.min.js')}}"></script>
     <script src="{{asset('assets/js/es5/dashboard.v1.script.js')}}"></script>

@endsection
