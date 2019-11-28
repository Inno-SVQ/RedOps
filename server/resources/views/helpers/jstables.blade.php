@push('scripts')
    <script>

        var selectedItems = []
        $('#checkbox-all').change(function () {
            if ($(this).is(":checked")) {
                dataT = $('.table-datatable').DataTable({retrieve: true}).data();
                for(i = 0; i<dataT.length; i++) {
                    if(!selectedItems.includes(dataT[i]['DT_RowId'])) {
                        selectedItems.push(dataT[i]['DT_RowId'])
                    }
                }
            } else {
                selectedItems = []
            }
            checkActions();
            ($(this).is(":checked") ? $('.checkbox-item').prop("checked", true) : $('.checkbox-item').prop("checked", false))
            console.log(selectedItems)

        });
        $(document).on("change", ".checkbox-item" , function() {
            if ($(this).is(":checked")) {
                selectedItems.push($(this).attr("id"))
                console.log(selectedItems)
            } else {
                selectedItems = selectedItems.filter(e => e !== $(this).attr("id"));
                console.log(selectedItems)
            }
            checkActions();
        });
        function checkActions() {
            if (selectedItems.length === 0) {
                $('#dropdown-actions').addClass('disabled')
            } else {
                $('#dropdown-actions').removeClass('disabled')
            }
        }

    </script>
@endpush
