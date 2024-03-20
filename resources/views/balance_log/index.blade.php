@extends('layouts.master')
@section('title', 'Payment')

@section('content')
    <style>
        .hidden {
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
                                <h4>Balance Log Management</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ url('admin/dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        View Balance Log
                                    </li>
                                </ol>
                            </nav>
                        </div>
                        {{-- <div class="col-md-6 d-flex justify-content-end align-items-center">
                            <button class="btn btn-success export-btn mr-2">
                                <i class="bi bi-file-earmark-excel"></i> Export
                            </button>
                        </div> --}}
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
                    </div>
                </div>
                <div class="pd-20 bg-white border-radius-4 box-shadow">
                    <table class="table table-bordered" id="balance_log_table">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th class="hidden">Job Order</th>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Credit</th>
                                <th>Debit</th>
                                {{-- <th>Balance</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            <?php $counter = 1; ?>
                            @foreach ($tenderLogs as $log)
                                <tr>
                                    <td>{{ $counter++ }}</td>
                                    <td class="hidden">{{ $log->tender->job_order ?? '' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($log->date)->format('d-m-Y') }}</td>
                                    <td>{{ $log->description }}</td>
                                    <td style="text-align: right;">{{ $log->type === 'Credit' ? '₹' . number_format($log->amount, 2) : '' }}</td>
                                    <td style="text-align: right;">{{ $log->type === 'Debit' ? '₹' . number_format($log->amount, 2) : '' }}</td>
                                    {{-- <td></td> --}}
                                </tr>
                            @endforeach
                            @foreach ($salaries as $salary)
                                <tr>
                                    <td>{{ $counter++ }}</td>
                                    <td class="hidden">{{ $salary->job_order }}</td>
                                    <td>{{ \Carbon\Carbon::parse($salary->date)->format('d-m-Y') }}</td>
                                    <td>{{ $salary->description }}</td>
                                    <td></td>
                                    <td style="text-align: right;">₹{{ number_format($salary->amount, 2) }}</td>
                                    {{-- <td style="text-align: right;"></td> --}}
                                </tr>
                            @endforeach
                            @foreach ($expenses as $expense)
                                <tr>
                                    <td>{{ $counter++ }}</td>
                                    <td class="hidden">{{ $expense->job_order }}</td>
                                    <td>{{ \Carbon\Carbon::parse($expense->date)->format('d-m-Y') }}</td>
                                    <td>{{ $expense->description }}</td>
                                    <td></td>
                                    <td style="text-align: right;">₹{{ number_format($expense->amount, 2) }}</td>
                                    {{-- <td style="text-align: right;"></td> --}}
                                </tr>
                            @endforeach
                            @foreach ($purchases as $purchase)
                                <tr>
                                    <td>{{ $counter++ }}</td>
                                    <td class="hidden">{{ $purchase->job_order_id }}</td>
                                    <td>{{ \Carbon\Carbon::parse($purchase->date)->format('d-m-Y') }}</td>
                                    <td>{{ $purchase->purchaseType->name }}</td>
                                    <td></td>
                                    <td style="text-align: right;">₹{{ number_format($purchase->final_total, 2) }}</td>
                                    {{-- <td style="text-align: right;"></td> --}}
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align: right;">Total:</td>
                                <td style="text-align: right;" id="creditTotal">0.00</td>
                                <td style="text-align: right;" id="debitTotal">0.00</td>
                                {{-- <td style="text-align: right;" id="balanceTotal">0.00</td> --}}
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('addscript')
<script type="text/javascript">
    $(document).ready(function() {
        calculateTotals();
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
            $(this).val(startDate.format('DD-MM-YYYY') + ' - ' + endDate.format('DD-MM-YYYY'));
            $(this).trigger('change');

            filterDateRange(dateRangeString);
        });

        $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
            filterDateRange('');
        });

        function filterDateRange(dateRangeString) {
            $('#balance_log_table tbody tr').each(function() {
                var dateCell = $(this).find('td:nth-child(3)').text();
                var formattedDateCell = formatDate(dateCell);
                if (dateRangeString === '' || isDateInRange(formattedDateCell, dateRangeString)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            calculateTotals();
        }

        function calculateTotals() {
                var creditTotal = 0;
                var debitTotal = 0;
                var balanceTotal = 0;
                $('#balance_log_table tbody tr:visible').each(function() {
                    var creditCell = $(this).find('td:nth-child(5)').text();
                    var debitCell = $(this).find('td:nth-child(6)').text();
                    var balanceCell = $(this).find('td:nth-child(7)');
                    var creditAmount = parseFloat(creditCell.replace('₹', '').replace(',', '')) || 0;
                    var debitAmount = parseFloat(debitCell.replace('₹', '').replace(',', '')) || 0;
                    creditTotal += creditAmount;
                    if (debitAmount !== 0) {
                        debitTotal += debitAmount;
                    }
                    var balance = creditAmount - debitAmount;
                    if (balance < 0) {
                        balance = 0;
                    }
                    balanceTotal += balance;
                    balanceCell.text('₹' + balance.toFixed(2));
                });

        $('#creditTotal').text('₹' + creditTotal.toFixed(2));
        $('#debitTotal').text('₹' + debitTotal.toFixed(2));
        $('#balanceTotal').text('₹' + balanceTotal.toFixed(2));
    }



        function formatDate(date) {
            return moment(date, 'DD-MM-YYYY').format('YYYY-MM-DD');
        }

        function isDateInRange(date, dateRangeString) {
            return dateRangeString.includes(date);
        }

        function filterJobOrder(jobOrderId) {
            $('#balance_log_table tbody tr').each(function() {
                var jobOrderCell = $(this).find('td.hidden');
                if (jobOrderId === '' || jobOrderCell.text() === jobOrderId) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            calculateTotals();
        }

        $('#job_orders').on('change', function() {
            var selectedJobOrderId = $(this).val();
            filterJobOrder(selectedJobOrderId);
        });

        $(document).on("click", ".export-btn", function() {
                var job_order = $('#job_orders').val();
                var date_range = $('#date_range').val();
                var export_url = "{{ url('balance_log/report') }}";
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
