@extends('layouts.dashboard')

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <h4 class="m-t-0 m-b-30">Clients</h4>

                    <a class="btn btn-success" style="float: right; margin-left: 5%;" href="add-client">Add New Client</a>


                    <table id="datatable-buttons" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                        </thead>

                        <tbody>

                        @foreach($clients as $client)

                            <tr>
                                <td>{{$client->name}}</td>
                                <td>{{$client->email}}</td>

                                <td>


                                    <a style="float: left;" class="btn btn-success" href="/merchant-admin/client-subscriptions/{{$client->user_id}}">View Details</a>

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