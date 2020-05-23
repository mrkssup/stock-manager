@extends('layouts._master')
@section('main-content')
           <div class="breadcrumb">
                <h1>รายการขาย</h1>

            </div>
            <div class="separator-breadcrumb border-top"></div>


            <!-- ============ Search ============= -->
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="md-form active-cyan">
                        <form method="POST" action="{{ url('searchsell') }}">
                        {{ csrf_field() }}
                            <input class="form-control"  type="search"  name ="search" placeholder="ค้นหารายการขาย" aria-label="ค้นหารายการขาย">
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
                                    <h3 class="w-50 float-left card-title m-0">รายการขาย</h3>
                                    <div class="text-right w-50 float-right">
                                        <a class="btn btn-primary btn-icon m-1" href="/addsell" role="button">
                                            <span class="ul-btn__icon"><i class="i-Add"></i></span>
                                            <span class="ul-btn__text">เพิ่มรายการขาย</span>
                                        </a>
                                        <a class="btn btn-success btn-icon m-1" href="/export_sell" role="button">
                                            <span class="ul-btn__icon"><i class="i-Add"></i></span>
                                            <span class="ul-btn__text">export to .xslx</span>
                                        </a>
                                    </div>
                                </div>

                                <div class="">
                                    <div class="table-responsive">
                                        <table id="products_table" class="table">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">รายการ</th>
                                                    <th scope="col">วันที่ทำรายการ</th>
                                                    <th scope="col">รหัสสินค้า</th>
                                                    <th scope="col">มูลค่า</th>
                                                    <th scope="col">สถานะ</th>
                                                    <th scope="col">Tracking number</th>
                                                    <th scope="col">การจัดการ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($sells as $sell)
                                                <tr>
                                                        <td>{{ $sell['order'] }}</td>
                                                        <td><a href="/sell/{{ $sell['sell_id'] }}">{{ $sell['sell_code'] }}</a></td>
                                                        <td>{{ $sell['sell_date'] }}</td>
                                                        <td><a href="/product/{{ $sell['product_id'] }}">{{ $sell['product_code'] }}</a></td>
                                                        <td>{{ $sell['sell_total'] }}</td>
                                                        @if( $sell['sell_status'] == 2)
                                                            <td>สำเร็จ</td>
                                                            <td><div class="as-track-button col-md-1" data-size="small" data-domain="stmanage.aftership.com" data-tracking-number={{ $sell['tracking_number'] }}></div></td>
                                                            <td>-</td>
                                                        @elseif( $sell['sell_status'] == 1)
                                                            <td>รอโอนสินค้า</td>
                                                            <td>-</td>
                                                            <td>-</td>
                                                        @elseif( $sell['sell_status'] == 9)
                                                            <td>ยกเลิก</td>
                                                            <td>-</td>
                                                            <td>-</td>
                                                        @else
                                                            <td>กำลังดำเนินการ</td>
                                                            <td>-</td>
                                                            <td>
                                                                <div class="btn-group">
                                                                    <button type="button" class="btn bg-white _r_btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                        <span class="_dot _r_block-dot bg-success"></span>
                                                                        <span class="_dot _r_block-dot bg-success"></span>
                                                                        <span class="_dot _r_block-dot bg-success"></span>
                                                                    </button>
                                                                    <div class="dropdown-menu" x-placement="bottom-start">
                                                                        <a class="dropdown-item" id ="edit-sell" data-id={{ $sell['sell_id'] }} data-name={{ $sell['sell_code'] }} data-stock={{ $sell['sell_stock'] }}  data-toggle="modal"  href="#editsell">โอนสินค้าออก</a></i>
                                                                        <a class="dropdown-item" id ="cancel-sell" data-id={{ $sell['sell_id'] }} data-name={{ $sell['sell_code'] }} data-stock={{ $sell['sell_stock'] }}  data-toggle="modal"  href="#cancelsell">ยกเลิก</a></i>
                                                                        <a class="dropdown-item" href="/editsell/{{ $sell['sell_id'] }}">แก้ไข</a>
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
                 {{---------------------------------------------cancelpurchase----------------------------------------------------------------}}
                 <div class="modal fade" id="cancelsell" tabindex="-1" role="dialog" aria-labelledby="cancelsellLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalh5">ยกเลิกรายการขายสินค้า</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="{{ url('cancelsell') }}">
                                    {{ csrf_field() }}
                                    <div class="form-group row">
                                        <label for="inputsell_code_name" class="col-sm-4 col-form-label">รายการเลขที่</label>
                                        <div class="col-sm-8">
                                            <input type="hidden" id="put" name="_method" value="PUT" />
                                            <input type="hidden" id="cancell_sell_stock"  name="stock_place_id"  class="form-control" value="">
                                            <input type="hidden" id="cancel_sell_id"  name="sell_id"  class="form-control" value="">
                                            <input type="text" id="cancel_sell_code"  name="sell_code"  class="form-control" value="">
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <label for="inputcategory_name" class="col-sm-8 col-form-label"> ท่านต้องการยกเลิกรายการขายสินค้าที่เลือกใช่หรือไม่</label>
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
<div id="as-root"></div><script>(function(e,t,n){var r,i=e.getElementsByTagName(t)[0];if(e.getElementById(n))return;r=e.createElement(t);r.id=n;r.src="https://button.aftership.com/all.js";i.parentNode.insertBefore(r,i)})(document,"script","aftership-jssdk")</script>
<script>
    var msg = '{{Session::get('alert')}}';
    var exist = '{{Session::has('alert')}}';
    if(exist){
      alert(msg);
    }
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).on("click", "#edit-sell", function (e) {
    e.preventDefault();
    var _self = $(this);
    var Id = _self.data('id');
    var Code = _self.data('name');
    var Stock = _self.data('stock');
    $("#sell_id").val(Id);
    $("#sell_code").val(Code);
    $("#sell_stock").val(Stock);
    });
</script>
<script>
    $(document).on("click", "#cancel-sell", function (e) {
    e.preventDefault();
    var _self = $(this);
    var Id = _self.data('id');
    var Code = _self.data('name');
    var Stock = _self.data('stock');
    $("#cancel_sell_id").val(Id);
    $("#cancel_sell_code").val(Code);
    $("#cancel_sell_stock").val(Stock);
    });
</script>

@section('page-js')

     <script src="{{asset('assets/js/vendor/echarts.min.js')}}"></script>
     <script src="{{asset('assets/js/es5/echart.options.min.js')}}"></script>
     <script src="{{asset('assets/js/es5/dashboard.v1.script.js')}}"></script>

@endsection
