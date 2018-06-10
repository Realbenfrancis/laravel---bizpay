
@extends('layouts.dashboard')

@section('content')


    <form method="post" action="/admin/merchant-detail">

                    <div class="col-sm-12">
                        <div class="panel panel-primary">
                            <div class="panel-body">
                                <h4 class="m-t-0 m-b-30">Merchant Details</h4>

                                <form class="form-horizontal" role="form">
                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Name</label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" value="Name">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-2 control-label" for="example-email">Email</label>
                                        <div class="col-md-10">
                                            <input type="email" id="example-email" name="example-email" class="form-control" placeholder="Email">
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Stripe Public Key</label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" disabled="disabled" value="Disabled value">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Stripe Private Key</label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" disabled="disabled" value="Disabled value">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Bizpay Merchant ID</label>
                                        <div class="col-md-10">
                                            <input type="text" class="form-control" disabled="disabled" value="Disabled value">
                                        </div>
                                    </div>


                                    {{--<div class="form-group">--}}
                                        {{--<label class="col-md-2 control-label"> Merchant Secret Key</label>--}}
                                        {{--<div class="col-md-10">--}}
                                            {{--<input type="text" class="form-control" disabled="disabled" value="Disabled value">--}}
                                        {{--</div>--}}
                                    {{--</div>--}}


                                    <div class="form-group">
                                        <label class="col-md-2 control-label">Notes</label>
                                        <div class="col-md-10">
                                            <textarea class="form-control" rows="5"></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">Website</label>
                                        <div class="col-sm-10">
                                            <p class="form-control-static">email@example.com</p>
                                        </div>
                                    </div>

                                    <div class="form-group">

                                        <button class="btn btn-primary" type="submit">Save</button>

                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>


    </form>

    @endsection


