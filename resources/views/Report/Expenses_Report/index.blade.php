@extends('layouts.master')
@section('title', 'Expenses')
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
                                <h4>Expenses Report</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ url('admin/dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        Expenses Report
                                    </li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-md-6 d-flex justify-content-end align-items-center">
                            <button class="btn btn-success expense_exports mr-2">
                                <i class="bi bi-file-earmark-excel"></i> Export
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
                    <table class="table">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th class="job-order-column">Job Order</th>
                                <th>Payment To</th>
                                <th>Date</th>
                                <th>Type</th>
                                {{-- <th>Description</th> --}}
                                <th>Payment Mode</th>
                                <th>Payment Details</th>
                                <th style="text-align: right;">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="expenses_table">

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('addscript')
    <script type="text/javascript">
        $(document).ready(function() {
            fetchExpenses();

            function fetchExpenses() {
                $.ajax({
                    url: "{{ route('expenses.fetch') }}",
                    success: function(response) {
                        renderExpenses(response);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }

            function renderExpenses(data) {
                var totalAmount = 0;
                $('#expenses_table').empty();

                $.each(data.expense, function(index, expense) {
                    var formattedDate = expense.date.split('-').reverse().join('-');
                    var row = '<tr>' +
                        '<td>' + (index + 1) + '</td>' +
                        '<td class="job-order-column">' + expense.job_order + '</td>' +
                        '<td>' + expense.payment_to + '</td>' +
                        '<td>' + formattedDate + '</td>' +
                        '<td>' + expense.type + '</td>' +
                        '<td>' + expense.payment_mode + '</td>' +
                        '<td>' + expense.payment_details + '</td>' +
                        '<td style="text-align: right;">₹' + parseFloat(expense.amount).toLocaleString(
                            'en-IN', {
                                maximumFractionDigits: 2,
                                minimumFractionDigits: 2
                            }) + '</td>' +
                        '</tr>';

                    totalAmount += parseFloat(expense.amount);
                    $('#expenses_table').append(row);
                });

                var totalRow = '<tr>' +
                    '<td colspan="6" style="text-align: right;"><b>Total:</b></td>' +
                    '<td style="text-align: right;"><b>₹' + totalAmount.toLocaleString(
                        'en-IN', {
                            maximumFractionDigits: 2,
                            minimumFractionDigits: 2
                        }) + '</b></td>' +
                    '</tr>';
                $('#expenses_table').append(totalRow);
                calculateTotalAmount();
            }

            $('#date_range').daterangepicker({
                autoUpdateInput: true,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'DD-MM-YYYY'
                },
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month')
            });

            $('#date_range').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD-MM-YYYY') + ' - ' + picker.endDate.format(
                    'DD-MM-YYYY'));
                calculateTotalAmount();
                applyTableFilters();
            });

            function calculateTotalAmount() {
                var filteredJobOrder = $('#job_orders').val();
                var filteredDateRange = $('#date_range').val();
                var filteredType = $('#type').val();
                var total = 0;

                $('#expenses_table tr').each(function() {
                    var jobOrder = $(this).find('td:eq(1)').text();
                    var date = $(this).find('td:eq(3)').text();
                    var type = $(this).find('td:eq(4)').text();
                    var amountText = $(this).find('td:eq(7)').text().trim();
                    var amountValue = amountText.replace(/[^\d.-]/g, '');
                    var amount = parseFloat(amountValue);

                    console.log("filteredJobOrder:", filteredJobOrder);
                    console.log("filteredDateRange:", filteredDateRange);
                    console.log("filteredType:", filteredType);
                    console.log("Job Order:", jobOrder);
                    console.log("Date:", date);
                    console.log("Type:", type);
                    console.log("Amount Text:", amountText);
                    console.log("Parsed Amount:", amount);

                    if ((filteredJobOrder === '' || jobOrder === filteredJobOrder) &&
                        (filteredDateRange === '' || isDateInRange(date, filteredDateRange)) &&
                        (filteredType === '' || type === filteredType)) {
                        total += isNaN(amount) ? 0 : amount;
                    }
                });

                var formattedTotal = '₹ ' + total.toLocaleString('en-IN', {
                    maximumFractionDigits: 2,
                    minimumFractionDigits: 2
                }) + ' /-';
                console.log("Total:", total);
                $('#total_amount').val(formattedTotal);
            }




            function isDateInRange(date, dateRange) {
                var startDate = moment(dateRange.split(' - ')[0], 'DD-MM-YYYY');
                var endDate = moment(dateRange.split(' - ')[1], 'DD-MM-YYYY');
                var currentDate = moment(date, 'DD-MM-YYYY');
                return currentDate.isBetween(startDate, endDate, null, '[]');
            }


            $('#type').on('change', function() {
                calculateTotalAmount();
                applyTableFilters();
            });

            $('#job_orders').on('change', function() {
                calculateTotalAmount();
                applyTableFilters();
            });


            function applyTableFilters() {
                var filteredJobOrder = $('#job_orders').val();
                var filteredDateRange = $('#date_range').val();
                var filteredType = $('#type').val();

                $('#expenses_table tr').each(function() {
                    var jobOrder = $(this).find('td:eq(1)').text();
                    var date = $(this).find('td:eq(3)').text();
                    var type = $(this).find('td:eq(4)').text();

                    if ((filteredJobOrder && jobOrder !== filteredJobOrder) ||
                        (filteredType && type !== filteredType)) {
                        $(this).hide();
                    } else if (filteredDateRange) {
                        var startDate = moment(filteredDateRange.split(' - ')[0], 'DD-MM-YYYY');
                        var endDate = moment(filteredDateRange.split(' - ')[1], 'DD-MM-YYYY');
                        var currentDate = moment(date, 'DD-MM-YYYY');

                        if (!currentDate.isBetween(startDate, endDate, null, '[]')) {
                            $(this).hide();
                        } else {
                            $(this).show();
                        }
                    } else {
                        $(this).show();
                    }
                });
            }


            $(document).on("click", ".expense_exports", function() {
                var job_order = $('#job_orders').val();
                var type = $('#type').val();
                var date_range = $('#date_range').val();

                var export_url = "{{ url('expense_export/report') }}";
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
        });
    </script>
@endsection
