@extends('layouts.master')
@section('title', 'Purchase')

@section('content')

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-6">
                            <div class="title">
                                <h4>Purchase Report</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ url('admin/dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        Purchase Report
                                    </li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-6 text-right">
                            <button class="btn btn-success purchase_export mr-2">
                                <i class="bi bi-file-earmark-excel"></i> Export
                            </button>
                        </div>
                    </div>
                </div>
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="job_orders">Filter by Job Order:</label>
                                <select class="form-control" name="job_orders" id="job_orders" required>
                                    <option value="" selected>All</option>
                                    @foreach ($tenders as $id => $tenderName)
                                        <option value="{{ $id }}">{{ $tenderName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="job_orders">Filter by Purchase Type:</label>
                                <select class="form-control" name="purchase_type" id="purchase_type" required>
                                    <option value="" selected>All</option>
                                    @foreach ($tenders as $id => $tenderName)
                                        <option value="{{ $id }}">{{ $tenderName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="date_range">Date Range:</label>
                                <input type="text" class="form-control" name="date_range" id="date_range">
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="total_amount">Grand Total Amount:</label>
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
                                <th>Type</th>
                                <th>Date</th>
                                {{-- <th>Invoice No</th> --}}
                                <th>Vendor</th>
                                <th>Product/Material</th>
                                <th>Quantity</th>
                                <th>Amount</th>
                                {{-- <th>GST</th> --}}
                                <th>Total</th>
                                <th width="100px">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="purchase-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title-label">
                        Create Labour
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        ×
                    </button>
                </div>
                <form id="labour-form">
                    @csrf
                    <input type="hidden" name="edit_id" id="edit_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Job Order</label>
                            <input type="text" class="form-control" name="job_order" id="job_order" required>
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <input type="text" class="form-control" name="type" id="type" required>
                        </div>
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" class="form-control" name="date" id="date" required>
                        </div>
                        <div class="form-group">
                            <label>Invoice No</label>
                            <input type="text" class="form-control" name="invoice_no" id="invoice_no" required>
                        </div>
                        <div class="form-group">
                            <label>Vendor</label>
                            <input type="text" class="form-control" name="vendor" id="vendor" required>
                        </div>
                        <div class="form-group">
                            <label>Product/Material</label>
                            <input type="text" class="form-control" name="material" id="material" required>
                        </div>
                        <div class="form-group">
                            <label>Quantity</label>
                            <input type="text" class="form-control" name="quantity" id="quantity" required>
                        </div>
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="text" class="form-control" name="amount" id="amount" required>
                        </div>
                        <div class="form-group">
                            <label>GST</label>
                            <input type="text" class="form-control" name="gst" id="gst" required>
                        </div>
                        <div class="form-group">
                            <label>Total</label>
                            <input type="text" class="form-control" name="total" id="total" required>
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
            flatpickr("#datepicker", {
                dateFormat: "Y-m-d"
            });


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
                    dateRangeString += currentDate.format('YYYY-MM-DD') + '|';
                    currentDate.add(1, 'day');
                }

                dateRangeString = dateRangeString.slice(0, -1);
                $(this).val(startDate.format('YYYY-MM-DD') + ' - ' + endDate.format('YYYY-MM-DD'));
                $(this).trigger('change');
                table.column(3).search(dateRangeString, true).draw();
            });


            $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                table.column(3).search('').draw();
                calculateTotalAmount();
            });

            function calculateTotalAmount() {
                $.ajax({
                    url: "{{ url('purchase/fetch') }}",
                    success: function(response) {
                        var total = 0;
                        var filteredJobOrder = $('#job_orders').val();
                        console.log('filteredJobOrder', filteredJobOrder);
                        var filteredDateRange = $('#date_range').val();

                        console.log('filteredDateRange', filteredDateRange);
                        console.log('response', response);
                        $.each(response.data, function(index, row) {
                            if (!row.deleted_at) {
                                var rowDate = new Date(row.date);
                                var startDate = new Date(filteredDateRange.split(' - ')[0]);
                                var endDate = new Date(filteredDateRange.split(' - ')[1]);

                                console.log('Row job ID', row.job_order_id, 'F_ID',
                                    filteredJobOrder, 'S_D', startDate, 'E_D', endDate);
                                if ((filteredJobOrder === '' || row.job_order_id ==
                                        filteredJobOrder) &&
                                    (filteredDateRange === '' || (rowDate >= startDate &&
                                        rowDate <= endDate))) {
                                    console.log("work fine");
                                    console.log('amount', row.total.replace(/[^\d.]/g, ''));
                                    var amount = parseFloat(row.total.replace(/[^\d.]/g, ''));
                                    console.log(amount);
                                    total += isNaN(amount) ? 0 : amount;
                                }
                            }
                        });

                        var formattedTotal = '₹ ' + total.toLocaleString('en-IN', {
                            maximumFractionDigits: 2,
                            minimumFractionDigits: 2
                        }) + ' /-';
                        $('#total_amount').val(formattedTotal);
                        console.log('formattedTotal', formattedTotal);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }


            table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ url('purchase/fetch') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'job_order_id',
                        name: 'job_order_id'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    // {
                    //     data: 'invoice_no',
                    //     name: 'invoice_no'
                    // },
                    {
                        data: 'vendor',
                        name: 'vendor',
                        render: function(data, type, full, meta) {
                            console.log('data', data);
                            return data;
                        }
                    },
                    {
                        data: 'material',
                        name: 'material',
                        render: function(data, type, full, meta) {
                            return data;
                        }
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'amount',
                        name: 'amount'
                    },
                    // {
                    //     data: 'gst',
                    //     name: 'gst'
                    // },
                    {
                        data: 'total',
                        name: 'total'
                    },
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
                    calculateTotalAmount();
                }
            });

            $('#job_orders').on('change', function() {
                var filterValue = $(this).val();
                table.columns(1).search(filterValue).draw();
                calculateTotalAmount();
            });




            $(document).on("click", ".purchase_export", function() {
                var job_order = $('#job_orders').val();
                var date_range = $('#date_range').val();
                var export_url = "{{ url('purchase_export') }}";
                if (date_range) {
                    export_url += "?date_range=" + date_range;
                }
                if (job_order) {
                    export_url += (date_range ? "&" : "?") + "job_order=" + job_order;
                }
                window.location.href = export_url;
            });

            $(document).on("click", ".delete-btn", function() {
                var edit_id = $(this).data('id');
                $("#edit_id").val(edit_id);
                $("#delete-confirm-text").text("Are you sure you want to delete this Labour?");
                $("#delete-confirm-modal").modal("show");
            });

            $(document).on("click", "#confirm-yes-btn", function() {
                var edit_id = $("#edit_id").val();
                $("#confirm-yes-btn").prop("disabled", true);

                $.ajax({
                    url: "{{ url('purchase/delete') }}/" + edit_id,
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
        });
    </script>
@endsection
