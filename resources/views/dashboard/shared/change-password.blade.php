@extends('layouts.dashboard')

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <h4 class="m-t-0 m-b-30">Change Password</h4>

                    <form method="post" action="change-password" class="content-form">

                        {{csrf_field()}}

                        <div class="form-group">
                            <label for="password">Current Password</label>
                            <input name="password" type="password" class="form-control material" id="password">
                        </div>


                        <div class="form-group">
                            <label for="password_new">New Password (minimum 6 characters)</label>
                            <input type="password" name="password_new" class="form-control material" id="password_new">
                        </div>


                        <div class="form-group">
                            <label for="password_confirm">Confirm New Password</label>
                            <input type="password" name="password_confirm" class="form-control material" id="password_confirm">
                        </div>


                        <div class="form-group">
                            <input type="submit" value="Change Password" class="btn btn-success">
                        </div>
                    </form>



                </div>
            </div>
        </div>

    </div>

@endsection