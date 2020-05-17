@extends('layouts._master')
@section('main-content')
           <div class="breadcrumb">
                <h1>สินค้า</h1>

            </div>
            <div class="separator-breadcrumb border-top"></div>


            <!-- ============ Search ============= -->
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="md-form active-cyan">
                        <form method="POST" action="{{ url('searchproduct') }}">
                        {{ csrf_field() }}
                            <input class="form-control"  type="search"  name ="search" placeholder="ค้นหาสินค้า" aria-label="ค้นหาสินค้า">
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
                                    <h3 class="w-50 float-left card-title m-0">รายการสินค้า</h3>
                                    <div class="text-right w-50 float-right">
                                        <a class="btn btn-primary btn-icon m-1" href="/addproduct" role="button">
                                            <span class="ul-btn__icon"><i class="i-Add"></i></span>
                                            <span class="ul-btn__text">เพิ่มสินค้าใหม่</span>
                                        </a>
                                        <a class="btn btn-success btn-icon m-1" href="/export_product" role="button">
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
                                                        <td>{{ $product['order'] }}</td>
                                                        <td>{{ $product['product_code'] }}</td>
                                                        <td><a href="/product/{{ $product['product_id'] }}">{{ $product['product_name'] }}</a></td>
                                                        <td>
                                                            <img class="rounded-circle m-0 avatar-sm-table " src={{ $product['product_file_server'] }} alt="">
                                                        </td>
                                                        <td>{{ $product['product_price_buy'] }}</td>
                                                        <td>{{ $product['product_price_sell'] }}</td>
                                                        <td>{{ $product['stock_number'] }}</td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <button type="button" class="btn bg-white _r_btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    <span class="_dot _r_block-dot bg-success"></span>
                                                                    <span class="_dot _r_block-dot bg-success"></span>
                                                                    <span class="_dot _r_block-dot bg-success"></span>
                                                                </button>
                                                                <div class="dropdown-menu" x-placement="bottom-start">
                                                                    <a class="dropdown-item" href="/addpurchase/{{ $product['product_id'] }}">ชื้อสินค้า</a>
                                                                    <a class="dropdown-item" href="/addsell/{{ $product['product_id'] }}">ขายสินค้า</a>
                                                                    <a class="dropdown-item" href="/tranfer/{{ $product['product_id'] }}">โอนสินค้า</a>
                                                                    <a class="dropdown-item" href="/adjust/{{ $product['product_id'] }}">ปรับจำนวน</a>
                                                                    <div class="dropdown-divider"></div>
                                                                    <a class="dropdown-item" href="/editproduct/{{ $product['product_id'] }}">แก้ไข</a>
                                                                    <a class="dropdown-item" id ="open-dialog"  data-id={{ $product['product_id'] }}  data-toggle="modal"  href="#deleteModal">ลบ</a>
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

                </div>
                <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalh5">ลบสินค้า</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="deleteproduct">
                                {{ csrf_field() }}
                                <input type="hidden" name="_method" id="delete" value="DELETE" />
                                <input type="hidden" name="product_id" id="deleteid" value="" />
                                <p>คุณแน่ใจแล้วหรือไม่ว่าต้องการลบสินค้าชิ้นนี้</p>
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
    $(document).on("click", "#open-dialog", function (e) {
    e.preventDefault();
    var _self = $(this);
    var Id = _self.data('id');
    $("#deleteid").val(Id);
    // $(_self.attr('href')).modal('show');
    });
</script>

@section('page-js')

     <script src="{{asset('assets/js/vendor/echarts.min.js')}}"></script>
     <script src="{{asset('assets/js/es5/echart.options.min.js')}}"></script>
     <script src="{{asset('assets/js/es5/dashboard.v1.script.js')}}"></script>

@endsection
