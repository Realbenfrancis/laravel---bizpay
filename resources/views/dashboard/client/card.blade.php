@extends('layouts.dashboard')

@section('content')

    <script src="/js/jquery.min.js"></script>


    <style>
        .alert.parsley {
            margin-top: 5px;
            margin-bottom: 0px;
            padding: 10px 15px 10px 15px;
        }

        .check .alert {
            margin-top: 20px;
        }

        .credit-card-box .panel-title {
            display: inline;
            font-weight: bold;
        }

        .credit-card-box .display-td {
            display: table-cell;
            vertical-align: middle;
            width: 100%;
        }

        .credit-card-box .display-tr {
            display: table-row;
        }
    </style>



    <div class="container">


        <!-- Boostrap Tabs -->


        <div class="row">
            <div class="col-lg-12">
                {{--<div class="page-header">--}}
                    {{--<h2>Add New Card</h2>--}}
                    {{--<ol class="breadcrumb">--}}
                        {{--<li><a href="/home">Home</a></li>--}}
                        {{--<li><a href="/payment">Payment </a></li>--}}
                        {{--<li class="active">Add New Card</li>--}}
                    {{--</ol>--}}
                {{--</div>--}}
            </div>
        </div>

        <br><br><br><br><br>



        <div class="row">

            <script src="/js/card.js"></script>


            <style>
                .demo-container {
                    width: 100%;
                    max-width: 350px;
                    margin: 50px auto;
                }

                form {
                    margin: 30px;
                }

                input {
                    width: 200px;
                    margin: 10px auto;
                    display: block;
                }
            </style>


            @include('partials._form-error')




            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <div class="panel panel-default credit-card-box">
                        <div class="panel-heading display-table">
                            <div class="row display-tr">
                                <h3 class="panel-title display-td">Card Details</h3>
                                <div class="display-td">

                                </div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-12">


                                <div class="demo-container">
                                    <div class="card-wrapper"></div>


                                    <div class="form-container active">


                                        <span id="payment-form" novalidate="" class="">

                                        </span>



                                    </div>



                                </div>

                                <script>
                                    new Card({
                                        form: document.querySelector('span'),
                                        container: '.card-wrapper',
                                        formSelectors: {
                                            numberInput: 'input#number', // optional — default input[name="number"]
                                            cvcInput: 'input#cvc', // optional — default input[name="cvc"]
                                            nameInput: 'input#fullName' // optional - defaults input[name="name"]

                                        },
                                        placeholders: {
                                            name: '',
                                            number: '**** **** **** {{$card->card_last_four}}',
                                            expiry: '{{$card->card_exp_month}}/{{$card->card_exp_year}}',
                                        },
                                    });



                                    $('#month').change(function() {
                                        var expiry=$('#month').val()+"/"+$('#year').val();
                                        $('.jp-card-expiry.jp-card-display').html('');
                                        $('.jp-card-expiry.jp-card-display').html(expiry);

                                    });

                                    $('#year').change(function() {
                                        var expiry=$('#month').val()+"/"+$('#year').val();
                                        $('.jp-card-expiry.jp-card-display').html('');
                                        $('.jp-card-expiry.jp-card-display').html(expiry);
                                    });


                                </script>


                            </div>
                        </div>
                    </div>

                    <a class="btn btn-warning" style="float: right;" href="/client/update-card">Update Card</a>


                </div>
            </div>

            <script>
                window.ParsleyConfig = {
                    errorsWrapper: '<div></div>',
                    errorTemplate: '<div class="alert alert-danger parsley" role="alert"></div>',
                    errorClass: 'has-error',
                    successClass: 'has-success'
                };
            </script>


            <br>

        </div>

    </div>









@endsection