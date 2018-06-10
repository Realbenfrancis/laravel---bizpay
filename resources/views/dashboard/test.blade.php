
@extends('layouts.dashboard')


@section('content')

    <link rel="stylesheet" type="text/css" href="/new-assets/plugins/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="/new-assets/css/app.css">

    <div class="page-content-wrapper ">




                <div style="margin-top: 10%;" class="container custom-container">


                    <div class="genric-checkout">


                        <div class="logo-area">
                            <div class="logo">
                                <img src="/new-assets/images/logo-img.png" class="img-responsive" alt="Logo">
                            </div>
                            <div class="simple-text">
                                <p>Sellers Logo</p>
                            </div>
                        </div>

                        <div class=" table-responsive table-bordered show-bottom">

                            <table class="table payments-table">
                                <thead>
                                <tr>
                                    <th class="pay-items "> Payments</th>
                                    <th class="th-padding text-right">Price</th>
                                    <th class="th-padding text-right">Tax</th>
                                    <th class="th-padding text-right">Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td class="td-padding padding-left-for">11 future payments of</td>
                                    <td class="th-padding text-right">£640</td>
                                    <td class="th-padding text-right">£160</td>
                                    <td class="th-padding text-right">£800</td>
                                </tr>

                                <tr class="hide-part ">
                                    <td class="td-padding padding-left-for">Amount to pay now</td>
                                    <td class="th-padding text-right">£256</td>
                                    <td class="th-padding text-right">£64</td>
                                    <td class="th-padding text-right">£300</td>
                                </tr>
                                <tr class="hide-part ">

                                    <th class="pay-items">Agreement total</th>
                                    <th class="th-padding text-right">£6600</th>
                                    <th class="th-padding text-right">£860</th>
                                    <th class="th-padding text-right">£4,320</th>
                                </tr>
                                </tbody>


                            </table>
                        </div>


                        <div class="row">
                            <div class="col-md-6 col-sm-6 col-xs-6 no-padding-right">
                                <a class="show-more show-area">show remaining +</a>

                            </div>
                            <div class="col-md-6 col-sm-6 col-xs-6 no-padding-left">
                                <a href="#" class="hide-part agreement pull-right">view agreement</a>

                            </div>
                        </div>

                        <div class="payments">
                            <form method="post" action="/test" role="form" data-toggle="validator" novalidate="true">
                                <h1 class="ch1 no-margin">Your details</h1>
                                <div class="row no-margin">

                                    <div class="col-md-6 col-sm-12 custom-padding-right">
                                        <div class="form-group amrgin">
                                            <label for="inputName">First Name <span class="control-label">*</span></label>
                                            <input type="text" class="form-control" id="inputName" required="" data-error="Please enter your first name">
                                            <div class="help-block with-errors"></div>

                                        </div>
                                    </div>
                                    <div class="col-md-6 col-sm-12 custom-padding-left">
                                        <div class="form-group amrgin">
                                            <label for="lname">Last Name <span class="control-label">*</span></label>
                                            <input type="text" class="form-control" id="lname" required="" data-error="Please enter your surname">
                                            <div class="help-block with-errors"></div>

                                        </div>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inputEmail">Email <span class="control-label">*</span></label>
                                    <input type="email" class="form-control" id="inputEmail" placeholder="Email" data-error="Invalid email address" required="">
                                    <div class="help-block with-errors"></div>
                                </div>
                                <div class="form-group has-error has-danger">
                                    <label for="ph">Phone Number</label>
                                    <input type="text" class="form-control" pattern="0-9" maxlength="15" id="ph">
                                </div>
                                <div class="form-group">
                                    <label>Buyer type</label><br>
                                    <div class="input-radio ">

                                        <input type="radio" name="check" checked="checked" value="2">Individual
                                        <span class="radio-margin">
                                 <input type="radio" name="check" value="3">Organisation
                            </span>

                                    </div>
                                </div>




                                <div id="rd-sec3" class="desc" style="display: none;">
                                    <div class="form-group has-error has-danger">
                                        <label for="org">Organisation name <span class="control-label">*</span></label>
                                        <input type="text" class="form-control" id="org">
                                        <div class="help-block with-errors"><ul class="list-unstyled"><li>Please enter an organisation name, this should be a maximum of 200 characters</li></ul></div>
                                    </div>
                                    <div class="form-group">
                                        <label for="crn">Company registration number</label>
                                        <input type="text" class="form-control" id="crn" placeholder="0234231">
                                    </div>

                                    <div class="form-group">
                                        <label>Role / Job title</label>
                                        <select class="form-control no-radius" id="test">
                                            <option value="1">Manager</option>
                                            <option value="2">CEO</option>
                                            <option value="3">C-Suite</option>
                                            <option value="4">Managing Director</option>
                                            <option value="5">Director</option>
                                            <option value="6">Founder</option>
                                            <option value="7">Other</option>

                                        </select>
                                    </div>


                                    <div id="hidden_div" style="display: none;">

                                        <div class="form-group">
                                            <label for="otitle">Please enter other job title</label>
                                            <input type="text" class="form-control" id="otitle" placeholder="job title which isn't in the list">
                                        </div>
                                    </div>
                                </div>


                                <h1 class="margin-bottom ch1">Address</h1>
                                <div class="row no-margin">
                                    <div class="form-group">
                                        <div class="col-md-12 no-padding">
                                            <label for="postcode">Postcode</label>
                                        </div>


                                        <div class="col-md-6 col-sm-6 col-xs-12 custom-padding-right">
                                            <input type="text" class="form-control" placeholder="Enter postcode and click find address" id="postcode">
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-12 custom-padding-left postcode">
                                            <span class="btn btn-default post-code-button">Find address</span>
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <input type="text" class="form-control" id="address">
                                </div>



                                <div class="form-group">
                                    <label for="town">Town</label>
                                    <input type="text" class="form-control" id="town">
                                </div>
                                <div class="form-group">
                                    <label for="country">Country</label>
                                    <select class="form-control no-radius" id="country">
                                        <option value="1">United Kingdom</option>
                                        <option value="2">United State</option>
                                        <option value="3">Germany</option>
                                        <option value="4">China</option>
                                        <option value="5">Russia</option>
                                    </select>
                                </div>

                            </form>

                        </div>
                        <div class="button-area text-center">
                            <button type="submit" class="btn btn-default text-center">Confirm</button>
                        </div>
                    </div>

                </div>


            </div> <!-- Page content Wrapper -->


    <script src="/new-assets/plugins/jquery/jquery-2.2.4.min.js"></script>
    <script src="/new-assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="/new-assets/plugins/validator/validator.js"></script>
    <script src="/new-assets/js/app.js"></script>

@endsection