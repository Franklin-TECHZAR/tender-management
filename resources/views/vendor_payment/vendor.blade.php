@extends('layouts.master')
@section('title', 'Payment')

@section('content')
    <style>
        .job-order-column {
            display: none;
        }
    </style>
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-6">
                            <div class="title">
                                <h4>Vendor Payment Management</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ url('admin/dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        Create Vendor Payment
                                    </li>
                                </ol>
                            </nav>
                        </div>
                        {{-- <div class="col-md-6 d-flex justify-content-end align-items-center">
                            <button class="btn btn-primary add-btn" data-toggle="modal" data-target="#vendor-payment-modal">
                                <i class="bi bi-plus"></i> Create New
                            </button>
                        </div> --}}
                    </div>
                </div>
                <div class="page-header">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="col-md-6 d-flex justify-content-start align-items-center">
                                    <div class="form-group">
                                        {{-- <label for="job_orders">Filter by Job Order:</label>
                                        <select class="form-control" name="job_orders" id="job_orders" required
                                            style="width: 250px;">
                                            <option value="" selected>All</option>
                                            @foreach ($tenders as $id => $tenderName)
                                                <option value="{{ $id }}">{{ $tenderName }}</option>
                                            @endforeach
                                        </select> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pd-20 bg-white border-radius-4 box-shadow">
                    <table class="table table-bordered data-table">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th class="job-order-column">id</th>
                                {{-- <th>Job Order</th> --}}
                                {{-- <th>Date</th> --}}
                                <th>Shop Name</th>
                                <th>GST Number</th>
                                <th>Out Standing</th>
                                {{-- <th>Amount</th> --}}
                                <th width="100px">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align: right;"><strong>Total Outstanding:</strong></td>
                                <td id="total-outstanding" style="text-align: right;"></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="vendor_payment-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title-label">
                        New Payment
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        ×
                    </button>
                </div>
                <input type="hidden" id="edit_id">
                <form id="vendor_payment-form">
                    @csrf
                    <input type="hidden" name="vendor_balance_id" id="vendor_balance_id" value="">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="job_order">Job Order</label>
                            <select class="form-control" name="job_order" id="job_order" required>
                                <option value="" disabled selected hidden>Select Job Order</option>
                                @foreach ($tenders as $id => $tenderName)
                                    <option value="{{ $id }}">{{ $tenderName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" class="form-control" name="date" id="date" required>
                        </div>
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" class="form-control" name="amount" id="amount" required>
                        </div>
                        <div class="form-group">
                            <label for="payment_mode">Payment Mode</label>
                            <select class="form-control" name="payment_mode" id="payment_mode" required>
                                <option value="" disabled selected hidden>Select Payment Mode</option>
                                <option value="Cash">Cash</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Neft">Neft</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="payment_details">Payment Details</label>
                            <textarea class="form-control" name="payment_details" id="payment_details" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" id="description" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Close
                        </button>
                        <button type="submit" id="submit-btn" class="btn btn-primary">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection


@section('addscript')
    <script type="text/javascript">
        $(document).ready(function() {
            var submitted = false;
            $(document).ready(function() {
                submitDataTableData();

                function fetch_table() {
                    $.ajax({
                        url: "{{ url('vendor_payment/fetch') }}",
                        type: "GET",
                        dataType: "json",
                        success: function(response) {
                            $('.data-table tbody').empty();
                            var processedVendors = [];
                            var totalOutstanding = 0;

                            $.each(response.main_array, function(index, item) {
                                console.log('item', item);
                                if (item.data[0].agency_name && item.data[0]
                                    .gst_number) {
                                    var vendorId = item.data[0].vendor_id;
                                    var vendorPaymentId = item.data[0].VendorPayment_id;

                                    if (!processedVendors.includes(vendorId)) {
                                        processedVendors.push(
                                            vendorId);

                                        var row = '<tr>' +
                                            '<td>' + (index + 1) + '</td>' +
                                            '<td class="job-order-column">' + vendorId +
                                            '</td>' +
                                            '<td>' + item.data[0].agency_name +
                                            '</td>' +
                                            '<td>' + item.data[0].gst_number + '</td>' +
                                            '<td style="text-align: right;">' +
                                            (function() {
                                                var balanceAmount = parseFloat(item
                                                    .data[0].balance);
                                                var symbol = '';
                                                if (balanceAmount < 0) {
                                                    balanceAmount = -balanceAmount;
                                                    symbol = "-";
                                                }
                                                totalOutstanding += balanceAmount;
                                                return symbol + '₹' + balanceAmount
                                                    .toFixed(2);
                                            })() +
                                            // '<td><button data-id="' + vendorPaymentId +
                                            // '" class="payments-btn dropdown-item"><i class="bi bi-cash-stack"></i> Payments</button></td>' +
                                            '<td><button data-id="' + vendorPaymentId + '" class="payments-btn dropdown-item btn-primary text-white rounded"><i class="bi bi-cash-stack"></i> Payments</button></td>'
                                            '</tr>';
                                        $('.data-table tbody').append(row);
                                    }
                                }
                            });
                            $('#total-outstanding').text('₹' + totalOutstanding.toFixed(2));
                            submitDataTableData();
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                            alert("An error occurred while fetching data.");
                        }
                    });
                }

                fetch_table();



                function submitDataTableData() {
                    var dataRows = $('.data-table tbody').find('tr');

                    var uniqueVendors = [];
                    var uniqueData = [];
                    dataRows.each(function() {
                        var vendorId = $(this).find('td:nth-child(2)').text();
                        var agencyName = $(this).find('td:nth-child(3)').text();
                        var gstNumber = $(this).find('td:nth-child(4)').text();
                        var outStanding = $(this).find('td:nth-child(5)').text();

                        console.log(vendorId);
                        if (outStanding !== 'Payments' && !uniqueVendors.includes(vendorId)) {
                            uniqueVendors.push(vendorId);
                            var rowData = {
                                id: vendorId,
                                agency_name: agencyName,
                                gst_number: gstNumber,
                                out_standing: outStanding,
                            };
                            uniqueData.push(rowData);
                        }
                    });

                    $.ajax({
                        url: "{{ url('vendor_payment/store') }}",
                        method: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            data: uniqueData
                        },
                        dataType: "json",
                        success: function(response) {
                            // Handle success response if needed
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                            alert("An error occurred while processing your request.");
                        }
                    });
                }

            });



            $("#vendor_payment-form").validate({
                submitHandler: function(form) {
                    $("#submit-btn").prop("disabled", true);
                    var data = new FormData(form);
                    var url = "{{ url('vendor_payment/payment-store') }}";
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: data,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            $("#vendor_payment-modal").modal("hide");
                            $("#submit-btn").prop("disabled", false);
                            fetch_table();
                            toastr.success(response.message);
                        },
                        error: function(code) {
                            toastr.error(code.statusText);
                        },
                    });
                    return false;
                }
            });

            $(document).on("click", ".payments-btn", function() {
                var vendorId = $(this).data('id');
                $("#vendor_balance_id").val(vendorId);
                $("#vendor_payment-modal").modal("show");
            });

            var selectedVendor = $('#vendors').val();
            $('#vendors').on('change', function() {
                var filterValue = $(this).val();
                table.columns(1).search(filterValue).draw();
                selectedVendor = filterValue;
            });


            // table.on('draw', function() {
            //     $('#vendors').val(selectedVendor);
            // });
        });


        $(document).on("click", ".add-btn", function() {
            $("#edit_id").val("");
            $("#payment-form")[0].reset();
            $("#job_order").prop('disabled', false);
            $("#date").prop('disabled', false);
            $("#amount").prop('disabled', false);
            $("#description").prop('disabled', false);
            $("#payment_mode").prop('disabled', false);
            $("#payment_details").prop('disabled', false);
            $("#modal-title-label").html('Create Payment');
            $("#modal-footer-buttons").show();
        });

        $(document).on("click", ".delete-btn", function() {
            var edit_id = $(this).data('id');
            $("#edit_id").val(edit_id);
            $("#delete-confirm-text").text("Are you sure you want to delete this Payment?");
            $("#delete-confirm-modal").modal("show");
        });

        $(document).on("click", "#confirm-yes-btn", function() {
            var edit_id = $("#edit_id").val();
            $("#confirm-yes-btn").prop("disabled", true);
            $.ajax({
                url: "{{ url('vendor_payment/delete') }}/" + edit_id,
                method: "GET",
                dataType: "json",
                success: function(response) {
                    table.clear().draw();
                    $("#confirm-yes-btn").prop("disabled", false);
                },
                error: function(code) {
                    alert(code.statusText);
                },
            });
        });
    </script>
@endsection
