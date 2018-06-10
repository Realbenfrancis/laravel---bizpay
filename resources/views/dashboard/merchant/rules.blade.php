@extends('layouts.dashboard')

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <h4 class="m-t-0 m-b-30">Rules</h4>

                    <a class="btn btn-success" style="float: right; margin-left: 5%;" href="add-rule">Add New Rule</a>


                    <table id="datatable-buttons" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            {{--<th>ID</th>--}}
                            <th>Name</th>
                            <th>Description</th>
                            <th>Check Type</th>
                            <th>Apply Rule On</th>
                            <th>Data </th>
                            <th>Limit 1</th>
                            <th>Limit 2</th>
                            <th>Limit 3</th>
                            <th>Action</th>
                            <th>Action Type</th>
                            <th>Action Value</th>
                            <th>Delete</th>
                        </tr>
                        </thead>

                        <tbody>

                        @foreach($rules as $rule)

                            <tr>
{{--                                <td>{{$rule->rule_id}}</td>--}}
                                <td>{{$rule->rule}}</td>
                                <td>{{$rule->description}}</td>
                                <td>

                                    @if($rule->check_type=="equal")
                                        Equals
                                    @elseif($rule->check_type=="greater")
                                        Greater Than
                                    @elseif($rule->check_type=="less")
                                        Less Than
                                    @elseif($rule->check_type=="addition")
                                        Addition
                                    @elseif($rule->check_type=="between")
                                        Between
                                    @elseif($rule->check_type=="subtraction")
                                        Subtraction
                                    @endif
                                </td>
                                <td>
                                    @if($rule->apply_rule_on=="client_age")
                                        Client's Age
                                    @elseif($rule->apply_rule_on=="client_card")
                                        Client's Card
                                    @elseif($rule->apply_rule_on=="client_country")
                                        Client's Country
                                    @elseif($rule->apply_rule_on=="billing_method")
                                        Billing Method
                                    @elseif($rule->apply_rule_on=="start_billing")
                                        Start Billing In
                                    @elseif($rule->apply_rule_on=="charge_right_away")
                                        Charge Right Away
                                    @elseif($rule->apply_rule_on=="offer_trial_for")
                                        Offer Trial For
                                    @elseif($rule->apply_rule_on=="offer_plan_only")
                                        Offer Plan Only for Price
                                    @elseif($rule->apply_rule_on=="plan")
                                        Plan Duration
                                    @elseif($rule->apply_rule_on=="instalment")
                                         Instalment
                                    @elseif($rule->apply_rule_on=="any")
                                         Any
                                    @endif

                                    {{--{{$product->currency_code}}--}}
                                </td>
                                <td>
                                    {{$rule->data_type}}
{{--                                    {{$product->price}}--}}
                                </td>

                                <td>{{$rule->limit1}}</td>
                                <td>{{$rule->limit2}}</td>
                                <td>{{$rule->limit3}}</td>

                                <td>

                                    @if($rule->action_on=="price")
                                        Change Price
                                    @elseif($rule->action_on=="user_status")
                                        User Status
                                    @elseif($rule->action_on=="credit")
                                        Offer Credit
                                    @elseif($rule->action_on=="first_payment")
                                        First Payment
                                    @endif

                                </td>
                                <td>{{$rule->action_type}}</td>
                                <td>{{$rule->action_value}}</td>

                                <td>
                                    <form method="post" action="rules">
                                        {{csrf_field()}}
                                        <input type="hidden" name="id" value="{{$rule->rule_id}}">
                                        <button type="submit" class="btn btn-primary">Delete</button>
                                    </form>
                                </td>


                            </tr>

                        @endforeach

                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>

@endsection