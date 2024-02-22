@extends('layouts.master')
@section('title', 'Salaries')
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
                                <h4>Salary Report</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ url('admin/dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        Salary Report
                                    </li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-md-6 d-flex justify-content-end align-items-center">
                            <button class="btn btn-success export-btn mr-2">
                                <i class="bi bi-file-earmark-excel"></i> Export
                            </button>
                            {{-- <button class="btn btn-primary add-btn" data-toggle="modal" data-target="#salary-modal">
                                <i class="bi bi-plus"></i> Create New
                            </button> --}}
                        </div>
                    </div>
                </div>
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-4">
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
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="date_range">Date Range:</label>
                                <input type="text" class="form-control" name="date_range" id="date_range">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="total_amount">Total Amount:</label>
                                <input type="text" class="form-control" id="total_amount" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pd-20 bg-white border-radius-4 box-shadow">
                    <table class="table table-bordered" id="salary_table">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th class="job-order-column">Job Order</th>
                                <th>Labour</th>
                                <th>Date</th>
                                <th>Payment Mode</th>
                                <th>Payment Details</th>
                                <th style="text-align: right;">Amount</th>
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>
                        <tbody id="salary_table_body">

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
            fetchSalaries();

            function fetchSalaries() {
                $.ajax({
                    url: "{{ route('salaries.fetch') }}",
                    success: function(response) {
                        renderSalaries(response);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }


            function renderSalaries(data) {
                $('#salary_table_body').empty();
                console.log('Salaries:', data.salaries);
                console.log('Labours:', data.labours);

                var totalAmount = 0;

                $.each(data.salaries, function(index, salary) {
                    var labourName = salary.labour;
                    console.log('Labour Name:', labourName);

                    var formattedDate = salary.date.split('-').reverse().join('-');
                    var row = '<tr>' +
                        '<td>' + (index + 1) + '</td>' +
                        '<td class="job-order-column">' + salary.job_order + '</td>' +
                        '<td>' + (labourName ? labourName : 'Undefined') + '</td>' +
                        '<td>' + formattedDate + '</td>' +
                        '<td>' + salary.payment_mode + '</td>' +
                        '<td>' + salary.payment_details + '</td>' +
                        '<td style="text-align: right;">₹' + parseFloat(salary.amount).toLocaleString(
                            'en-IN', {
                                maximumFractionDigits: 2,
                                minimumFractionDigits: 2
                            }) + '</td>' +
                        '</tr>';

                    totalAmount += parseFloat(salary.amount);

                    $('#salary_table_body').append(row);
                });

                var totalRow = '<tr>' +
                    '<td colspan="5" style="text-align: right;"><b>Total:</b></td>' +
                    '<td style="text-align: right;"><b>₹' + totalAmount.toLocaleString(
                            'en-IN', {
                                maximumFractionDigits: 2,
                                minimumFractionDigits: 2
                            }) + '</b></td>' +
                    '</tr>';
                $('#salary_table_body').append(totalRow);
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
                var total = 0;

                $('#salary_table_body tr').each(function() {
                    var jobOrder = $(this).find('td:eq(1)').text();
                    var date = $(this).find('td:eq(3)').text();
                    var amountText = $(this).find('td:eq(6)').text().trim();
                    var amountValue = amountText.replace(/[^\d.-]/g, '');
                    var amount = parseFloat(amountValue);
                    console.log("Job Order:", jobOrder);
                    console.log("Date:", date);
                    console.log("Amount Text:", amountText);
                    console.log("Parsed Amount:", amount);
                    if ((filteredJobOrder === '' || jobOrder === filteredJobOrder) &&
                        (filteredDateRange === '' || isDateInRange(date, filteredDateRange))) {
                        total += isNaN(amount) ? 0 : amount;
                    }
                });

                var formattedTotal = '₹' + total.toLocaleString('en-IN', {
                    maximumFractionDigits: 2,
                    minimumFractionDigits: 2
                }) + ' /-';
                console.log("Total:", total);
                $('#total_amount').val(formattedTotal);
            }



            $('#job_orders').on('change', function() {
                calculateTotalAmount();
                applyTableFilters();
            });

            function isDateInRange(date, dateRange) {
                var startDate = moment(dateRange.split(' - ')[0], 'DD-MM-YYYY');
                var endDate = moment(dateRange.split(' - ')[1], 'DD-MM-YYYY');
                var currentDate = moment(date, 'DD-MM-YYYY');
                return currentDate.isBetween(startDate, endDate, null, '[]');
            }

            function applyTableFilters() {
                var filteredJobOrder = $('#job_orders').val();
                var filteredDateRange = $('#date_range').val();
                console.log('Filtered Job Order:', filteredJobOrder);
                console.log('Filtered Date Range:', filteredDateRange);

                $('#salary_table_body tr').each(function() {
                    var jobOrder = $(this).find('td:eq(1)').text();
                    var date = $(this).find('td:eq(3)').text();
                    console.log('Job Order in Row:', jobOrder);
                    console.log('Date in Row:', date);

                    var jobOrderMatches = (filteredJobOrder !== '' && jobOrder !== filteredJobOrder);
                    var dateInRange = isDateInRange(date, filteredDateRange);

                    if (jobOrderMatches || !dateInRange) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });
            }

            $(document).on("click", ".export-btn", function() {
                var job_order = $('#job_orders').val();
                var date_range = $('#date_range').val();
                var export_url = "{{ url('salaries_report/report') }}";
                if (date_range) {
                    export_url += "?date_range=" + date_range;
                }
                if (job_order) {
                    export_url += (date_range ? "&" : "?") + "job_order=" + job_order;
                }
                window.location.href = export_url;
            });
        });
    </script>
@endsection
