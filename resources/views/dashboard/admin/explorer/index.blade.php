@extends('layouts.dashboard')


@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <h4 class="m-t-0 m-b-30">API Explorer</h4>


                    <form method="post" action="/admin/api-explorer">
                        {{csrf_field()}}


                        <div class="row">


                    <select class="form-control" name="merchant-id" id="" required>
                        <option value="">Please select a merchant</option>
                        @foreach($merchants as $merchant)
                            @if($merchant->id!=1)
                            <option value="{{$merchant->id}}">{{$merchant->merchant_name}}</option>
                        @endif

                            @endforeach
                    </select>
                        </div>


                        <br> <br>

                        <br>

                        <div class="row">





                            <div class="col-sm-6 col-lg-3">
                                <div class="panel text-center">
                                    <div class="panel-body p-t-10">

                                        <button name="btn-action" value="createProduct" class="btn btn-primary" type="submit">Create Test Product</button>


                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-lg-3">
                                <div class="panel text-center">
                                    <div class="panel-body p-t-10">

                                        <button name="btn-action" value="getProducts" class="btn btn-primary" type="submit">Fetch  Products</button>


                                    </div>
                                </div>
                            </div>


                            <div class="col-sm-6 col-lg-3">
                                <div class="panel text-center">
                                    <div class="panel-body p-t-10">

                                        <button name="btn-action" value="createPlan" class="btn btn-primary" type="submit">Create Test Plan</button>


                                    </div>
                                </div>
                            </div>


                            <div class="col-sm-6 col-lg-3">
                                <div class="panel text-center">
                                    <div class="panel-body p-t-10">

                                        <button name="btn-action" value="fetchPlans" class="btn btn-primary" type="submit">Fetch Plans</button>


                                    </div>
                                </div>
                            </div>


                            <div class="col-sm-6 col-lg-3">
                                <div class="panel text-center">
                                    <div class="panel-body p-t-10">

                                        <button name="btn-action" value="createDynamicQuote" class="btn btn-primary" type="submit">Create Dynamic Quote</button>


                                    </div>
                                </div>
                            </div>


                            <div class="col-sm-6 col-lg-3">
                                <div class="panel text-center">
                                    <div class="panel-body p-t-10">


                                        <button name="btn-action" value="createUser" class="btn btn-primary" type="submit">Create Test User</button>


                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-lg-3">
                                <div class="panel text-center">
                                    <div class="panel-body p-t-10">

                                        <button name="btn-action" value="getUsers" class="btn btn-primary" type="submit">Fetch  Users</button>


                                    </div>
                                </div>
                            </div>


                            <div class="col-sm-6 col-lg-3">
                                <div class="panel text-center">
                                    <div class="panel-body p-t-10">

                                        <button name="btn-action" value="AddPayment" class="btn btn-primary" type="submit">Add User's Payment Details</button>


                                    </div>
                                </div>
                            </div>




                            <div class="col-sm-6 col-lg-3">
                                <div class="panel text-center">
                                    <div class="panel-body p-t-10">

                                        <button name="btn-action" value="createAgreement" class="btn btn-primary" type="submit">Create Agreement</button>


                                    </div>
                                </div>
                            </div>


                            <div class="col-sm-6 col-lg-3">
                                <div class="panel text-center">
                                    <div class="panel-body p-t-10">

                                        <button name="btn-action" value="createScheduledPayments" class="btn btn-primary" type="submit">Create Scheduled Payments</button>


                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-lg-3">
                                <div class="panel text-center">
                                    <div class="panel-body p-t-10">

                                        <button name="btn-action" value="viewScheduledPayments" class="btn btn-primary" type="submit">View  Payments</button>


                                    </div>
                                </div>
                            </div>


                            <div class="col-sm-6 col-lg-3">
                                <div class="panel text-center">
                                    <div class="panel-body p-t-10">

                                        <button name="btn-action" value="cancelAgreement" class="btn btn-primary" type="submit">Cancel Agreement</button>


                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-lg-3">
                                <div class="panel text-center">
                                    <div class="panel-body p-t-10">

                                        <button name="btn-action" value="refundAgreement" class="btn btn-primary" type="submit">Refund Agreement</button>


                                    </div>
                                </div>
                            </div>





                        </div>

                    </form>







                </div>
            </div>
        </div>

    </div>

@endsection