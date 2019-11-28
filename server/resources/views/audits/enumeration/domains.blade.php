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
                <h2>Domains<small></small></h2>
                <ul class="nav navbar-right panel_toolbox">
                    <!-- Large modal -->
                    <button type="button" class="btn btn-primary" data-toggle="modal"
                            data-target="#modal-add-domain">Add domain
                    </button>
                    <button type="button" class="btn btn-primary" data-toggle="modal"
                            data-target="#modal-add-subdomain">Add subdomain
                    </button>

                    <div id="modal-add-domain" class="modal fade bs-add-domain-modal-lg" tabindex="-1" role="dialog"
                         aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel">Add domain to "{{$selectedAudit->name}}
                                        "</h4>
                                    <button type="button" class="close" data-dismiss="modal"><span
                                                aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <?php
                                    $parentOptions = array('-' => '-');
                                    foreach ($selectedAudit->companies() as $company) {
                                        $parentOptions[$company->id] = $company->name;
                                    }
                                    ?>
                                    {!! BootForm::open() !!}
                                    {!! BootForm::text('add-domain-domain', 'Domain') !!}
                                    {!! BootForm::select('add-domain-company_id', 'Company', $parentOptions); !!}
                                </div>
                                <div class="modal-footer">
                                    <button id="add-company-cancel" type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Close
                                    </button>
                                    <button id="add-domain-confirm" type="button" class="btn btn-default">Add domain
                                    </button>
                                    {!! BootForm::close() !!}
                                </div>

                            </div>
                        </div>
                    </div>

                    <div id="modal-add-subdomain" class="modal fade bs-add-subdomain-modal-lg" tabindex="-1"
                         role="dialog"
                         aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel">Add subdomain to "{{$selectedAudit->name}}
                                        "</h4>
                                    <button type="button" class="close" data-dismiss="modal"><span
                                                aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <?php
                                    $parentOptions = array('-' => '-');
                                    foreach ($selectedAudit->companies() as $company) {
                                        $parentOptions[$company->id] = $company->name;
                                    }
                                    ?>
                                    {!! BootForm::open() !!}
                                    {!! BootForm::text('add-company-name', 'Domain') !!}
                                    {!! BootForm::select('add-company-parent', 'Company', $parentOptions); !!}
                                    {!! BootForm::text('add-company-domain', 'Main domain (if known)'); !!}
                                </div>
                                <div class="modal-footer">
                                    <button id="add-company-cancel" type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Close
                                    </button>
                                    <button id="add-company-confirm" type="button" class="btn btn-default">Add company
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
                        <li><a id="action-find-subdomains">Find subdomains</a></li>
                        <li><a id="action-resolve-domains">Resolve Domains</a></li>
                        <li><a data-toggle="modal" href="#modal-action-find-services">Find
                                services</a></li>
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
                                    <p>The next companies will be deleted:</p>
                                    <div id="domains-to-delete">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button data-dismiss="modal" class="btn btn-danger" id="btn-delete-domains"
                                            type="submit">Delete
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div id="modal-action-find-services" class="modal fade bs-add-service-modal-lg" tabindex="-1"
                         role="dialog"
                         aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel">Port scan</h4>
                                    <button type="button" class="close" data-dismiss="modal"><span
                                                aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="form-group row">
                                        <select id="find-services-protocol" class="form-control">
                                            <option value="TCP">TCP</option>
                                            <option value="UDP">UDP</option>
                                        </select>
                                    </div>
                                    <div class="form-group row">
                                        <input id="find-services-ports" type="text" class="form-control"
                                               placeholder="80,22-25,...">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button id="add-company-cancel" type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Close
                                    </button>
                                    <button id="action-find-services"  type="button" class="btn btn-default">Find services
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>

                </div>

                <p class="text-muted font-13 m-b-30">
                    List of the domains for this audit.
                </p>

                <table id="datatable-domains" class="table table-striped table-bordered dt-responsive nowrap table-datatable"
                       cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th style="width: 2%;"><input type="checkbox" id="checkbox-all" aria-label="..."></th>
                        <th style="width: 33%;">Domain</th>
                        <th style="width: 25%;">Parent Domain</th>
                        <th style="width: 20%;">Parent Company</th>
                        <th style="width: 20%;">IP</th>
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
                $('#action-find-services').click(function () {
                    $.post('{{ route('ajax/enumeration/companies/findServices', $selectedAudit->id) }}', {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        data: JSON.stringify(selectedItems),
                        protocol: $('#find-services-protocol').val(),
                        port: $('#find-services-ports').val()
                    }, function (response) {
                        $('#find-services-ports').empty()
                    }).error().success();
                });

                $('#btn-delete-domains').click(function () {
                    $.post('{{ route('domains/delete', $selectedAudit->id) }}', {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        data: JSON.stringify(selectedItems),
                    }, function (response) {
                        selectedItems = [];
                        checkActions();
                    }).error().success();
                });
                $('#show-modal-delete').click(function () {
                    $("#domains-to-delete").empty();
                    dataT = $('#datatable-domains').DataTable({retrieve: true}).data();
                    for (var i = 0; i < selectedItems.length; i++) {
                        for (var j = 0; j < dataT.length; j++) {
                            if (dataT[j]['DT_RowId'] === selectedItems[i]) {
                                $("#domains-to-delete").append("<h5>" + dataT[j]['domain'] + "</h5>");
                            }
                        }
                    }
                });
                $('#add-domain-confirm').click(function () {
                    domain = {
                        'domain': $('#add-domain-domain').val(),
                        'company_id': $('#add-domain-company_id').val(),
                        'domain_id': $('#add-domain-domain_id').val()
                    };

                    $('#modal-add-domain').modal('hide');

                    $.post('{{ route('domains/add', $selectedAudit->id) }}', {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        data: JSON.stringify(domain),
                    }, function (response) {
                        $('#add-domain-domain').empty()
                    }).error().success();
                });
                $(document).ready(function () {
                    if ($('#datatable-domains').length) {
                        table = $('#datatable-domains').DataTable({
                            serverSide: true,
                            processing: true,
                            searchDelay: 400,
                            ajax: '{{ route('ajax/enumeration/domains', $selectedAudit) }}',
                            columns: [
                                {data: "checkbox", name: "checkbox", orderable: false, searchable: false},
                                {data: "domain", name: "domain"},
                                {data: "domain_id", name: "domain_id"},
                                {data: "company_id", name: "company_id"},
                                {data: "ip", name: "ip"}
                            ],
                            aLengthMenu: [[10, 25, 50, 100,500,1000,-1], [10, 25, 50,100,500,1000, "All"]],
                            aoColumnDefs: [
                                {'bSortable': true, 'aTargets': [1, 2, 3, 4]},
                                {'bSearchable': true, 'aTargets': [1, 2, 3, 4]}
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