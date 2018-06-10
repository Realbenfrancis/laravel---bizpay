@extends('layouts.dashboard-full')

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
                <div class="page-header">
                    <h2>Add New Card</h2>
                    <ol class="breadcrumb">
                        <li><a href="/home">Home</a></li>
                        <li><a href="/payment">Payment </a></li>
                        <li class="active">Add New Card</li>
                    </ol>
                </div>
            </div>
        </div>


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
                                <h3 class="panel-title display-td">Payment Details Form</h3>
                                <div class="display-td">
                                    <img class="img-responsive pull-right"
                                         src="/assets/accepted_cards.png">
                                </div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="col-md-12">


                                <div class="demo-container">
                                    <div class="card-wrapper"></div>

                                    <div class="form-container active">
                                        {{--<form action="">--}}
                                        {{--<input placeholder="Card number" type="text" name="number">--}}
                                        {{--<input placeholder="Full name" type="text" name="name">--}}
                                        {{--<input placeholder="MM/YY" type="text" name="expiry">--}}
                                        {{--<input placeholder="CVC" type="text" name="cvc">--}}
                                        {{--</form>--}}



                                        {{--{!! Form::open(['action' => "PaymentCardController@store", 'data-parsley-validate', 'id' => 'payment-form']) !!}--}}


                                        <form action="/payment"  method="post" accept-charset="UTF-8" data-parsley-validate="data-parsley-validate" id="payment-form" novalidate="" class="">


                                            {{csrf_field()}}

                                        @if (session('next-page'))
                                            <input type="text" name="next_page" value="{{session('next-page')}}">
                                        @endif

                                        <script>
                                            $(document).ready(function () {

                                                function onchange() {
                                                    // alert('test');
                                                    var box1 = $('#number');
                                                    var box2 = $('#card');
                                                    var val1 = $.trim(box1.val());
                                                    box2.val(val1);
                                                }

                                                $('#number').on('change', onchange);
                                            });
                                        </script>


                                        {{--<input type="hidden" name="card" id="card">--}}

                                        <div class="form-group" id="cc-group">

                                            {!! Form::hidden('card', null, [
                                               'id'                         => 'card',
                                             'class'                         => 'form-control',
                                             'required'                      => 'required',
                                             'data-stripe'                   => 'number',
                                             'data-parsley-type'             => 'number',
                                             'maxlength'                     => '16',
                                             'data-parsley-trigger'          => 'change focusout',
                                             'data-parsley-class-handler'    => '#cc-group'
                                             ]) !!}

                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('number', 'Credit card number:') !!}
                                            {!! Form::text('number', null, [
                                                'class'                         => 'form-control',
                                                'required'                      => 'required',

                                                'maxlength'                     => '20',

                                                ]) !!}
                                        </div>

                                        <div class="form-group" id="ccv-group">
                                            {!! Form::label('cvc', 'CVC (3 or 4 digit number):') !!}
                                            {!! Form::text('cvc', null, [
                                                'class'                         => 'form-control',
                                                'required'                      => 'required',
                                                'data-stripe'                   => 'cvc',
                                                'data-parsley-type'             => 'number',
                                                'data-parsley-trigger'          => 'change focusout',
                                                'maxlength'                     => '4',
                                                'data-parsley-class-handler'    => '#ccv-group'
                                                ]) !!}
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group" id="exp-m-group">
                                                    {!! Form::label('month', 'Ex. Month') !!}
                                                    {!! Form::selectMonth('month', 'month', [
                                                        'class'                 => 'form-control',
                                                        'required'              => 'required',
                                                        'data-stripe'           => 'exp-month'
                                                    ], '%m') !!}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group" id="exp-y-group">
                                                    {!! Form::label('year', 'Ex. Year') !!}
                                                    {!! Form::selectYear('year', date('Y'), date('Y') + 10, null, [
                                                        'class'             => 'form-control',
                                                        'required'          => 'required',
                                                        'data-stripe'       => 'exp-year'
                                                        ]) !!}
                                                </div>
                                            </div>
                                        </div>

                                            <input type="hidden" name="plan" value="{{$plan}}">




                                        <div class="form-group">
                                            {!! Form::submit('Save card', ['class' => 'btn btn-lg btn-block btn-primary btn-order', 'id' => 'submitBtn' , 'style' => 'margin-bottom: 10px;']) !!}
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span  class="payment-errors" style="padding:0px; color: red;margin-top:10px;"></span>
                                            </div>
                                        </div>


                                        {!! Form::close() !!}

                                    </div>


                                </div>

                                <script>
                                    new Card({
                                        form: document.querySelector('form'),
                                        container: '.card-wrapper',
                                        formSelectors: {
                                            numberInput: 'input#number', // optional — default input[name="number"]
                                            cvcInput: 'input#cvc', // optional — default input[name="cvc"]
                                            nameInput: 'input#fullName' // optional - defaults input[name="name"]

                                        },
                                        placeholders: {
                                            name: '',
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

            <script src="/js/parsley.js?v=1"></script>

            <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
            <script>
                Stripe.setPublishableKey("{{env('STRIPE_PUBLISHABLE_SECRET')}}");
                jQuery(function ($) {
                    $('#payment-form').submit(function (event) {

                        var $form = $('#payment-form');
                        $form.parsley().subscribe('parsley:form:validate', function (formInstance) {
                            formInstance.submitEvent.preventDefault();
                            return false;
                        });
                        $form.find('#submitBtn').prop('disabled', true);
                        Stripe.card.createToken($form, stripeResponseHandler);
                        return false;

                    });
                });

                function submitCardForm() {
                    alert('yes');
                    var $form = $('#payment-form');
                    $form.parsley().subscribe('parsley:form:validate', function (formInstance) {
                        formInstance.submitEvent.preventDefault();
                        return false;
                    });
                    $form.find('#submitBtn').prop('disabled', true);
                    Stripe.card.createToken($form, stripeResponseHandler);
                    return false;
                }


                function stripeResponseHandler(status, response) {
                    var $form = $('#payment-form');
                    if (response.error) {
                        $form.find('.payment-errors').text(response.error.message);
                        $form.find('.payment-errors').addClass('alert alert-danger');
                        $form.find('#submitBtn').prop('disabled', false);
                        $('#submitBtn').button('reset');
//                        window.setTimeout(function() {
//                            window.location.href = '/subscription';
//                        }, 5000);
                    } else {
                        var token = response.id;
                        $form.append($('<input type="hidden" name="stripeToken" />').val(token));
                        $form.unbind('submit').submit();
                        $form.get(0).submit();
                    }
                }
                ;
            </script>


            <br>

        </div>

    </div>









@endsection