
@extends('layouts.dashboard')


@section('content')

<div class="content-page">
    <div class="content">

        <div class="">
            <div class="page-header-title">
                <h4 class="page-title">Dashboard</h4>
            </div>
        </div>

        <div class="page-content-wrapper ">

            <div class="container">

                <div class="row">
                    <div class="col-sm-6 col-lg-3">
                        <div class="panel text-center">
                            <div class="panel-heading">
                                <h4 class="panel-title text-muted font-light">Total Customers</h4>
                            </div>
                            <div class="panel-body p-t-10">
                                <h2 class="m-t-0 m-b-15"><i class="mdi mdi-arrow-up-bold-circle-outline text-success m-r-10"></i><b>{{count($customers)}}</b></h2>
                                {{--<p class="text-muted m-b-0 m-t-20"> 2</p>--}}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="panel text-center">
                            <div class="panel-heading">
                                <h4 class="panel-title text-muted font-light">Total Tickets</h4>
                            </div>
                            <div class="panel-body p-t-10">
                                <h2 class="m-t-0 m-b-15"><i class="mdi mdi-arrow-up-bold-circle-outline text-success m-r-10"></i><b>{{count($tickets)}}</b></h2>
                                {{--<p class="text-muted m-b-0 m-t-20"> 2</p>--}}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="panel text-center">
                            <div class="panel-heading">
                                <h4 class="panel-title text-muted font-light">API Users</h4>
                            </div>
                            <div class="panel-body p-t-10">
                                <h2 class="m-t-0 m-b-15"><i class="mdi mdi-arrow-up-bold-circle-outline text-success m-r-10"></i><b>2</b></h2>
                                {{--<p class="text-muted m-b-0 m-t-20"> 2 </p>--}}
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3">
                        <div class="panel text-center">
                            <div class="panel-heading">
                                <h4 class="panel-title text-muted font-light">API Status</h4>
                            </div>
                            <div class="panel-body p-t-10">

                                @if($api)

                                    <h2 class="m-t-0 m-b-15"><i class="mdi mdi-arrow-up-bold-circle-outline text-success m-r-10"></i><b>Running</b></h2>


                                @else

                                    <h2 class="m-t-0 m-b-15"><i class="mdi mdi-arrow-down-bold-circle-outline text-danger m-r-10"></i><b>Running</b></h2>

                                @endif

                                {{--<p class="text-muted m-b-0 m-t-20"> Running</p>--}}
                            </div>
                        </div>
                    </div>
                </div>


                <!-- end row -->

                <div class="row">

                    <div class="col-md-12">
                        <div class="panel">
                            <div class="panel-body">
                                <h4 class="m-b-30 m-t-0"> Customers</h4>
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="table-responsive">
                                            <table class="table table-hover m-b-0">
                                                <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                </tr>

                                                </thead>
                                                <tbody>

                                                @foreach($customers as $k=>$v)

                                                <tr>
                                                    <td>{{$v}}</td>
                                                    <td>{{$k}}</td>

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
                    <!-- end col -->
                </div>
                <!-- end row -->



            </div><!-- container -->


        </div> <!-- Page content Wrapper -->

    </div> <!-- content -->

    <footer class="footer">
        © 2017 Bizpay Ltd - All Rights Reserved.
    </footer>

</div>


    @endsection