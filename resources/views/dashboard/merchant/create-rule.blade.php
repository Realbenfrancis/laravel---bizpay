@extends('layouts.dashboard')

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">

                <div class="panel-body">
                    <h4 class="m-t-0 m-b-30">Add Rule</h4>
                    <form action="add-rule" method="post" class="form-horizontal" role="form">

                        {{csrf_field()}}


                        @if(Request::segment(1)=="admin")

                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-3 control-label">Merchant</label>
                                <div class="col-sm-9">
                                    <select  class="form-control" name="merchant_id" id="">
                                        @foreach($merchants as $merchant)
                                            <option value="{{$merchant->id}}">{{$merchant->merchant_name}}</option>
                                        @endforeach
                                    </select>

                                </div>
                            </div>


                        @endif

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Rule Name</label>
                            <div class="col-sm-9">
                                <input type="text" name="name" class="form-control" id="inputEmail3" placeholder="Name">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Check Type</label>
                            <div class="col-sm-9">

                                <select  class="form-control" name="check_type" id="">
                                    <option value="equal">Equals</option>
                                    <option value="greater">Greater Than</option>
                                    <option value="less">Less Than</option>
                                    <option value="addition">Addition</option>
                                    <option value="between">Between</option>
                                    <option value="subtraction">Subtraction</option>
                                </select>

                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Data Type</label>
                            <div class="col-sm-9">

                                <select  class="form-control" name="data_type" id="">
                                    <option value="String">String</option>
                                    <option value="Integer">Integer</option>
                                    <option value="Boolean">Boolean</option>
                                </select>

                            </div>
                        </div>


                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">Description</label>
                            <div class="col-sm-9">
                                <textarea  class="form-control" name="description" id="" cols="30" rows="10"></textarea>
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Apply Rule On</label>
                            <div class="col-sm-9">
                                <select  class="form-control" name="apply_rule_on" id="">
                                    <option value="client_age">Client's Age</option>
                                    <option value="client_country">Client's Country</option>
                                    <option value="start_billing">Start Billing In</option>
                                    <option value="offer_trial_for">Offer Trial For</option>
                                    <option value="offer_plan_only">Offer Plan Only for Price</option>
                                    <option value="plan">Plan Duration</option>
                                    <option value="instalment">Instalment</option>
                                </select>
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Limit 1</label>
                            <div class="col-sm-9">
                                <input type="text" name="limit1" class="form-control" id="inputEmail3" placeholder="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Limit 2</label>
                            <div class="col-sm-9">
                                <input type="text" name="limit2" class="form-control" id="inputEmail3" placeholder="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Limit 3</label>
                            <div class="col-sm-9">
                                <input type="text" name="limit3" class="form-control" id="inputEmail3" placeholder="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Action</label>
                            <div class="col-sm-9">
                                <select  class="form-control" name="action_on" id="">
                                    <option value="price">Change Price</option>
                                    <option value="user_status"> User Status</option>
                                    <option value="credit">Offer Credit</option>
                                    <option value="first_payment">First Payment</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Action Type*</label>
                            <div class="col-sm-9">
                                <select  class="form-control" name="action_type" id="">
                                    <option value="Boolean">Boolean</option>
                                    <option value="Percentage">Percentage</option>
                                    <option value="Value">Value</option>
                                </select>
                            </div>
                        </div>

                        <p>*All options might not be available. Please check before selecting this. </p>

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Action Value</label>
                            <div class="col-sm-9">
                                <input type="text" name="action_value" class="form-control" id="inputEmail3" placeholder="">
                            </div>
                        </div>


                        <div class="form-group m-b-0">
                            <div class="col-sm-offset-3 col-sm-9">
                                <button type="submit" class="btn btn-info waves-effect waves-light">Add Rule</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>


@endsection