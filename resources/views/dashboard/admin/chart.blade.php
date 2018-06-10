@extends('layouts.dashboard')


@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-body">
                    <h4 class="m-t-0 m-b-30">API Usage</h4>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.bundle.min.js"></script>
    <script>
        var year = <?php echo $dates; ?>;
        var data_viewer = <?php echo $viewer; ?>;


        var barChartData = {
            labels: year,
            datasets: [ {
                label: 'Time in s',
                backgroundColor: "rgba(151,187,205,0.5)",
                data: data_viewer
            }]
        };


        window.onload = function() {
            var ctx = document.getElementById("canvas").getContext("2d");
            window.myBar = new Chart(ctx, {
                type: 'bar',
                data: barChartData,
                options: {
                    elements: {
                        rectangle: {
                            borderWidth: 2,
                            borderColor: 'rgb(0, 255, 0)',
                            borderSkipped: 'bottom'
                        }
                    },
                    responsive: true,
                    title: {
                        display: true,
                        text: 'API Latency'
                    }
                }
            });


        };
    </script>


    <div class="container">
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">API Latency</div>

                    <form method="get" action="">

                        <label for="">Merchant</label>


                        <select name="merchant-id">
                            @foreach($merchants as $merchant)
                            <option value="{{$merchant->id}}" @if(request()->has('merchant-id')) @if($merchant->id==request()->get('merchant-id')) selected   @endif @endif>{{$merchant->merchant_name}}</option>
                            @endforeach
                        </select>


                        <input type="submit" value="Fetch">

                    </form>


                    <div class="panel-body">
                        <canvas id="canvas" height="280" width="600"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>


                </div>
            </div>
        </div>

    </div>

@endsection