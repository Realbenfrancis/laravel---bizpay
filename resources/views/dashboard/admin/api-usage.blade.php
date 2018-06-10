@extends('layouts.dashboard')


@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <h4 class="m-t-0 m-b-30">API Response</h4>



                    <table id="datatable-buttons" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Email</th>
                            <th>Name</th>
                            <th>API Key</th>
                            <th>API Limit</th>
                            <th>API Usage</th>
                        </tr>
                        </thead>

                        <tbody>

                        @foreach($users as $user)

                            <tr>
                                <td>{{$user->email}}</td>
                                <td> {{$user->name}} </td>
                                <td>{{$user->api_token}}</td>
                                <td>{{$user->api_limit}}</td>
                                <td>{{$user->api_usage}}</td>
                            </tr>

                        @endforeach

                        </tbody>
                    </table>



                </div>
            </div>
        </div>

    </div>

@endsection