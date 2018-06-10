@extends('layouts.dashboard')

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <h4 class="m-t-0 m-b-30">Products</h4>

                    <a class="btn btn-success" style="float: right; margin-left: 5%;" href="add-product">Add New Product</a>
                    <a class="btn btn-success" style="float: right; margin-left: 5%;" href="add-installment">Add Installment Plan</a>


                    <table id="datatable-buttons" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Currency</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                        </thead>

                        <tbody>

                        @foreach($products as $product)

                            <tr>
                                <td>{{$product->product_id}}</td>
                                <td>{{$product->name}}</td>
                                <td>
                                    @if($product->type==1)
                                        One-off
                                    @elseif($product->type==2)
                                        Subscription
                                    @elseif($product->type==3)
                                        Instalment Product
                                    @endif
                                </td>
                                <td>{{$product->currency_code}}</td>
                                <td>{{$product->price}}</td>

                                <td></td>


                            </tr>

                        @endforeach

                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>

@endsection