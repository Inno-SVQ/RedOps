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
                <h2>Credentials<small></small></h2>
                <ul class="nav navbar-right panel_toolbox">
                    <!-- Large modal -->
                    <button type="button" class="btn btn-primary" data-toggle="modal"
                            data-target=".bs-example-modal-lg">Add credential
                    </button>

                    <div id="modal-add-credential" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog"
                         aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title" id="myModalLabel">Add credential to "{{$selectedAudit->name}}
                                        "</h4>
                                    <button type="button" class="close" data-dismiss="modal"><span
                                                aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <div class="modal-footer">
                                    <button id="add-credential-cancel" type="button" class="btn btn-secondary"
                                            data-dismiss="modal">Close
                                    </button>
                                    <button id="add-credential-confirm" type="button" class="btn btn-default">Add credential
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

                <p class="text-muted font-13 m-b-30">
                    List of credentials found for this audit.
                </p>

                <table id="datatable-credentials"
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

                $('#btn-delete-credentials').click(function () {
                    $.post('{{ route('credentials/delete', $selectedAudit->id) }}', {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        data: JSON.stringify(selectedItems),
                    }, function (response) {
                        selectedItems = [];
                        checkActions();
                    }).error().success();
                });
                $('#show-modal-delete').click(function () {
                    $("#credentials-to-delete").empty();
                    dataT = $('#datatable-credentials').DataTable({retrieve: true}).data();
                    for (var i = 0; i < selectedItems.length; i++) {
                        for (var j = 0; j < dataT.length; j++) {
                            if (dataT[j]['DT_RowId'] === selectedItems[i]) {
                                $("#credentials-to-delete").append("<h5>" + dataT[j]['name'] + "</h5>");
                            }
                        }
                    }
                });
                $('#add-credential-confirm').click(function () {
                    credential = {
                        'username': $('#add-credential-username').val(),
                        'password': $('#add-credential-password').val(),
                        'domain': $('#add-credential-domain').val()
                    };

                    $('#modal-add-credential').modal('hide');

                    $.post('{{ route('credentials/add', $selectedAudit->id) }}', {
                        '_token': $('meta[name=csrf-token]').attr('content'),
                        data: JSON.stringify(credential),
                    }, function (response) {
                        $('#add-credential-username').empty()
                        $('#add-credential-password').empty()
                    }).error().success();
                });
                $(document).ready(function () {
                    if($('#datatable-credentials').length) {
                        table = $('#datatable-credentials').DataTable({
                            serverSide: true,
                            processing: true,
                            searchDelay: 400,
                            ajax: '{{ route('ajax/enumeration/credentials', $selectedAudit) }}',
                            columns: [
                                {data: "checkbox", name: "checkbox", orderable: false, searchable: false},
                                {data: "username", name: "name"},
                                {data: "password", name: "password"}
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