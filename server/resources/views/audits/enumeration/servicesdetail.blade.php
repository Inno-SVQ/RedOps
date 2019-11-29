@extends('layouts.blank')

@push('stylesheets')
    <!-- Example -->
    <!--<link href=" <link href="{{ asset("css/myFile.min.css") }}" rel="stylesheet">" rel="stylesheet">-->
@endpush

@section('main_container')

    <!-- page content -->
    <div class="right_col" role="main">

        <div class="x_panel">
            <div class="x_title">
                <h2>{{$service->application_protocol}}://{{$service->getDomain()->domain}}:{{$service->port}}<small></small></h2>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">

                <div class="row">
                    <div class="col-md-9 col-sm-9">
                        <div class="dashboard_graph">
                            <div class="row x_title">
                                <div class="col-md-6">
                                    <h3>Technologies used in webservice</h3>
                                </div>
                            </div>
                            <ul class="quick-list technologies-list" id="technologies-list-{{$service->id}}">
                                @if(count($service->technologies()) > 0)
                                    @foreach($service->technologies() as $technology)
                                        <li>
                                            <img src="https://s3.dualstack.ap-southeast-2.amazonaws.com/assets.wappalyzer.com/images/icons/{{$technology->icon}}"
                                                 width="24" height="24"> {{$technology->name}}</li>
                                    @endforeach
                                @else
                                    <button id="fetch-technologies" type="button" class="btn btn-primary">Fetch used technologies</button>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-3">
                        <img src="/storage/{{($service->id)}}.jpeg" width="300">
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="row x_title">
                    <div class="col-md-6">
                        <h3>Directories found</h3>
                    </div>
                </div>

                <div class="dropdown">
                    <button class="btn btn-default dropdown-toggle disabled" type="button" id="dropdown-actions"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        Actions
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <li><a id="action-find-subdomains"></a></li>
                        <li role="separator" class="divider"></li>
                        <li><a id="show-modal-delete" data-toggle="modal" href="#modal-action-delete">Delete</a></li>
                    </ul>
                    <div id="modal-action-delete" class="modal fade bs-modal-" tabindex="-1" role="dialog"
                         aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><span
                                                aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel">Are you sure?</h4>
                                </div>
                                <div class="modal-body">
                                    <p>The next services will be deleted:</p>
                                    <div id="services-to-delete">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button data-dismiss="modal" class="btn btn-danger" id="btn-delete-services"
                                            type="submit">Delete
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <p class="text-muted font-13 m-b-30">
                    List of directories of this webservice
                </p>

                <table id="datatable-{{$service->id}}-directories"
                       class="table table-striped table-bordered dt-responsive nowrap table-datatable"
                       cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th><input type="checkbox" id="checkbox-all" aria-label="..."></th>
                        <th>Path</th>
                        <th>Type</th>
                        <th>Word length</th>
                        <th>Char length</th>
                        <th>Return code</th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>

        @include('helpers.jstables')

        @push('scripts')

            <script>

                $('#action-find-subdomains').click(function () {
                    $.post('{{ route('ajax/enumeration/companies/findSubdomains', $selectedAudit->id) }}', {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        data: JSON.stringify(selectedItems),
                    }).error().success();
                });
                $('#btn-delete-services').click(function () {
                    $.post('{{ route('services/delete', $selectedAudit->id) }}', {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        data: JSON.stringify(selectedItems),
                    }, function (response) {
                        selectedItems = [];
                        checkActions();
                    }).error().success();
                });
                $('#fetch-technologies').click(function () {
                    $.post('{{ route('ajax/enumeration/services/webtechnologies', $selectedAudit->id) }}', {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        data: JSON.stringify(['{{$service->id}}']),
                    }).error().success();
                });
                $('#show-modal-delete').click(function () {
                    $("#services-to-delete").empty();
                    dataT = $('#datatable-services').DataTable({retrieve: true}).data();
                    for (var i = 0; i < selectedItems.length; i++) {
                        for (var j = 0; j < dataT.length; j++) {
                            if (dataT[j]['DT_RowId'] === selectedItems[i]) {
                                $("#services-to-delete").append("<h5>Host: " + dataT[j]['host'] + ", Port: " + dataT[j]['port'] + "</h5>");
                            }
                        }
                    }
                });

                $('#add-service-confirm').click(function () {
                    service = {
                        'host': $('#add-service-host').val(),
                        'protocol': $('#add-service-protocol').val(),
                        'port': $('#add-service-port').val(),
                        'application_protocol': $('#add-service-application-protocol').val(),
                        'product': $('#add-service-product').val(),
                        'version': $('#add-service-version').val(),
                    };

                    $('#modal-add-service').modal('hide');

                    $.post('{{ route('services/add', $selectedAudit->id) }}', {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        data: JSON.stringify(service),
                    }, function (response) {
                        $('#add-service-port').empty()
                        $('#add-service-product').empty()
                        $('#add-service-version').empty()
                    }).error().success();
                });
                $(document).ready(function () {
                    if ($('#datatable-{{$service->id}}-directories').length) {
                        table = $('#datatable-{{$service->id}}-directories').DataTable({
                            serverSide: true,
                            processing: true,
                            searchDelay: 400,
                            ajax: '{{ route('ajax/enumeration/services/directories', ['id' => $selectedAudit->id, 'serviceid' => $service->id]) }}',
                            columns: [
                                {data: "checkbox", name: "checkbox", orderable: false, searchable: false},
                                {data: "path", name: "path"},
                                {data: "file_type", name: "file_type"},
                                {data: "word_length", name: "word_length"},
                                {data: "char_length", name: "char_length"},
                                {data: "status_code", name: "status_code"},
                            ],
                            aoColumnDefs: [
                                {'bSortable': true, 'aTargets': [1, 2, 3, 4, 5]},
                                {'bSearchable': true, 'aTargets': [1, 2, 3, 4, 5]}
                            ],
                            drawCallback: function () {
                                for (i = 0; i < selectedItems.length; i++) {
                                    $('#' + selectedItems[i] + '.checkbox-item').prop("checked", true);
                                }
                            }
                        });
                    }
                });

                function addTechnologies(data) {
                    if($('#technologies-list-{{$service->id}}').length) {
                        $('#technologies-list-{{$service->id}}').empty();
                        for(let i = 0; i < data.data.length; i++){
                            if(data.data[i].type === '__technology__' && data.data[i].serviceId === '{{$service->id}}') {
                                $("#technologies-list-{{$service->id}}").append('<li><img src="https://s3.dualstack.ap-southeast-2.amazonaws.com/assets.wappalyzer.com/images/icons/' + data.data[i].icon + '"width="24" height="24"> ' +  data.data[i].name + '</li>');
                            }
                        }
                    }
                }

            </script>

        @endpush

    </div>
    <!-- /page content -->

@endsection