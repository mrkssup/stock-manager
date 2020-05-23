<div class="side-content-wrap">
    <div class="sidebar-left open rtl-ps-none" data-perfect-scrollbar data-suppress-scroll-x="true">
        <ul class="navigation-left">
            <li class="nav-item">
                <a class="nav-item-hold" href="/dashboard">
                    <i class="nav-icon i-Bar-Chart"></i>
                    <span class="nav-text">ภาพรวม</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item active" data-item="sales">
                <a class="nav-item-hold" href="#">
                    <i class="nav-icon i-Tag-2"></i>
                    <span class="nav-text">รายการขาย</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item active" data-item="buys">
                <a class="nav-item-hold" href="#">
                    <i class="nav-icon i-Add-Cart"></i>
                    <span class="nav-text">รายการซื้อ</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item active" data-item="products">
                <a class="nav-item-hold" href="#">
                    <i class="nav-icon i-Shop-2"></i>
                    <span class="nav-text">สินค้า</span>
                </a>
                <div class="triangle"></div>
            </li>
            <li class="nav-item active" data-item="finance">
                <a class="nav-item-hold" href="#">
                    <i class="nav-icon i-Cash-register-2"></i>
                    <span class="nav-text">การเงิน</span>
                </a>
                <div class="triangle"></div>
            </li>
        </ul>
    </div>

    <div class="sidebar-left-secondary rtl-ps-none" data-perfect-scrollbar data-suppress-scroll-x="true">
        <!-- Submenu Dashboards -->
        <ul class="childNav" data-parent="sales">
            <li class="nav-item ">
                <a  href="{{ route('sells')}}">
                    <span class="item-name">ดูรายการขาย</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/addsell">
                    <span class="item-name">สร้างรายการขาย</span>
                </a>
            </li>
            {{-- <li class="nav-item">
                <a href="#">
                    <span class="item-name">รายการขนส่งสินค้า</span>
                </a>
            </li> --}}
        </ul>
        <ul class="childNav" data-parent="buys">

            <li class="nav-item">
                <a  href="{{route('purchases')}}">
                    <span class="item-name">ดูรายการซื้อ</span>
                </a>
            </li>

            <li class="nav-item">
                <a  href="/addpurchase">
                    <span class="item-name">สร้างรายการซื้อ</span>
                </a>
            </li>
        </ul>
        <ul class="childNav" data-parent="products">
            <li class="nav-item">
                <a  href="{{route('products')}}">
                    <span class="item-name">รายการสินค้า</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{route('stocks')}}">
                    <span class="item-name">คลังสินค้า</span>
                </a>
            </li>
            <li class="nav-item">
                <a  href="{{route('category')}}">
                    <span class="item-name">หมวดหมู่</span>
                </a>
            </li>
        </ul>

        <ul class="childNav" data-parent="finance">
            <li class="nav-item">
                <a  href="{{route('credits')}}">
                    <span class="item-name">ชำระค่าบริการ</span>
                </a>
            </li>
        </ul>
    </div>
    <div class="sidebar-overlay"></div>
</div>
<!--=============== Left side End ================-->
