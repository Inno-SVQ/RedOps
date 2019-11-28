@extends('layouts.blank')

@push('stylesheets')
    <!-- Example -->
    <!--<link href=" <link href="{{ asset("css/myFile.min.css") }}" rel="stylesheet">" rel="stylesheet">-->
@endpush

@section('main_container')

    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Jobs for "{{$selectedAudit->name}}"</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <!-- start project list -->
                            <table id="datatable-jobs" class="table table-striped table-bordered dt-responsive nowrap companies"
                                   cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th style="width: 15%">Name</th>
                                    <th style="width: 25%">Status</th>
                                    <th style="width: 15%">Date</th>
                                    <th style="width: 40%">Entities added</th>
                                    <th style="width: 5%">#Edit</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($selectedAudit->jobs() as $job)
                                    <tr id="tr-{{$job->id}}">
                                        <td>
                                            <a>{{ \App\Http\Controllers\JobsController::getHumanModuleName($job->module) }}</a>
                                            <br/>
                                            <small>({{$job->module}})</small>
                                        </td>
                                        <td class="project_progress">
                                            <div class="progress progress_sm">
                                                <div class="progress-bar bg-green" role="progressbar"
                                                     data-transitiongoal="{{ $job->progress }}"></div>
                                            </div>
                                            @if($job->progress == -1)
                                                <small>Job Running</small>
                                            @elseif($job->progress == 100)
                                                <small>Finished</small>
                                            @else
                                                <small>Job Running ({{$job->progress}})</small>
                                            @endif
                                        </td>
                                        <td class="project_progress">
                                            Created at {{\Carbon\Carbon::parse($job->created_at)->format('d.m.Y h:i')}}
                                            </br>
                                            <small>{{\Carbon\Carbon::createFromTimeStamp(strtotime($job->created_at))->diffForHumans()}}</small>
                                        </td>
                                        <td class="project_progress">
                                            @include('audits.jobs.accordiondomainsadded')
                                            @include('audits.jobs.accordionservicesadded')
                                        </td>
                                        <td class="project_progress">
                                            @if($job->status !== 2)
                                                <a type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target=".bs-modal-{{$job->id}}"><i class="fa fa-remove"></i>Stop</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('helpers.jstables', ['models' => array()])

    @push('scripts')

        <script>
            $(document).ready(function () {
                if ($('#datatable-jobs').length) {
                    table = $('#datatable-jobs').DataTable({
                        columns: [
                            {data: "name", name: "name"},
                            {data: "status", name: "status"},
                            {data: "date", name: "date"},
                            {data: "entities", name: "entities"},
                            {data: "edit", name: "edit"}
                        ],
                        aoColumnDefs: [
                            {'bSortable': true, 'aTargets': [1, 2, 3]},
                            {'bSearchable': true, 'aTargets': [1, 2, 3]}
                        ]
                    });
                }
            });
        </script>

    @endpush

    <!-- /page content -->
@endsection