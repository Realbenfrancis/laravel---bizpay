@extends('layouts.dashboard')

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">

                <div class="panel-body">
                    <h4 class="m-t-0 m-b-30">Edit Profile</h4>
                    <form action="profile" method="post" class="form-horizontal" role="form">

                        {{csrf_field()}}

                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-3 control-label">Name</label>
                            <div class="col-sm-9">
                                <input type="text" name="name" class="form-control" id="inputEmail3" value="{{$user->name}}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">Email</label>
                            <div class="col-sm-9">
                                <input type="email" name="email" class="form-control" id="inputPassword3" value="{{$user->email}}">
                            </div>
                        </div>

                        @if(Request::segment(1)=="merchant-admin")

                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">API</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="inputPassword3" value="{{$user->api_token}}" readonly>
                            </div>
                        </div>

                        @endif

                        <div class="form-group">
                            <label for="inputPassword3" class="col-sm-3 control-label">Phone Number</label>
                            <div class="col-sm-9">
                                <input type="text" name="phone_number" class="form-control" id="inputPassword3" value="{{$user->phone_number}}">
                            </div>
                        </div>


                        <div class="form-group m-b-0">
                            <div class="col-sm-offset-3 col-sm-9">
                                <button type="submit" class="btn btn-info waves-effect waves-light">Update Profile</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>


@endsection