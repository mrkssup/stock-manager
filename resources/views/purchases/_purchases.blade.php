@extends('layouts._master')
@section('main-content')
           <div class="breadcrumb">
                <h1>รายการซื้อ</h1>

            </div>
            <div class="separator-breadcrumb border-top"></div>


            <!-- ============ Search ============= -->
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="md-form active-cyan">
                        <form method="POST" action="{{ url('searchpurchase') }}">
                        {{ csrf_field() }}
                            <input class="form-control"  type="search"  name ="search" placeholder="ค้นหาหมวดหมู่" aria-label="ค้นหาหมวดหมู่">
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
                                    <h3 class="w-50 float-left card-title m-0">รายการซื้อ</h3>
                                    <div class="text-right w-50 float-right">
                                        <a class="btn btn-primary btn-icon m-1" href="/addpurchase" role="button">
                                            <span class="ul-btn__icon"><i class="i-Add"></i></span>
                                            <span class="ul-btn__text">เพิ่มรายการซื้อ</span>
                                        </a>
                                        <a class="btn btn-success btn-icon m-1" href="/export_purchase" role="button">
                                            <span class="ul-btn__icon"><i class="i-Add"></i></span>
                                            <span class="ul-btn__text">export to .xslx</span>
                                        </a>
                                    </div>
                                </div>

                                <div class="">
                                    <div class="table-responsive">
                                        <table id="products_table" class="table  text-center">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">รายการ</th>
                                                    <th scope="col">วันที่ทำรายการ</th>
                                                    <th scope="col">รหัสสินค้า</th>
                                                    <th scope="col">มูลค่า</th>
                                                    <th scope="col">สถานะ</th>
                                                    <th scope="col">การจัดการ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($purchases as $purchase)
                                                <tr>
                                                        <td>{{ $purchase['order'] }}</td>
                                                        <td><a href="/purchase/{{ $purchase['purchase_id'] }}">{{ $purchase['purchase_code'] }}</a></td>
                                                        <td>{{ $purchase['purchase_date'] }}</td>
                                                        <td><a href="/product/{{ $purchase['product_id'] }}">{{ $purchase['product_code'] }}</a></td>
                                                        <td>{{ $purchase['purchase_total'] }}</td>
                                                        @if( $purchase['purchase_status_tranfer'] == 1)
                                                            <td>สำเร็จ</td>
                                                            <td>-</td>
                                                        @else
                                                            <td>รอโอนสินค้า</td>
                                                            <td>
                                                                <div class="btn-group">
                                                                    <button type="button" class="btn bg-white _r_btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                        <span class="_dot _r_block-dot bg-success"></span>
                                                                        <span class="_dot _r_block-dot bg-success"></span>
                                                                        <span class="_dot _r_block-dot bg-success"></span>
                                                                    </button>
                                                                    <div class="dropdown-menu" x-placement="bottom-start">
                                                                        <a class="dropdown-item" id ="edit-purchase" data-id={{ $purchase['purchase_id'] }} data-name={{ $purchase['purchase_code'] }} data-stock={{ $purchase['purchase_stock'] }}  data-toggle="modal"  href="#editpurchase">เปลี่ยนสถานะ</a></i>
                                                                        <a class="dropdown-item" href="/editpurchase/{{ $purchase['purchase_id'] }}">แก้ไข</a>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        @endif
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
                <div class="modal fade" id="editpurchase" tabindex="-1" role="dialog" aria-labelledby="editpurchaseLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalh5">โอนสินค้า</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="{{ url('statuspurchase') }}">
                                    {{ csrf_field() }}
                                    <div class="form-group row">
                                        <label for="inputcategory_name" class="col-sm-4 col-form-label">รายการเลขที่</label>
                                        <div class="col-sm-8">
                                            <input type="hidden" id="put" name="_method" value="PUT" />
                                            <input type="hidden" id="purchase_stock"  name="stock_place_id"  class="form-control" value="">
                                            <input type="hidden" id="purchase_id"  name="purchase_id"  class="form-control" value="">
                                            <input type="text" id="purchase_code"  name="purchase_code"  class="form-control" value="">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputcategory_name" class="col-sm-8 col-form-label"> ท่านต้องการโอนสินค้าเข้าคลังที่เลือกไว้ใช่หรือไม่</label>
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
    $(document).on("click", "#edit-purchase", function (e) {
    e.preventDefault();
    var _self = $(this);
    var Id = _self.data('id');
    var Code = _self.data('name');
    var Stock = _self.data('stock');
    $("#purchase_id").val(Id);
    $("#purchase_code").val(Code);
    $("#purchase_stock").val(Stock);
    });
</script>

@section('page-js')

     <script src="{{asset('assets/js/vendor/echarts.min.js')}}"></script>
     <script src="{{asset('assets/js/es5/echart.options.min.js')}}"></script>
     <script src="{{asset('assets/js/es5/dashboard.v1.script.js')}}"></script>

@endsection
