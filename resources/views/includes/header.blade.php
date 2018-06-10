

<!-- Begin page -->
<div id="wrapper">

    <!-- Top Bar Start -->
    <div class="topbar">
        <!-- LOGO -->
        <div class="topbar-left">
            <div class="text-center">

                <img height="44" src="https://static.wixstatic.com/media/638582_05b10375892e42c1bba79747d1b9d661.png/v1/fill/w_165,h_54,al_c,usm_0.66_1.00_0.01/638582_05b10375892e42c1bba79747d1b9d661.png" width="135" class="CToWUd">

                {{--<a href="/index" class="logo"><span>Biz</span>Pay</a>--}}
                {{--<a href="/index" class="logo-sm"><span>BP</span></a>--}}
                <!--<a href="index.html" class="logo"><img src="assets/images/logo_white_2.png" height="28"></a>-->
                <!--<a href="index.html" class="logo-sm"><img src="assets/images/logo_sm.png" height="36"></a>-->
            </div>
        </div>
        <!-- Button mobile view to collapse sidebar menu -->
        <div class="navbar navbar-default" role="navigation">
            <div class="container">
                <div class="">
                    <div class="pull-left">
                        {{--<button type="button" class="button-menu-mobile open-left waves-effect waves-light">--}}
                            {{--<i class="ion-navicon"></i>--}}
                        {{--</button>--}}
                        <span class="clearfix"></span>
                    </div>


                    <ul class="nav navbar-nav navbar-right pull-right">
                        <li class="dropdown hidden-xs">
                            <a href="#" data-target="#" class="dropdown-toggle waves-effect waves-light notification-icon-box" data-toggle="dropdown" aria-expanded="true">
                                <i class="fa fa-bell"></i> <span class="badge badge-xs badge-danger"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-lg">
                                <li class="text-center notifi-title">Notifications</li>
                                <li class="list-group">
                                    <!-- list item-->
                                    <!-- list item-->
                                    <a href="javascript:void(0);" class="list-group-item">
                                        <div class="media">
                                            <div class="media-body clearfix">
                                                <div class="media-heading">Service Alert</div>
                                                <p class="m-0">
                                                    <small>All services are running normally!</small>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                    <!-- list item-->
                                    <!-- last list item -->

                                </li>
                            </ul>
                        </li>
                        <li class="hidden-xs">
                            <a href="#" id="btn-fullscreen" class="waves-effect waves-light notification-icon-box"><i class="mdi mdi-fullscreen"></i></a>
                        </li>
                        <li class="dropdown">
                            <a href="" class="dropdown-toggle profile waves-effect waves-light" data-toggle="dropdown" aria-expanded="true">
                                {{--<img src="/assets/images/users/avatar-1.jpg" alt="user-img" class="img-circle">--}}
                                        <span class="profile-username">
                                           {{\Illuminate\Support\Facades\Auth::user()->name}} <br/>
                                        </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a href="/logout"> Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <!--/.nav-collapse -->
            </div>
        </div>
    </div>

