@extends('layouts.dashboard')

@section('content')

    <link href="/assets/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css" rel="stylesheet">
    <script src="/assets/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">

                <div class="panel-body">
                    <h4 class="m-t-0 m-b-30">Settings</h4>
                    <form action="settings" method="post" class="form-horizontal" role="form">

                        {{csrf_field()}}

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Name</label>
                            <div class="col-sm-9">
                                <input type="text" name="merchant_name" class="form-control" id="inputEmail3" value="{{$merchant->merchant_name}}" >
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">Merchant Id</label>
                            <div class="col-sm-9">
                                <input type="text" name="email" class="form-control" id="inputPassword3" value="{{$merchant->merchant_id}}" readonly>
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Tax Rate (%)</label>
                            <div class="col-sm-9">
                                <input  type="number" name="tax" class="form-control" id="inputEmail3" value="{{$merchant->tax}}" >
                                {{--<span style="float: right;">%</span>--}}
                            </div>
                        </div>

                        {{--<div class="form-group">--}}
                            {{--<label for="inputPassword3" class="col-sm-3 control-label">Color</label>--}}
                            {{--<div class="col-sm-9">--}}
                                {{--<input name="color"  type="text" class="colorpicker-default form-control colorpicker-element" value="{{$merchant->color}}">--}}
                            {{--</div>--}}
                        {{--</div>--}}




                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">Default Gateway</label>
                            <div class="col-sm-9">

                                <select class="form-control" name="default-gateway">
                                    <option value="1" @if($merchant->gateway==1) selected @endif>Stripe</option>
                                    <option value="2" @if($merchant->gateway==2) selected @endif>GoCardless</option>
                                </select>

                            </div>
                        </div>


                        <br><br>

                        <h3>Payment Gateway Settings</h3>
                        <br><br>

                        <div class="row">

                            <div class="tabs-vertical-env">
                                <ul class="nav tabs-vertical">
                                    <li class="active">
                                        <a href="#stripe" data-toggle="tab" aria-expanded="true">Stripe</a>
                                    </li>
                                    <li class="">
                                        <a href="#gocardless" data-toggle="tab" aria-expanded="false">GoCardless</a>
                                    </li>


                                </ul>

                                <div style="width: 90%;" class="tab-content">
                                    <div class="tab-pane active" id="stripe">



                                        @if(count($stripe)<1)


                                        <div class="col-sm-12" class="form-group">
                                            <label for="inputPassword3" class="col-sm-3 control-label">Publishable Key</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="stripe_credential_1" class="form-control" id="inputPassword3" value="">
                                            </div>
                                        </div>

                                        <div class="col-sm-12" class="form-group">
                                            <label for="inputPassword3" class="col-sm-3 control-label">Secret Key</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="stripe_credential_2" class="form-control" id="inputPassword3" value="">
                                            </div>
                                        </div>

                                            @else

                                            <div class="col-sm-12" class="form-group">
                                                <label for="inputPassword3" class="col-sm-3 control-label">Publishable Key</label>
                                                <div class="col-sm-9">
                                                    <input type="text" name="stripe_credential_1" class="form-control" id="inputPassword3" value="{{$stripe[0]->credential_1}}">
                                                </div>
                                            </div>

                                            <div class="col-sm-12" class="form-group">
                                                <label for="inputPassword3" class="col-sm-3 control-label">Secret Key</label>
                                                <div class="col-sm-9">
                                                    <input type="text" name="stripe_credential_2" class="form-control" id="inputPassword3" value="{{$stripe[0]->credential_2}}">
                                                </div>
                                            </div>


                                            @endif

                                    </div>

                                    <div class="tab-pane" id="gocardless">

                                        @if(count($goCardless)<1)

                                        <div class="col-sm-12" class="form-group">
                                            <label for="inputPassword3" class="col-sm-3 control-label">Access Key</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="gocardless_credential" class="form-control" id="inputPassword3">
                                            </div>
                                        </div>

                                            @else

                                            <div class="col-sm-12" class="form-group">
                                                <label for="inputPassword3" class="col-sm-3 control-label">Access Key</label>
                                                <div class="col-sm-9">
                                                    <input type="text" name="gocardless_credential" class="form-control" id="inputPassword3" value="{{$goCardless[0]->credential_1}}">
                                                </div>
                                            </div>

                                        @endif

                                    </div>

                                    <div class="tab-pane" id="worldpay">

                                        <p>Coming soon!</p>
                                    </div>


                                    <div class="tab-pane" id="paypal">

                                        <p>Coming soon!</p>
                                    </div>


                                </div>
                            </div>

                        </div>


                        <div class="form-group m-b-0">
                            <div class="col-sm-offset-3 col-sm-9">
                                <button type="submit" class="btn btn-info waves-effect waves-light"> Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>


@endsection