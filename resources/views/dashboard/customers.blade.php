@extends('layouts.dashboard')

@section('content')

    <div class="content-page">
        <div class="content">

            <div class="">
                <div class="page-header-title">
                    <h4 class="page-title">Customers</h4>
                </div>
            </div>

            <div class="page-content-wrapper ">

                <div class="container">


                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-primary">
                                <div class="panel-body">
                                    <h4 class="m-b-30 m-t-0"></h4>

                                    <table id="datatable-buttons" class="table table-striped table-bordered">
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

        </div>

        <footer class="footer">
            © 2017 Bizpay - All Rights Reserved.
        </footer>

    </div>

    </div>



    @endsection