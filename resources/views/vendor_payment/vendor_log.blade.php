@extends('layouts.master')
@section('title', 'Balance')

@section('content')
    <style>
        .hidden {
            display: none;
        }

        .hidden_vendor {
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
                                <h4>Vendor Balance Log</h4>
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
                        <div class="col-md-6 d-flex justify-content-end align-items-center">
                            <button class="btn btn-success export-btn mr-2">
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
                        <div class="form-group">
                            <label for="vendors">Filter by Vendor:</label>
                            <select class="form-control" name="vendors" id="vendors" required style="width: 250px;">
                                {{-- <option value="" selected>All</option> --}}
                                @foreach ($vendor as $id => $vendorName)
                                    <option value="{{ $id }}">{{ $vendorName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
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
                                <th class="hidden_vendor">Vendor</th>
                                <th>Date</th>
                                <th>Description</th>
                                <th>Credit</th>
                                <th>Debit</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $counter = 1; ?>
                            <?php usort($allLogs, function ($a, $b) {
                                return strtotime($a['date']) - strtotime($b['date']);
                            }); ?>
                            {{-- {{dd($allLogs);}} --}}
                            @foreach ($allLogs as $log)
                                <tr>
                                    <td>{{ $counter++ }}</td>
                                    <td class="hidden">
                                        @if (isset($log['tender']))
                                            {{ $log['tender']['id'] }}
                                        @elseif(isset($log['job_order']))
                                            {{ $log['job_order'] }}
                                        @elseif(isset($log['job_order_id']))
                                            {{ $log['job_order_id'] }}
                                        @else
                                            ''
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($log['date'])->format('d-m-Y') }}</td>
                                    <td class="hidden_vendor">
                                        @if (isset($log['vendor_id']))
                                            {{ $log['vendor_id'] }}
                                        @endif
                                        @if (isset($log['vendor_balance_id']))
                                            {{ $log['vendor_balance_id'] }}
                                        @endif
                                    </td>
                                    <td>
                                        @if (isset($log['description']))
                                            {{ $log['description'] }}
                                        @elseif(isset($log['purchase_type']['name']))
                                            {{ $log['purchase_type']['name'] }}
                                        @else
                                            ''
                                        @endif
                                    </td>
                                    <td style="text-align: right;">
                                        @if (isset($log['final_total']))
                                            {{ '₹' . number_format($log['final_total'], 2) }}
                                        @elseif(!isset($log['type']) || ($log['type'] !== 'Debit' && isset($log['amount'])))
                                            {{ '₹' . number_format($log['amount'], 2) }}
                                        @endif
                                    </td>
                                    <td style="text-align: right;">
                                        @if (isset($log['type']) && $log['type'] === 'Debit' && isset($log['amount']))
                                            {{ '₹' . number_format($log['amount'], 2) }}
                                        @endif
                                    </td>
                                    <td style="text-align: right;">
                                        @if (isset($log['vendor_id']) && isset($vendorBalances[$log['vendor_id']]))
                                            {{ '₹' . number_format($vendorBalances[$log['vendor_id']]['balance'], 2) }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align: right;">Total:</td>
                                <td style="text-align: right;" id="creditTotal">0.00</td>
                                <td style="text-align: right;" id="debitTotal">0.00</td>
                                <td style="text-align: right;" id="balanceTotal">0.00</td>
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
            calculateVendorBalances()
            calculateTotals();

            function filterCurrentMonth() {
                var startDate = moment().startOf('month').format('YYYY-MM-DD');
                var endDate = moment().endOf('month').format('YYYY-MM-DD');
                var dateRangeString = '';

                var currentDate = moment(startDate);
                while (currentDate.isSameOrBefore(endDate)) {
                    dateRangeString += currentDate.format('YYYY-MM-DD') + '|';
                    currentDate.add(1, 'day');
                }
                dateRangeString = dateRangeString.slice(0, -1);

                filterDateRange(dateRangeString);
                $('#date_range').val(moment(startDate).format('DD-MM-YYYY') + ' - ' + moment(endDate).format(
                    'DD-MM-YYYY'));
            }

            filterCurrentMonth();

            $('#date_range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear',
                    format: 'DD-MM-YYYY'
                },
                startDate: moment().startOf('month').format('DD-MM-YYYY'),
                endDate: moment().endOf('month').format('DD-MM-YYYY')
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
                filterCurrentMonth();
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


            // function calculateVendorBalances() {
            //     var vendorBalances = {};

            //     $('#balance_log_table tbody tr:visible').each(function() {
            //         var vendorId = $(this).find('td.hidden_vendor').text().trim();
            //         var creditCell = parseFloat($(this).find('td:nth-child(6)').text().replace('₹', '')
            //             .replace(',', '')) || 0;
            //         var debitCell = parseFloat($(this).find('td:nth-child(7)').text().replace('₹', '')
            //             .replace(',', '')) || 0;
            //         var balanceCell = $(this).find('td:nth-child(8)');

            //         if (!vendorBalances[vendorId]) {
            //             vendorBalances[vendorId] = 0;
            //         }

            //         vendorBalances[vendorId] += (creditCell - debitCell);

            //         balanceCell.text('₹' + vendorBalances[vendorId].toFixed(2));
            //     });
            // }


            function calculateVendorBalances() {
                var vendorBalances = {};

                $('#balance_log_table tbody tr:visible').each(function() {
                    var vendorId = $(this).find('td.hidden_vendor').text().trim();
                    var creditCell = parseFloat($(this).find('td:nth-child(6)').text().replace('₹', '')
                        .replace(',', '')) || 0;
                    var debitCell = parseFloat($(this).find('td:nth-child(7)').text().replace('₹', '')
                        .replace(',', '')) || 0;
                    var balanceCell = $(this).find('td:nth-child(8)');

                    if (!vendorBalances[vendorId]) {
                        vendorBalances[vendorId] = 0;
                    }
                    vendorBalances[vendorId] += (creditCell - debitCell);
                    var balanceAmount = vendorBalances[vendorId];
                    var symbol = '';
                    if (balanceAmount < 0) {
                        balanceAmount = -balanceAmount;
                        symbol = "-";
                    }

                    balanceCell.text(symbol + '₹' + balanceAmount.toFixed(2));
                });
            }


            function calculateTotals() {
                var creditTotal = 0;
                var debitTotal = 0;
                var balanceTotal = 0;

                $('#balance_log_table tbody tr:visible').each(function() {
                    var creditCell = parseFloat($(this).find('td:nth-child(6)').text().replace('₹', '')
                        .replace(',', '')) || 0;
                    var debitCell = parseFloat($(this).find('td:nth-child(7)').text().replace('₹', '')
                        .replace(',', '')) || 0;

                    creditTotal += creditCell;
                    debitTotal += debitCell;
                });

                var balanceTotal = creditTotal - debitTotal;
                var symbol = '';
                if (balanceTotal < 0) {
                    balanceTotal = -balanceTotal;
                    symbol = "-";
                }

                $('#creditTotal').text('₹' + creditTotal.toFixed(2));
                $('#debitTotal').text('₹' + debitTotal.toFixed(2));
                $('#balanceTotal').text(symbol + '₹' + balanceTotal.toFixed(2));
            }



            function formatDate(date) {
                return moment(date, 'DD-MM-YYYY').format('YYYY-MM-DD');
            }

            function isDateInRange(date, dateRangeString) {
                return dateRangeString.includes(date);
            }

            function filterJobOrder(jobOrderId) {
                $('#balance_log_table tbody tr').each(function() {
                    var jobOrderCell = $(this).find('td.hidden').text().trim();
                    if (jobOrderId === '' || jobOrderCell === jobOrderId) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
                calculateVendorBalances()
                calculateTotals();
            }

            // function filterVendor(vendorId, dateRangeString) {
            //     $('#balance_log_table tbody tr').each(function() {
            //         var vendorCell = $(this).find('td.hidden_vendor').text().trim();
            //         var dateCell = $(this).find('td:nth-child(3)').text();
            //         var formattedDateCell = formatDate(dateCell);

            //         if ((vendorId === '' || vendorCell == vendorId) &&
            //             (dateRangeString === '' || isDateInRange(formattedDateCell, dateRangeString))) {
            //             $(this).show();
            //         } else {
            //             $(this).hide();
            //         }
            //     });
            //     calculateVendorBalances();
            //     calculateTotals();
            // }

            // function filterVendor(vendorId, dateRangeString) {
            //     $('#balance_log_table tbody tr').each(function() {
            //         var vendorCell = $(this).find('td.hidden_vendor').text().trim();
            //         var dateCell = $(this).find('td:nth-child(3)').text();
            //         var formattedDateCell = formatDate(dateCell);

            //         if ((vendorId === '' || vendorCell == vendorId) &&
            //             (dateRangeString === '' || isDateInRange(formattedDateCell, dateRangeString))) {
            //             $(this).show();
            //         } else {
            //             $(this).hide();
            //         }
            //     });

            //     // Calculate and display opening balance for the selected vendor
            //     var openingBalanceRow = $('#balance_log_table tbody tr.opening-balance');
            //     var openingBalance = calculateOpeningBalance(vendorId);
            //     if (openingBalanceRow.length) {
            //         openingBalanceRow.find('td:nth-child(8)').text('₹' + openingBalance.toFixed(2));
            //     } else {
            //         $('#balance_log_table tbody').prepend('<tr class="opening-balance">' +
            //             '<td colspan="5" style="text-align: right;">Opening Balance:</td>' +
            //             '<td style="text-align: right;">₹' + openingBalance.toFixed(2) + '</td>' +
            //             '</tr>');
            //     }

            //     calculateVendorBalances();
            //     calculateTotals();
            // }


            function filterVendor(vendorId, dateRangeString) {
                $('#balance_log_table tbody tr').each(function() {
                    var vendorCell = $(this).find('td.hidden_vendor').text().trim();
                    var dateCell = $(this).find('td:nth-child(3)').text();
                    var formattedDateCell = formatDate(dateCell);

                    if ((vendorId === '' || vendorCell == vendorId) &&
                        (dateRangeString === '' || isDateInRange(formattedDateCell, dateRangeString))) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });

                $('#balance_log_table tbody tr.opening-balance').remove();
                var openingBalanceRows = calculateOpeningBalance(vendorId);
                if (openingBalanceRows.length > 0) {
                    openingBalanceRows.forEach(function(openingBalanceInfo) {
                        $('#balance_log_table tbody').prepend('<tr class="opening-balance">' +
                            '<td colspan="1">1</td>' +
                            '<td>' + openingBalanceInfo.date + '</td>' +
                            '<td colspan="1" style="text-align: right; font-weight: bold;font-size: 15px;">Opening Balance:</td>' +
                            '<td style="text-align: right;">₹' + openingBalanceInfo.credit.toFixed(2) +
                            '</td>' +
                            '<td style="text-align: right;">₹' + openingBalanceInfo.debit.toFixed(2) +
                            '</td>' +
                            '<td style="text-align: right;">₹' + openingBalanceInfo.balance.toFixed(2) +
                            '</td>' +
                            '</tr>');
                    });
                }

                // Recalculate totals
                // calculateVendorBalances();
                calculateTotals();
            }

            function calculateOpeningBalance(vendorId) {
                var openingBalances = [];
                var currentDate = moment().startOf('month');
                var previousMonthDate = currentDate.subtract(1, 'months').format('YYYY-MM');

                $('#balance_log_table tbody tr').each(function() {
                    var vendorCell = $(this).find('td.hidden_vendor').text().trim();
                    var dateCell = $(this).find('td:nth-child(3)').text();
                    var logDate = moment(dateCell, 'DD-MM-YYYY');

                    if (vendorCell == vendorId && logDate.format('YYYY-MM') === previousMonthDate) {
                        var creditCell = parseFloat($(this).find('td:nth-child(6)').text().replace('₹', '')
                            .replace(',', '')) || 0;
                        var debitCell = parseFloat($(this).find('td:nth-child(7)').text().replace('₹', '')
                            .replace(',', '')) || 0;
                        var openingBalance = creditCell - debitCell;

                        openingBalances.push({
                            date: logDate.format('DD-MM-YYYY'),
                            credit: creditCell,
                            debit: debitCell,
                            balance: openingBalance
                        });
                    }
                });

                return openingBalances;
            }



            var selectedVendorId = $('#vendors').val();
            var startDate = moment().startOf('month').format('YYYY-MM-DD');
            var endDate = moment().endOf('month').format('YYYY-MM-DD');
            var dateRangeString = generateDateRangeString(startDate, endDate);
            filterVendor(selectedVendorId, dateRangeString);

            $('#vendors').on('change', function() {
                var selectedVendorId = $(this).val();
                filterVendor(selectedVendorId, dateRangeString);
            });

            function generateDateRangeString(startDate, endDate) {
                var dateRangeString = '';
                var currentDate = moment(startDate);
                while (currentDate.isSameOrBefore(endDate)) {
                    dateRangeString += currentDate.format('YYYY-MM-DD') + '|';
                    currentDate.add(1, 'day');
                }
                return dateRangeString.slice(0, -1);
            }

            $('#job_orders').on('change', function() {
                debugger
                var selectedJobOrderId = $(this).val();
                filterJobOrder(selectedJobOrderId);
            });

            $(document).on("click", ".export-btn", function() {
                var job_order = $('#job_orders').val();
                var vendor = $('#vendors').val();
                var date_range = $('#date_range').val();
                var export_url = "{{ url('vendor_log/report') }}";
                if (date_range) {
                    export_url += "?date_range=" + date_range;
                }
                if (job_order) {
                    export_url += (date_range || vendor ? "&" : "?") + "job_order=" + job_order;
                }
                if (vendor) {
                    export_url += (date_range || job_order ? "&" : "?") + "vendor=" + vendor;
                }
                window.location.href = export_url;
            });

        });
    </script>
@endsection
