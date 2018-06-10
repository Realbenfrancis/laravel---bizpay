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
                            <th>Type</th>
                            <th>Merchant ID</th>
                            <th>Request</th>
                            <th>Status</th>
                            <th>Response Time</th>
                            <th>Time</th>
                        </tr>
                        </thead>

                        <tbody>

                        @foreach($logs as $log)

                            <tr>
                                <td>{{$log->action}}</td>
                                <td> {{\App\Http\Models\Merchant::findorFail($log->merchant_id)->merchant_name}} </td>
                                <td>{{$log->request}}</td>
                                <td>{{$log->error}}</td>
                                <td>{{$log->response_time}}</td>
                                <td>{{$log->created_at}}</td>
                            </tr>

                        @endforeach

                        </tbody>
                    </table>



                </div>
            </div>
        </div>

    </div>

@endsection