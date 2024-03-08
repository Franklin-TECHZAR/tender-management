@extends('layouts.master')
@section('title', 'Expenses')
@section('content')
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-6">
                            <div class="title">
                                <h4>Expenses Management</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ url('admin/dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        Expenses
                                    </li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-md-6 d-flex justify-content-end align-items-center">
                            {{-- <button class="btn btn-success expense_export mr-2">
                                <i class="bi bi-file-earmark-excel"></i> Export
                            </button> --}}
                            <button class="btn btn-primary add-btn" data-toggle="modal" data-target="#expense-modal">
                                <i class="bi bi-plus"></i> Create New
                            </button>
                        </div>
                    </div>
                </div>
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-3">
                            <label for="job_orders">Filter by Job Order:</label>
                            <select class="form-control" name="job_orders" id="job_orders" required>
                                <option value="" selected>All</option>
                                @foreach ($tenders as $id => $tenderName)
                                    <option value="{{ $id }}">{{ $tenderName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label for="type">Filter by Type:</label>
                            <select class="form-control" name="type" id="type" required>
                                <option value="" selected>All</option>
                                @foreach ($ExpenseType as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="date_range">Date Range:</label>
                                <input type="text" class="form-control" name="date_range" id="date_range">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="total_amount">Total Amount:</label>
                                <input type="text" class="form-control" id="total_amount" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pd-20 bg-white border-radius-4 box-shadow">
                    <table class="table table-bordered data-table">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>Job Order</th>
                                <th>Payment To</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                {{-- <th>Description</th> --}}
                                <th>Payment Mode</th>
                                {{-- <th>Payment Details</th> --}}
                                <th width="100px">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="expense-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title-label">
                        Create Expense
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        ×
                    </button>
                </div>
                <form id="expense-form">
                    @csrf
                    <input type="hidden" name="edit_id" id="edit_id">
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
                            <label>Payment To</label>
                            <input type="text" class="form-control" name="payment_to" id="payment_to" required>
                        </div>
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" class="form-control" name="date" id="date" required>
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <select class="form-control" name="type" id="type" required>
                                <option value="" disabled selected hidden>Select Type</option>
                                @foreach ($ExpenseType as $type)
                                    <option value="{{ $type }}">{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" class="form-control" name="amount" id="amount" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" id="description"></textarea>
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
                            <label>Payment Details</label>
                            <textarea class="form-control" name="payment_details" id="payment_details" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer" id="modal-footer-buttons">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="close-btn">
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

            $('#date_range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            $('#date_range').on('apply.daterangepicker', function(ev, picker) {
                var startDate = picker.startDate;
                var endDate = picker.endDate;
                var currentDate = startDate.clone();
                var dateRangeString = '';

                while (currentDate.isSameOrBefore(endDate)) {
                    dateRangeString += currentDate.format('DD-MM-YYYY') + '|';
                    currentDate.add(1, 'day');
                }

                dateRangeString = dateRangeString.slice(0, -1);
                $(this).val(startDate.format('DD-MM-YYYY') + ' - ' + endDate.format('DD-MM-YYYY'));
                $(this).trigger('change');
                table.column(3).search(dateRangeString, true).draw();
            });


            $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                table.column(3).search('').draw();
                calculateExpenseTotal();
            });

            function calculateExpenseTotal() {
                $.ajax({
                    url: "{{ url('expenses/fetch') }}",
                    success: function(response) {
                        var total = 0;
                        var filteredJobOrder = $('#job_orders').val();
                        var filteredDateRange = $('#date_range').val();
                        var filteredType = $('#type').val();

                        console.log('filteredDateRange', filteredDateRange);
                        console.log('response', response);
                        $.each(response.data, function(index, row) {
                            if (!row.deleted_at) {
                                var rowDate = new Date(row.date);
                                var startDate = new Date(filteredDateRange.split(' - ')[0]);
                                var endDate = new Date(filteredDateRange.split(' - ')[1]);

                                if ((filteredJobOrder === '' || row.job_order ===
                                        filteredJobOrder) &&
                                    (filteredDateRange === '' || (rowDate >= startDate &&
                                        rowDate <= endDate)) &&
                                    (filteredType === '' || row.type === filteredType)) {
                                    var amount = parseFloat(row.amount.replace(/[^\d.]/g, ''));
                                    total += isNaN(amount) ? 0 : amount;
                                }
                            }
                        });

                        var formattedTotal = '₹ ' + total.toLocaleString('en-IN', {
                            maximumFractionDigits: 2,
                            minimumFractionDigits: 2
                        }) + ' /-';
                        $('#total_amount').val(formattedTotal);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }


            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ url('expenses/fetch') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'job_order',
                        name: 'job_order'
                    },
                    {
                        data: 'payment_to',
                        name: 'payment_to'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    // { data: 'description', name: 'description' },
                    {
                        data: 'payment_mode',
                        name: 'payment_mode'
                    },
                    // { data: 'payment_details', name: 'payment_details' },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                columnDefs: [{
                    targets: 1,
                    visible: false
                }],
                footerCallback: function(row, date, end, display) {
                    calculateExpenseTotal();
                }
            });


            $(document).on("click", ".expense_export", function() {
                var job_order = $('#job_orders').val();
                var type = $('#type').val();
                var date_range = $('#date_range').val();

                var export_url = "{{ url('expense_export') }}";
                var queryParams = [];

                if (job_order) {
                    queryParams.push("job_order=" + job_order);
                }
                if (type) {
                    queryParams.push("type=" + type);
                }
                if (date_range) {
                    queryParams.push("date_range=" + encodeURIComponent(date_range));
                }

                if (queryParams.length > 0) {
                    export_url += "?" + queryParams.join("&");
                }

                window.location.href = export_url;
            });


            $("#expense-form").validate({
                submitHandler: function(form) {
                    $("#submit-btn").prop("disabled", true);
                    var data = new FormData(form);
                    var url = "{{ url('expenses/store') }}";
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: data,
                        processData: false,
                        contentType: false,
                        success: function() {
                            $("#expense-modal").modal("hide");
                            table.clear().draw();
                            $("#submit-btn").prop("disabled", false);
                        },
                        error: function(code) {
                            alert(code.statusText);
                        },
                    });
                    return false;
                }
            });




            $(document).on("click", ".edit-btn", function() {
                var edit_id = $(this).data('id');
                $("#edit_id").val(edit_id);
                $.ajax({
                    url: "{{ url('expenses/fetch-edit') }}/" + edit_id,
                    dataType: "json",
                    success: function(response) {
                        console.log('response', response);
                        $("#job_order").val(response.job_order).prop('disabled', false);
                        $("#payment_to").val(response.payment_to).prop('disabled', false);
                        $("#date").val(response.date).prop('disabled', false);
                        // $("#type").val(response.type).prop('disabled', false);
                        $("#amount").val(response.amount).prop('disabled', false);
                        $("#description").val(response.description).prop('disabled', false);
                        $("#payment_mode").val(response.payment_mode).prop('disabled', false);
                        $("#payment_details").val(response.payment_details).prop('disabled',
                            false);
                        $("#type option").each(function() {
                            if ($(this).val() === response.type) {
                                $(this).prop("selected", true);
                            }
                        });
                        $("#modal-title-label").html('Edit Expense');
                        $("#expense-modal").modal("show");
                        $("#modal-footer-buttons").show();
                    },
                    error: function(code) {
                        alert(code.statusText);
                    },
                });
            });



            $(document).on("click", ".view-btn", function() {
                var edit_id = $(this).data('id');
                $("#edit_id").val(edit_id);
                $.ajax({
                    url: "{{ url('expenses/fetch-edit') }}/" + edit_id,
                    dataType: "json",
                    success: function(response) {
                        $("#job_order").val(response.job_order).prop('disabled', true);
                        $("#payment_to").val(response.payment_to).prop('disabled', true);
                        $("#date").val(response.date).prop('disabled', true);
                        $("#type").val(response.type).prop('disabled', true);
                        $("#amount").val(response.amount).prop('disabled', true);
                        $("#description").val(response.description).prop('disabled', true);
                        $("#payment_mode").val(response.payment_mode).prop('disabled', true);
                        $("#payment_details").val(response.payment_details).prop('disabled',
                            true);
                        $("#modal-title-label").html('View Expense');
                        $("#expense-modal").modal("show");
                        $("#modal-footer-buttons").hide();
                    },
                    error: function(code) {
                        alert(code.statusText);
                    },
                });
            });


            $(document).on("click", ".add-btn", function() {
                $("#edit_id").val("");
                $("#expense-form")[0].reset();
                $("#job_order").prop('disabled', false);
                $("#payment_to").prop('disabled', false);
                $("#date").prop('disabled', false);
                $("#type").prop('disabled', false);
                $("#amount").prop('disabled', false);
                $("#description").prop('disabled', false);
                $("#payment_mode").prop('disabled', false);
                $("#payment_details").prop('disabled', false);
                $("#modal-title-label").html('Create Expense');
                $("#modal-footer-buttons").show();
            });



            $(document).on("click", ".delete-btn", function() {
                var edit_id = $(this).data('id');
                $("#edit_id").val(edit_id);
                $("#delete-confirm-text").text("Are you sure you want to delete this Expense?");
                $("#delete-confirm-modal").modal("show");
            });

            $(document).on("click", "#confirm-yes-btn", function() {
                var edit_id = $("#edit_id").val();
                $("#confirm-yes-btn").prop("disabled", true);
                $.ajax({
                    url: "{{ url('expenses/delete') }}/" + edit_id,
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


            $('#type').on('change', function() {
                var filterValue = $(this).val();
                table.columns(4).search(filterValue).draw();
                calculateExpenseTotal();
            });

            $('#job_orders').on('change', function() {
                var filterValue = $(this).val();
                table.columns(1).search(filterValue).draw();
                calculateExpenseTotal();
            });
        });
    </script>
@endsection
