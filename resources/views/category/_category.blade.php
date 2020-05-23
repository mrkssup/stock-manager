@extends('layouts._master')
@section('main-content')
           <div class="breadcrumb">
                <h1>หมวดหมู่</h1>

            </div>
            <div class="separator-breadcrumb border-top"></div>


            <!-- ============ Search ============= -->
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="md-form active-cyan">
                        <form method="POST" action="{{ url('searchcategory') }}">
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
                                    <h3 class="w-50 float-left card-title m-0">รายการหมวดหมู่</h3>
                                    <div class="text-right w-50 float-right">
                                        <a class="btn btn-primary btn-icon m-1" id ="add-category" data-toggle="modal"  href="#addcategory" role="button">
                                            <span class="ul-btn__icon"><i class="i-Add"></i></span>
                                            <span class="ul-btn__text">เพิ่มหมวดหมู่</span>
                                        </a>
                                    </div>
                                </div>

                                <div class="">
                                    <div class="table-responsive">
                                        <table id="products_table" class="table  text-center">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">ชื่อหมวดหมู่</th>
                                                    <th scope="col">จำนวนสินค้าคงเหลือ</th>
                                                    <th scope="col">การจัดการ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($category as $cat)
                                                <tr>
                                                        <td>{{ $cat['order'] }}</td>
                                                        <td><a href="/products?category={{ $cat['category_id'] }}">{{ $cat['category_name'] }}</a></td>
                                                        <td>{{ $cat['stock_number'] }}</td>
                                                        <td>
                                                            <div class="btn-group">
                                                                <button type="button" class="btn bg-white _r_btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    <span class="_dot _r_block-dot bg-success"></span>
                                                                    <span class="_dot _r_block-dot bg-success"></span>
                                                                    <span class="_dot _r_block-dot bg-success"></span>
                                                                </button>
                                                                <div class="dropdown-menu" x-placement="bottom-start">
                                                                    <a class="dropdown-item" id ="edit-category" data-id={{ $cat['category_id'] }} data-name={{ $cat['category_name'] }}  data-toggle="modal"  href="#editcategory">แก้ไข</a>
                                                                    <a class="dropdown-item" id ="delete-category" data-id={{ $cat['category_id'] }} data-toggle="modal"  href="#deletecategory">ลบ</a>
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
                {{---------------------------------------------addcategory----------------------------------------------------------------}}
                <div class="modal fade" id="addcategory" tabindex="-1" role="dialog" aria-labelledby="addcategoryLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalh5">เพิ่มหมวดหมู่</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="addcategory">
                                {{ csrf_field() }}
                                <div class="form-group row">
                                    <label for="inputcategory_name" class="col-sm-4 col-form-label">ชื่อหมวดหมู่</label>
                                    <div class="col-sm-8">
                                        <input type="text" name ="category_name" class="form-control" id="inputcategory_name" placeholder="ชื่อหมวดหมู่">
                                    </div>
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
                {{---------------------------------------------editcategory----------------------------------------------------------------}}
                <div class="modal fade" id="editcategory" tabindex="-1" role="dialog" aria-labelledby="editcategoryLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalh5">แก้ไขหมวดหมู่</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="editcategory">
                                    {{ csrf_field() }}
                                    <div class="form-group row">
                                        <label for="inputcategory_name" class="col-sm-4 col-form-label">ชื่อหมวดหมู่</label>
                                        <div class="col-sm-8">
                                            <input type="hidden" id="put" name="_method" value="PUT" />
                                            <input type="hidden" id="category_id"  name="category_id"  class="form-control" value="">
                                            <input type="text" id="category_name" name="category_name" class="form-control" id="inputcategory_name" placeholder="ชื่อหมวดหมู่" value="">
                                        </div>
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
                {{---------------------------------------------deletecategory----------------------------------------------------------------}}
                <div class="modal fade" id="deletecategory" tabindex="-1" role="dialog" aria-labelledby="editcategoryLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalh5">ลบหมวดหมู่</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="deletecategory">
                                {{ csrf_field() }}
                                <input type="hidden" name="_method" id="delete" value="DELETE" />
                                <input type="hidden" id="delete_id" name="category_id"  value="" />
                                <p>คุณแน่ใจแล้วหรือไม่ว่าต้องการลบหมวดหมู่นี้</p>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).on("click", "#edit-category", function (e) {
    e.preventDefault();
    var _self = $(this);
    var Id = _self.data('id');
    var Name = _self.data('name');
    $("#category_id").val(Id);
    $("#category_name").val(Name);
    // $(_self.attr('href')).modal('show');
    });
</script>
<script>
    $(document).on("click", "#delete-category", function (e) {
    e.preventDefault();
    var _self = $(this);
    var Id = _self.data('id');
    console.log(Id);
    $("#delete_id").val(Id);
    // $(_self.attr('href')).modal('show');
    });
</script>

@section('page-js')

     <script src="{{asset('assets/js/vendor/echarts.min.js')}}"></script>
     <script src="{{asset('assets/js/es5/echart.options.min.js')}}"></script>
     <script src="{{asset('assets/js/es5/dashboard.v1.script.js')}}"></script>

@endsection
