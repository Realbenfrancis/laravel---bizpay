    @extends('layouts.dashboard')

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">

                <div class="panel-body">
                    <h4 class="m-t-0 m-b-30">Add Merchant</h4>
                    <form action="/admin/add-merchant" method="post" class="form-horizontal" role="form">

                        {{csrf_field()}}

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Name</label>
                            <div class="col-sm-9">
                                <input type="text" name="merchant_name" class="form-control" id="inputEmail3" placeholder="Name">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">Email</label>
                            <div class="col-sm-9">
                                <input type="email" name="merchant_email" class="form-control" id="inputPassword3" placeholder="Email">
                            </div>
                        </div>

                        {{--<div class="form-group">--}}
                            {{--<label for="inputPassword3" class="col-sm-3 control-label">Phone Number</label>--}}
                            {{--<div class="col-sm-9">--}}
                                {{--<input type="text" name="merchant_phone_number" class="form-control" id="inputPassword3" placeholder="Phone Number">--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">Website</label>
                            <div class="col-sm-9">
                                <input name="merchant_website" type="text" class="form-control" id="inputPassword3" placeholder="Website">
                            </div>
                        </div>




                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <div class="checkbox checkbox-primary">
                                    <input name="resource_check" id="checkbox222" type="checkbox">
                                    <label for="checkbox222">
                                        Dedicated Resources
                                    </label>
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <div class="checkbox checkbox-primary">
                                    <input name="direct_client" id="checkbox222" type="checkbox">
                                    <label for="checkbox222">
                                        Direct Client
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <div class="checkbox checkbox-primary">
                                    <input name="bizpay_credit" id="checkbox222" type="checkbox">
                                    <label for="checkbox222">
                                        Offer Bizpay Credit
                                    </label>
                                </div>
                            </div>
                        </div>


                        <div class="form-group m-b-0">
                            <div class="col-sm-offset-3 col-sm-9">
                                <button type="submit" class="btn btn-info waves-effect waves-light">Add Merchant</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>


@endsection