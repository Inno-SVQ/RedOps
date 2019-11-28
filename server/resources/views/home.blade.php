@extends('layouts.blank')

@push('stylesheets')
    <!-- Example -->
    <!--<link href=" <link href="{{ asset("css/myFile.min.css") }}" rel="stylesheet">" rel="stylesheet">-->
@endpush

@section('main_container')

    <!-- page content -->
    <div class="right_col" role="+">
        <ga-panel panel-title="Products"
                  panel-query="true"
                  panel-query-string="$ctrl.queryValue"
                  panel-add-record-url="#!/products/add">
            <panel-toolbar>
                <a href="" title="Some button"><i class="fa fa-modx"></i></a>
                <a href="" title="And another"><i class="fa fa-random"></i></a>
            </panel-toolbar>
            <table class="table table-hover dataTable">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                </tr>
                </thead>
                <tbody>
                <tr ng-repeat="product in $ctrl.products | filter:$ctrl.queryValue">
                    <th scope="row"><a href="#!/products/2">3</a></th>
                    <td><a href="#!/products/1">s</a></td>
                </tr>
                <tr ng-repeat="product in $ctrl.products | filter:$ctrl.queryValue">
                    <th scope="row"><a href="#!/products/2">4</a></th>
                    <td><a href="#!/products/1">r</a></td>
                </tr>
                </tbody>
            </table>
        </ga-panel>
    </div>
    <!-- /page content -->
@endsection