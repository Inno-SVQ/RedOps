@extends('layouts.blank')

@push('stylesheets')
    <!-- Example -->
    <!--<link href=" <link href="{{ asset("css/myFile.min.css") }}" rel="stylesheet">" rel="stylesheet">-->
@endpush

@section('main_container')

    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Audits</h3>
                </div>

                <div class="title_right">
                    <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search for...">
                            <span class="input-group-btn">
                      <button class="btn btn-secondary" type="button">Go!</button>
                    </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Projects</h2>
                            <ul class="nav navbar-right panel_toolbox">
                                <a class="btn btn-primary" href="{{route('createAudit')}}" role="button">Create
                                    audit</a>
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            <p>List of all of your audits </p>

                            <!-- start project list -->
                            <table class="table table-striped projects">
                                <thead>
                                <tr>
                                    <th style="width: 1%">#</th>
                                    <th style="width: 20%">Audit name</th>
                                    <th>Team Members</th>
                                    <th>Audit Progress</th>
                                    <th>Status</th>
                                    <th style="width: 20%">#Edit</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($audits as $audit)
                                    <tr>
                                        <td>#</td>
                                        <td>
                                            <a>{{ $audit->name }}</a>
                                            <br/>
                                            <small>Created {{\Carbon\Carbon::parse($audit->created_at)->format('d.m.Y')}}</small>
                                        </td>
                                        <td>
                                            <ul class="list-inline">
                                                <li>
                                                    <img src="{{ Gravatar::src(Auth::user()->email) }}" class="avatar"
                                                         alt="{{ Auth::user()->email }}">
                                                </li>
                                            </ul>
                                        </td>
                                        <td class="project_progress">
                                            <div class="progress progress_sm">
                                                <div class="progress-bar bg-green" role="progressbar"
                                                     data-transitiongoal="{{ $audit->getProgress() }}"></div>
                                            </div>
                                            <small>{{ $audit->getProgress() }}% Complete</small>
                                        </td>
                                        <td>
                                            @if($audit->getProgress() == 100)
                                                <button type="button" class="btn btn-default btn-xs">Finished</button>
                                            @elseif($audit->getProgress() == 0)
                                                <button type="button" class="btn btn-default btn-xs">Not started</button>
                                            @else
                                                <button type="button" class="btn btn-success btn-xs">Open</button>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('auditDetail', $audit->id)  }}"
                                               class="btn btn-primary btn-xs"><i class="fa fa-folder"></i> Open </a>
                                            <a href="#" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit
                                            </a>
                                            <!-- Large modal -->
                                            <a type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target=".bs-modal-{{$audit->id}}"><i class="fa fa-trash-o"></i> Delete</a>

                                            <div class="modal fade bs-modal-{{$audit->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span>
                                                            </button>
                                                            <h4 class="modal-title" id="myModalLabel">Are you sure?</h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <h4>Delete "{{$audit->name}}"</h4>
                                                            <p>All the data will be deleted.</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                            <form action="{{ route('auditDelete', $audit->id) }}" method="post">
                                                                <input class="btn btn-danger" type="submit" value="Delete" />
                                                                <input type="hidden" name="_method" value="delete" />
                                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                            </form>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <!-- end project list -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('helpers/jstables', ['models' => array()])

    <!-- /page content -->
@endsection