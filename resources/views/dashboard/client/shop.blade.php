
@extends('layouts.dashboard')

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <h4 class="m-t-0 m-b-30">Shop</h4>


                    @foreach($products as $product)

                    <div class="col-sm-12">
    <div class="panel">
        <div class="panel-body user-card">
            <div class="media-main">
                <a class="pull-left" href="#">
                    <img class="thumb-lg img-circle" src="" alt="">
                </a>
                <div class="info">
                    <h4>{{$product->name}}</h4>
                    <p class="text-muted">{{$product->currency_code}} {{$product->price}}</p>
                </div>
            </div>
            <div class="clearfix"></div>

            <p class="text-muted info-text">


                {{$product->description}}
            </p>
            <hr>

            @if($product->type==1)

                <form method="post" action="/client/shop">
                    {{csrf_field()}}
                    <input type="hidden" name="id" value="{{$product->product_id}}">
                    <button style="float: right;" class="btn btn-primary" type="submit">Buy Now</button>
                </form>


            @elseif($product->type==2)

                <form method="post" action="/client/shop">
                    {{csrf_field()}}
                    <input type="hidden" name="id" value="{{$product->product_id}}">
                    <button style="float: right;" class="btn btn-primary" type="submit">Subscribe</button>
                </form>


            @elseif($product->type==3)

                <form method="post" action="/client/instalment">
                    {{csrf_field()}}
                    <input type="hidden" name="id" value="{{$product->product_id}}">
                    <button style="float: right;" class="btn btn-primary" type="submit">Select Instalment Plan</button>
                </form>

            @endif

            <ul class="social-links list-inline m-b-0">
                {{--<li>--}}
                    {{--<a title="" data-placement="top" data-toggle="tooltip" class="tooltips" href="" data-original-title="Facebook"><i class="fa fa-facebook"></i></a>--}}
                {{--</li>--}}

            </ul>
        </div> <!-- panel-body -->
    </div>
</div>

                        @endforeach


                </div>

            </div>

        </div>

    </div>


    @endsection
