@extends('layouts.dashboard')

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <h4 class="m-t-0 m-b-30">Subscription</h4>


                    <a style="float: right;" class="btn btn-success" href="/merchant-admin/add-card">Update Current Card</a>



                    <form method="post" action="/merchant-admin/subscribe">
                        {{csrf_field()}}
                        <button type="submit" class="btn btn-success">Subscribe</button>
                    </form>

                </div>
            </div>
        </div>

    </div>

@endsection