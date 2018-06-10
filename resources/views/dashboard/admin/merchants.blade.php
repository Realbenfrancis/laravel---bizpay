@extends('layouts.dashboard')

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <h4 class="m-t-0 m-b-30">Merchants</h4>

                    <a class="btn btn-success" style="float: right; margin-left: 5%;" href="/admin/add-merchant">Add New Merchant</a>


                    <table id="datatable-buttons" class="table table-striped table-bordered">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>Merchant ID</th>
                            <th>Actions</th>
                        </tr>
                        </thead>

                        <tbody>

                        @foreach($merchants as $merchant)

                            <tr>
                                <td>{{$merchant->merchant_name}}</td>
                                <td>{{$merchant->merchant_id}}</td>

                                <td>

                                    <form method="post"   style="float: left;  margin-right: 10%;"  action="/admin/delete-merchant">
                                        {{csrf_field()}}
                                        <input type="hidden" name="id" value="{{$merchant->id}}">
                                        <button type="submit" class="btn btn-primary"  style="float: left; margin-right: 10%;" >Delete Merchant</button>
                                    </form>

                                    @if($merchant->status==1)

                                    <form method="post"  style="float: left;  margin-right: 10%;"  action="/admin/disable-merchant">
                                        {{csrf_field()}}
                                        <input type="hidden" name="id" value="{{$merchant->id}}">
                                        <button type="submit"  class="btn btn-warning"  style="float: left; margin-right: 10%;" >Disable Merchant</button>
                                    </form>

                                    @else


                                    <form method="post"  style="float: left;  margin-right: 10%;"  action="/admin/enable-merchant">
                                        {{csrf_field()}}
                                        <input type="hidden" name="id" value="{{$merchant->id}}">
                                        <button type="submit"  class="btn btn-success"  style="float: left; margin-right: 10%;" >Enable Merchant</button>
                                    </form>

                                    @endif


                                    @if($merchant->direct_client!=1)

                                        <form method="post"  style="float: left;"  action="/admin/enable-direct-merchant">
                                            {{csrf_field()}}
                                            <input type="hidden" name="id" value="{{$merchant->id}}">
                                            <button type="submit"  class="btn btn-success"  style="float: left; margin-right: 10%;" >Enable  Full Access </button>
                                        </form>

                                    @else


                                        <form method="post"  style="float: left;  "  action="/admin/disable-direct-merchant">
                                            {{csrf_field()}}
                                            <input type="hidden" name="id" value="{{$merchant->id}}">
                                            <button type="submit"  class="btn btn-success"  style="float: left; margin-right: 10%;" >Disable Full Access </button>
                                        </form>

                                    @endif


                                    {{--<a style="float: left;" class="btn btn-success" href="/admin/merchant-details/{{$merchant->merchant_id}}">View Details</a>--}}

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