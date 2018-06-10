@include('includes.head')
@include('includes.header')
@include('includes.top')
@include('includes.sidebar')


<div class="content-page">


    <div class="content">

        @if(\Illuminate\Support\Facades\Session::has('message'))
            <div style="margin-bottom: 3%;" class="alert alert-info">{{(\Illuminate\Support\Facades\Session::get('message'))}}</div>
        @endif

        <div class="">
            <div class="page-header-title">
                <h4 class="page-title">
                    @if(Request::segment(1)=="client")

                        {{ \App\Http\Models\Merchant::findorFail(\Illuminate\Support\Facades\Auth::user()->merchant_id)->merchant_name  }} Dashboard

                        @else

                    Bizpay Dashboard

                        @endif
                </h4>
            </div>


        </div>

        <div class="page-content-wrapper ">

            <div class="container">

@yield('content')

            </div>

        </div>

    </div>

    <footer class="footer">
        © 2017 Bizpay - All Rights Reserved.
    </footer>

</div>

</div>
@include('includes.footer')
@include('includes.foot')
