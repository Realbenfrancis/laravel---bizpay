@extends('layouts.dashboard')


@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <h4 class="m-t-0 m-b-30">API Response</h4>


                    <pre id="api-json-resp">{{json_encode($result, JSON_PRETTY_PRINT)}}</pre>



                </div>
            </div>
        </div>

    </div>

@endsection