@extends('layouts.dashboard')

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <h4 class="m-t-0 m-b-30">Clients</h4>

                    <a class="btn btn-success" style="float: right; margin-left: 5%;" href="/merchant-admin/add-manager">Add New Manager</a>


                    <table id="datatable-buttons" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                        </thead>

                        <tbody>

                        @foreach($managers as $manager)

                            <tr>
                                <td>{{$manager->name}}</td>
                                <td>{{$manager->email}}</td>

                                <td>


                                    <form method="post"  style="float: left;  margin-right: 10%;"  action="/merchant-admin/managers">
                                        {{csrf_field()}}
                                        <input type="hidden" name="email" value="{{$manager->email}}">
                                        <button type="submit"  class="btn btn-warning"  style="float: left; margin-right: 10%;" >Delete Manager</button>
                                    </form>
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