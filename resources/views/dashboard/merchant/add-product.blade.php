@extends('layouts.dashboard')

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">

                <div class="panel-body">
                    <h4 class="m-t-0 m-b-30">Add Product</h4>
                    <form action="add-product" method="post" class="form-horizontal" role="form">

                        {{csrf_field()}}

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Name</label>
                            <div class="col-sm-9">
                                <input type="text" name="name" class="form-control" id="inputEmail3" placeholder="Name">
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">Description</label>
                            <div class="col-sm-9">
                                <textarea  class="form-control" name="description" id="" cols="30" rows="10"></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">Price</label>
                            <div class="col-sm-9">
                                <input type="text" name="price" class="form-control" id="inputPassword3" placeholder="10.00">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">Billing Cycle</label>
                            <div class="col-sm-9">
                                <select class="form-control"  name="duration" id="">

                                        <option value="day">daily</option>

                                        <option value="month">monthly</option>

                                        <option value="year">yearly</option>

                                        <option value="week">weekly</option>

                                        <option value="3-month">every 3 months</option>

                                        <option value="6-month">every 6 months</option>

                                </select>

                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">Type</label>
                            <div class="col-sm-9">
                                <select class="form-control"  name="type" id="">
                                    <option value="1">One-off</option>
                                    <option value="2">Subscription</option>
                                </select>

                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">Currency</label>
                            <div class="col-sm-9">
                                <select class="form-control"  name="currency_code" id="">
                                    <option value="USD">USD</option>
                                    <option value="EUR">EUR</option>
                                    <option value="GBP">GBP</option>
                                </select>
                            </div>
                        </div>


                        <div class="form-group m-b-0">
                            <div class="col-sm-offset-3 col-sm-9">
                                <button type="submit" class="btn btn-info waves-effect waves-light">Add Product</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>


@endsection