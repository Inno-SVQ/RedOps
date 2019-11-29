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
                <h2>Services<small></small></h2>
                <ul class="nav navbar-right panel_toolbox">
                    <!-- Large modal -->
                    <button type="button" class="btn btn-primary" data-toggle="modal"
                            data-target="#modal-add-service">Add service
                    </button>

                    <div id="modal-add-service" class="modal fade bs-add-service-modal-lg" tabindex="-1" role="dialog"
                         aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel">Add service to "{{$selectedAudit->name}}
                                        "</h4>
                                    <button type="button" class="close" data-dismiss="modal"><span
                                                aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <?php
                                    $parentOptions = array('-' => '-');
                                    foreach ($selectedAudit->companies() as $company) {
                                        foreach ($company->domains() as $domain) {
                                            $parentOptions[$domain->id] = $domain->domain;
                                        }
                                    }
                                    ?>
                                    {!! BootForm::open() !!}
                                    {!! BootForm::select('add-service-host', 'Host', $parentOptions) !!}
                                    {!! BootForm::select('add-service-protocol', 'Protocol', array('TCP' => 'TCP','UDP' => 'UDP')) !!}
                                    {!! BootForm::text('add-service-port', 'Port') !!}
                                    {!! BootForm::select('add-service-application-protocol', 'Application protocol', array('ssh' => 'ssh','telnet' => 'telnet','http'=>'http','https'=>'https','ftp'=>'ftp','pop3'=>'pop3','imap'=>'imap','sip'=>'sip','unknown'=>'unknown')) !!}
                                    {!! BootForm::text('add-service-product', 'Product') !!}
                                    {!! BootForm::text('add-service-version', 'Version') !!}
                                </div>
                                <div class="modal-footer">
                                    <button id="add-company-cancel" type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Close
                                    </button>
                                    <button id="add-service-confirm" type="button" class="btn btn-default">Add service
                                    </button>
                                    {!! BootForm::close() !!}
                                </div>

                            </div>
                        </div>
                    </div>

                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="x_content">

                <div class="dropdown">
                    <button class="btn btn-default dropdown-toggle disabled" type="button" id="dropdown-actions"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        Actions
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <li><a id="action-find-webtechnologies">Scan web technologies</a></li>
                        <li><a id="show-modal-fuzz" data-toggle="modal" href="#modal-action-fuzz">Fuzz web directories</a></li>
                        <li><a id="show-modal-screenshots" data-toggle="modal" href="#modal-action-screenshots">Take screenshot of webpages</a></li>
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
                    <div id="modal-action-fuzz" class="modal fade bs-modal-" tabindex="-1" role="dialog"
                         aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><span
                                                aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel">Warning</h4>
                                </div>
                                <div class="modal-body">
                                    <p>Directory fuzzing is a heavy task. This webservices will be fuzzed:</p>
                                    <div id="services-to-fuzz">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button data-dismiss="modal" class="btn btn-warning" id="btn-fuzz-services"
                                            type="submit">Go fuzzing
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div id="modal-action-screenshots" class="modal fade bs-modal-" tabindex="-1" role="dialog"
                         aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"><span
                                                aria-hidden="true">×</span>
                                    </button>
                                    <h4 class="modal-title" id="myModalLabel">Warning</h4>
                                </div>
                                <div class="modal-body">
                                    <p>This screenshots will be taken:</p>
                                    <div id="services-to-screenshot">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button data-dismiss="modal" class="btn btn-primary" id="btn-screenshot-services"
                                            type="submit">Take screenshots
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <p class="text-muted font-13 m-b-30">
                    List of the services for this audit.
                </p>

                <table id="datatable-services"
                       class="table table-striped table-bordered dt-responsive nowrap table-datatable"
                       cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th><input type="checkbox" id="checkbox-all" aria-label="..."></th>
                        <th>Host</th>
                        <th>Protocol</th>
                        <th>Port</th>
                        <th>Application Protocol</th>
                        <th>Product</th>
                        <th>Version</th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>

        @include('helpers/jstables')

        @push('scripts')

            <script>

                $('#action-find-subdomains').click(function () {
                    $.post('{{ route('ajax/enumeration/companies/findSubdomains', $selectedAudit->id) }}', {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        data: JSON.stringify(selectedItems),
                    }).error().success();
                });
                $('#action-find-webtechnologies').click(function () {
                    $.post('{{ route('ajax/enumeration/services/webtechnologies', $selectedAudit->id) }}', {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        data: JSON.stringify(selectedItems),
                    }).error().success();
                });
                $('#btn-fuzz-services').click(function () {
                    fuzzItems = []
                    for (var i = 0; i < selectedItems.length; i++) {
                        for (var j = 0; j < dataT.length; j++) {
                            if (dataT[j]['DT_RowId'] === selectedItems[i] && (dataT[j]['application_protocol'] === 'http' || dataT[j]['application_protocol'] === 'https')) {
                                fuzzItems.push(dataT[i]['DT_RowId'])
                            }
                        }
                    }
                    $.post('{{ route('ajax/enumeration/services/fuzz', $selectedAudit->id) }}', {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        data: JSON.stringify(fuzzItems),
                    }).error().success();
                });
                $('#btn-screenshot-services').click(function () {
                    screenshotItems = []
                    for (var i = 0; i < selectedItems.length; i++) {
                        for (var j = 0; j < dataT.length; j++) {
                            if (dataT[j]['DT_RowId'] === selectedItems[i] && (dataT[j]['application_protocol'] === 'http' || dataT[j]['application_protocol'] === 'https')) {
                                screenshotItems.push(dataT[i]['DT_RowId'])
                            }
                        }
                    }
                    $.post('{{ route('ajax/enumeration/services/screenshot', $selectedAudit->id) }}', {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        data: JSON.stringify(screenshotItems),
                    }).error().success();
                });
                $('#show-modal-fuzz').click(function () {
                    $("#services-to-fuzz").empty();
                    dataT = $('#datatable-services').DataTable({retrieve: true}).data();
                    for (var i = 0; i < selectedItems.length; i++) {
                        for (var j = 0; j < dataT.length; j++) {
                            if (dataT[j]['DT_RowId'] === selectedItems[i] && (dataT[j]['application_protocol'] === 'http' || dataT[j]['application_protocol'] === 'https')) {
                                $("#services-to-fuzz").append("<h5>" + dataT[j]['host'] + "</h5>");
                            }
                        }
                    }
                });
                $('#show-modal-screenshots').click(function () {
                    $("#services-to-screenshot").empty();
                    dataT = $('#datatable-services').DataTable({retrieve: true}).data();
                    for (var i = 0; i < selectedItems.length; i++) {
                        for (var j = 0; j < dataT.length; j++) {
                            if (dataT[j]['DT_RowId'] === selectedItems[i] && (dataT[j]['application_protocol'] === 'http' || dataT[j]['application_protocol'] === 'https')) {
                                $("#services-to-screenshot").append("<h5>" + dataT[j]['host'] + "</h5>");
                            }
                        }
                    }
                });
                $('#action-fuzz-directories').click(function () {
                    $.post('{{ route('ajax/enumeration/services/fuzz', $selectedAudit->id) }}', {
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
                    if ($('#datatable-services').length) {
                        table = $('#datatable-services').DataTable({
                            dom: 'lBfrtip',
                            serverSide: true,
                            processing: true,
                            searchDelay: 400,
                            ajax: '{{ route('ajax/enumeration/services', $selectedAudit) }}',
                            columns: [
                                {data: "checkbox", name: "checkbox", orderable: false, searchable: false},
                                {data: "host", name: "host"},
                                {data: "protocol", name: "protocol"},
                                {data: "port", name: "port"},
                                {data: "application_protocol", name: "application_protocol"},
                                {data: "product", name: "product"},
                                {data: "version", name: "version"}
                            ],
                            aoColumnDefs: [
                                {'bSortable': true, 'aTargets': [1, 2, 3, 4, 5, 6]},
                                {'bSearchable': true, 'aTargets': [1, 2, 3, 4, 5, 6]}
                            ],
                            drawCallback: function () {
                                for (i = 0; i < selectedItems.length; i++) {
                                    $('#' + selectedItems[i] + '.checkbox-item').prop("checked", true);
                                }
                            }
                        });
                    }
                });

            </script>

        @endpush

    </div>
    <!-- /page content -->

@endsection