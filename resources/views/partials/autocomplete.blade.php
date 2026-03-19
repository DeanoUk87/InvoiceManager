<script type="text/javascript">

    /*Customers Details*/
    $("#customersAutocomplete").autocomplete({
        minLength:1,
        source: function (request, response) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "{{route('customers.auto')}}",//url
                data: { searchText: request.term, '_token': csrf_token},
                dataType: "json",
                type: "POST",
                success: function (data) {
                    response($.map(data, function (item) {
                        return {
                            label: item.value,
                            customer: item.value,
                            id: item.id
                        }
                    }))
                },
                error: function (xhr, status, err) {
                    alert("Error")
                }
            });
        },
        select: function (even, ui) {
            $("#customer_account").val(ui.item.customer);
        }
    });



    /*Drivers Details*/
    $("#invoiceAutocomplete").autocomplete({
        minLength:1,
        source: function (request, response) {
            var csrf_token = $('meta[name="csrf-token"]').attr('content');
            $.ajax({
                url: "{{route('invoice.auto')}}",//url
                data: { searchText: request.term, '_token': csrf_token},
                dataType: "json",
                type: "POST",
                success: function (data) {
                    response($.map(data, function (item) {
                        return {
                            label: item.value,
                            invoice_no: item.value,
                            id: item.id
                        }
                    }))
                },
                error: function (xhr, status, err) {
                    alert("Error")
                }
            });
        },
        select: function (even, ui) {
            $("#invoice_number").val(ui.item.invoice_no);
        }
    });
</script>