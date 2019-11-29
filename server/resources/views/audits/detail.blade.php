@extends('layouts.blank')

@push('stylesheets')
    <!-- Example -->
    <!--<link href=" <link href="{{ asset("css/myFile.min.css") }}" rel="stylesheet">" rel="stylesheet">-->
@endpush

@section('main_container')

    <!-- page content -->
    <div class="right_col" role="main">
        <!-- top tiles -->
        <div class="row tile_count">
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                <span class="count_top"><i class="fa fa-user"></i> Total domains</span>
                <div class="count">{{count($selectedAudit->domains())}}</div>
                <span class="count_bottom"><i class="green">4% </i> From last Week</span>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                <span class="count_top"><i class="fa fa-clock-o"></i> Total IPs</span>
                <div class="count">{{count($selectedAudit->domains())}}</div>
                <span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i>3% </i> From last Week</span>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                <span class="count_top"><i class="fa fa-user"></i> Total services</span>
                <div class="count green">{{count($selectedAudit->domains())}}</div>
                <span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i>34% </i> From last Week</span>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                <span class="count_top"><i class="fa fa-user"></i> Total Different technologies used</span>
                <div class="count">{{count($selectedAudit->differentTechnologies())}}</div>
                <span class="count_bottom"><i class="red"><i class="fa fa-sort-desc"></i>12% </i> From last Week</span>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                <span class="count_top"><i class="fa fa-user"></i> Total Credentials leaked</span>
                <div class="count">{{count($selectedAudit->getCredentials())}}</div>
                <span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i>34% </i> From last Week</span>
            </div>
            <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count">
                <span class="count_top"><i class="fa fa-user"></i> Total Jobs executed</span>
                <div class="count">{{count($selectedAudit->jobs())}}</div>
                <span class="count_bottom"><i class="green"><i class="fa fa-sort-asc"></i>34% </i> From last Week</span>
            </div>
        </div>
        <!-- /top tiles -->

        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="dashboard_graph">

                    <div class="row x_title">
                        <div class="col-md-6">
                            <h3>Job Activity <small>in last hours</small></h3>
                        </div>
                    </div>

                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <canvas id="myChart"></canvas>
                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-12 bg-white">
                        <div class="x_title">
                            <h2>Top Technologies used</h2>
                            <div class="clearfix"></div>
                        </div>

                        <div class="col-md-12 col-sm-12 col-xs-6">
                            <div>
                                @if(count($selectedAudit->differentTechnologies()) > 0)
                                    @foreach(array_slice($selectedAudit->differentTechnologies(), 0, min(count($selectedAudit->differentTechnologies()), 9)) as $technologyName => $count)
                                        <p>{{$technologyName}}</p>
                                        <div class="">
                                            <div class="progress progress_sm" style="width: 76%;">
                                                <div class="progress-bar bg-green" role="progressbar"
                                                     data-transitiongoal="{{$count*20}}"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    Any technology found
                                @endif
                            </div>
                        </div>

                    </div>

                    <div class="clearfix"></div>
                </div>
            </div>

        </div>
        <br/>

    </div>

    @push('scripts')
        <script>
            var ctx = document.getElementById('myChart').getContext('2d');
            var chart = new Chart(ctx, {
                // The type of chart we want to create
                type: 'line',

                // The data for our dataset
                data: {
                    labels: ['23h. ago', '22h. ago', '21h. ago', '20h. ago', '19h. ago', '18h. ago', '17h. ago', '16h. ago', '15h. ago', '14h. ago', '13h. ago', '12h. ago', '11h. ago', '10h. ago', '9h. ago', '8h. ago', '7h. ago', '6h. ago', '5h. ago', '4h. ago', '3h. ago', '2h. ago', '1h. ago', '0h. ago'],
                    datasets: [{
                        label: 'Domains discovered',
                        backgroundColor: 'rgba(53,152,219,0.43)',
                        data: {{json_encode($selectedAudit->getDomainInsertEvents())}}
                    }, {
                        label: 'Services discovered',
                        backgroundColor: 'rgba(37,185,154,0.43)',
                        data: {{json_encode($selectedAudit->getServiceInsertEvents())}}
                    }, {
                        label: 'Leaked credentials discovered',
                        backgroundColor: 'rgba(231,76,60,0.43)',
                        data: {{json_encode($selectedAudit->getCredntialsInsertEvents())}}
                    }]
                },

                // Configuration options go here
                options: {}
            });
        </script>
    @endpush
    <!-- /page content -->

@endsection