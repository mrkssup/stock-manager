<?php
use App\Http\Controllers\NotifyController;
$notify = NotifyController::notify();
$notify_count = NotifyController::count();
?>
<div class="main-header">
            <div class="logo">
                <img src="{{asset('assets/images/stock-manager.png')}}" alt="">
            </div>

            <div class="menu-toggle">
                <div></div>
                        <div></div>
                <div></div>
            </div>

            <div style="margin: auto"></div>

            <div class="header-part-right">
                <!-- Full screen toggle -->
                <i class="i-Full-Screen header-icon d-none d-sm-inline-block" data-fullscreen></i>
                <!-- Notificaiton -->
                <div class="dropdown">
                    <div class="badge-top-container" role="button" id="dropdownNotification" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                        <?php if( $notify_count > 0){ echo '<span class="badge badge-primary">'.$notify_count .'</span>';}  ?>
                        <i class="i-Bell text-muted header-icon"></i>
                    </div>
                    <!-- Notification dropdown -->
                    <div class="dropdown-menu dropdown-menu-right notification-dropdown rtl-ps-none" aria-labelledby="dropdownNotification" data-perfect-scrollbar data-suppress-scroll-x="true">
                        <div class="dropdown-item d-flex">
                            <div class="notification-details flex-grow-1">
                                <p class="m-0 d-flex align-items-center">
                                    <?php foreach($notify as $noti){echo '<p class="text-small text-muted m-0">'.$noti['data'].'</p>';} ?>
                                    {{-- <span class="flex-grow-1"></span> --}}
                                    {{-- <span class="text-small text-muted ml-auto">10 sec ago</span> --}}
                                </p>
                                {{-- <p class="text-small text-muted m-0">James: Hey! are you busy?</p> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Notificaiton End -->
                <!-- User avatar dropdown -->
                <div class="dropdown">
                    <div  class="user col align-self-end">
                        <img src="{{asset('assets/images/user-solid.png')}}" id="userDropdown" alt="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                            <div class="dropdown-header">
                                <i class="i-Lock-User mr-1"></i> {{Session::get('fullname')}}
                            </div>
                            <a class="dropdown-item" href="{{route('logout')}}">Sign out</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- header top menu end -->
