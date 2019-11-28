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
                <h2>Companies<small></small></h2>
                <ul class="nav navbar-right panel_toolbox">
                    <!-- Large modal -->
                    <button type="button" class="btn btn-primary" data-toggle="modal"
                            data-target=".bs-example-modal-lg">Add company
                    </button>

                    <div id="modal-add-company" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog"
                         aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel">Add company to "{{$selectedAudit->name}}
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
                                    {!! BootForm::text('add-company-name', 'Company Name') !!}
                                    {!! BootForm::select('add-company-parent', 'Parent Company', $parentOptions); !!}
                                    {!! BootForm::select('add-company-country', 'Country', array()); !!}
                                    {!! BootForm::text('add-company-domain', 'Main domain (if known)'); !!}
                                    {!! BootForm::text('add-company-linkedin', 'Linkedin (if known)'); !!}
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
                        <li><a id="action-find-subdomains">Find domains</a></li>
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
                                    <div id="companies-to-delete">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                    <button data-dismiss="modal" class="btn btn-danger" id="btn-delete-companies"
                                            type="submit">Delete
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                <p class="text-muted font-13 m-b-30">
                    List of the companies for this audit.
                </p>

                <table id="datatable-companies"
                       class="table table-striped table-bordered dt-responsive nowrap table-datatable"
                       cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th><input type="checkbox" id="checkbox-all" aria-label="..."></th>
                        <th>Name</th>
                        <th>Parent</th>
                        <th>Country</th>
                        <th>Main domain</th>
                        <th>LinkedIn</th>
                    </tr>
                    </thead>
                </table>

            </div>
        </div>

        @include('helpers/jstables')

        @push('scripts')

            <script>

                $('#action-find-subdomains').click(function () {
                    $.post('{{ route('ajax/enumeration/companies/findDomains', $selectedAudit->id) }}', {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        data: JSON.stringify(selectedItems),
                    }).error().success();
                });
                $('#btn-delete-companies').click(function () {
                    $.post('{{ route('companies/delete', $selectedAudit->id) }}', {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        data: JSON.stringify(selectedItems),
                    }, function (response) {
                        selectedItems = [];
                        checkActions();
                    }).error().success();
                });
                $('#show-modal-delete').click(function () {
                    $("#companies-to-delete").empty();
                    dataT = $('#datatable-companies').DataTable({retrieve: true}).data();
                    for (var i = 0; i < selectedItems.length; i++) {
                        for (var j = 0; j < dataT.length; j++) {
                            if (dataT[j]['DT_RowId'] === selectedItems[i]) {
                                $("#companies-to-delete").append("<h5>" + dataT[j]['name'] + "</h5>");
                            }
                        }
                    }
                });
                $('#add-company-confirm').click(function () {
                    company = {
                        'name': $('#add-company-name').val(),
                        'parent': $('#add-company-parent').val(),
                        'country': $('#add-company-country').val(),
                        'domain': $('#add-company-domain').val(),
                        'linkedin': $('#add-company-linkedin').val()
                    };

                    $('#modal-add-company').modal('hide');

                    $.post('{{ route('companies/add', $selectedAudit->id) }}', {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        data: JSON.stringify(company),
                    }, function (response) {
                        $('#add-company-name').empty()
                        $('#add-company-domain').empty()
                        $('#add-company-linkedin').empty()
                    }).error().success();
                });
                $(document).ready(function () {
                    if($('#datatable-companies').length) {
                        table = $('#datatable-companies').DataTable({
                            serverSide: true,
                            processing: true,
                            searchDelay: 400,
                            ajax: '{{ route('ajax/enumeration/companies', $selectedAudit) }}',
                            columns: [
                                {data: "checkbox", name: "checkbox", orderable: false, searchable: false},
                                {data: "name", name: "name"},
                                {data: "parent", name: "parent"},
                                {data: "country", name: "country"},
                                {data: "domain", name: "domain"},
                                {data: "linkedin", name: "linkedin"}
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

            </script>

        @endpush

    </div>
    <!-- /page content -->

@endsection