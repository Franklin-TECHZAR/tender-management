@extends('layouts.master')
@section('title', 'Purchase')

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
                                <label for="purchase_type">Filter by Purchase Type:</label>
                                <select class="form-control" name="purchase_type" id="purchase_type" required>
                                    <option value="" selected>All</option>
                                    @foreach ($purchaseTypes as $id => $typeName)
                                        <option value="{{ $id }}">{{ $typeName }}</option>
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
                                <th class="job-order-column">Job Order</th>
                                <th class="purchaseType">Type</th>
                                <th class="formattedDate">Date</th>
                                {{-- <th>Invoice No</th> --}}
                                <th>Vendor</th>
                                <th>Product/Material</th>
                                <th>Quantity</th>
                                <th style="text-align: right;">Amount</th>
                                {{-- <th>GST</th> --}}
                                {{-- <th>Total</th> --}}
                                {{-- <th width="100px">Action</th> --}}
                            </tr>
                        </thead>
                        <tbody id="purchase_table_body">

                        </tbody>
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
            fetchPurchases();

            function fetchPurchases() {
                $.ajax({
                    url: "{{ route('purchases.fetch') }}",
                    success: function(response) {
                        renderPurchases(response);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }

            function renderPurchases(data) {
                $('#purchase_table_body').empty();
                console.log('Purchases:', data.purchases);
                console.log('Vendors:', data.vendors);

                var totalAmount = 0;

                $.each(data.purchases, function(index, purchase) {
                    console.log('purchase:', purchase);
                    var vendorName = data.vendors[purchase.vendor_id];
                    var purchaseType = data.purchaseType[purchase.type];
                    console.log('Vendor Name:', vendorName);
                    console.log('purchaseType Name:', purchaseType);

                    var formattedDate = purchase.date.split('-').reverse().join('-');
                    var invoiceProducts = purchase.invoice_product;
                    console.log('invoiceProducts:', invoiceProducts);
                    var rows = '';

                    invoiceProducts.forEach(function(product, productIndex) {
                        console.log('Product:', product);
                        var material = '';
                        console.log('Material ID:', product.material_id);
                        console.log('Materials Name:', data.materials[product.material_id]);

                        if (product.material_id && data.materials[product.material_id]) {
                            material = data.materials[product.material_id];
                        } else {
                            console.error('Material ID not found or invalid for product:', product);
                        }

                        console.log('material:', material);

                        var row = '<tr>' +
                            '<td>' + (index + 1) + '</td>' +
                            '<td class="job-order-column">' + purchase.job_order_id + '</td>' +
                            '<td class="purchaseType">' + (purchaseType ? purchaseType :
                                'Undefined') + '</td>' +
                            '<td class="formattedDate">' + formattedDate + '</td>' +
                            '<td>' + (vendorName ? vendorName : 'Undefined') + '</td>' +
                            '<td>' + (material ? material : 'Undefined') + '</td>' +
                            '<td style="text-align: right;">' + product.quantity + '</td>' +
                            '<td style="text-align: right;">₹' + parseFloat(product.total)
                            .toLocaleString(
                                'en-IN', {
                                    maximumFractionDigits: 2,
                                    minimumFractionDigits: 2
                                }) + '</td>' +
                            // '<td>' + product.total + '</td>' +
                            '</tr>';

                        totalAmount += parseFloat(product.total);
                        rows += row;
                    });

                    $('#purchase_table_body').append(rows);
                });

                var totalRow = '<tr>' +
                    '<td colspan="6" style="text-align: right;"><b>Total:</b></td>' +
                    '<td style="text-align: right;"><b>₹' + totalAmount.toLocaleString(
                        'en-IN', {
                            maximumFractionDigits: 2,
                            minimumFractionDigits: 2
                        }) + '</b></td>' +
                    '</tr>';
                $('#purchase_table_body').append(totalRow);
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


            $('#job_orders').on('change', function() {
                calculateTotalAmount();
                applyTableFilters();
            });

            $('#purchase_type').on('change', function() {
                calculateTotalAmount();
                applyTableFilters();
            });


            function calculateTotalAmount() {
                var filteredJobOrder = $('#job_orders').val();
                var filteredDateRange = $('#date_range').val();
                var filteredPurchaseType = $('#purchase_type option:selected').text();
                var total = 0;

                $('#purchase_table_body tr').each(function() {
                    // debugger
                    var jobOrder = $(this).find('.job-order-column').text().trim();
                    var purchaseType = $(this).find('td.purchaseType').text();
                    var date = $(this).find('td.formattedDate').text();
                    var amountText = $(this).find('td:eq(7)').text().trim();
                    var amountValue = amountText.replace(/[^\d.-]/g, '');
                    var amount = parseFloat(amountValue);

                    if ((filteredJobOrder === '' || jobOrder === filteredJobOrder) &&
                        (filteredDateRange === '' || isDateInRange(date, filteredDateRange) &&
                            (filteredPurchaseType === '' || purchaseType === filteredPurchaseType || filteredPurchaseType === 'All'))) {
                        total += isNaN(amount) ? 0 : amount;
                        console.log(total);
                    }
                });

                var formattedTotal = '₹' + total.toLocaleString('en-IN', {
                    maximumFractionDigits: 2,
                    minimumFractionDigits: 2
                }) + ' /-';
                $('#total_amount').val(formattedTotal);
            }


            function isDateInRange(date, dateRange) {
                var startDate = moment(dateRange.split(' - ')[0], 'DD-MM-YYYY');
                var endDate = moment(dateRange.split(' - ')[1], 'DD-MM-YYYY');
                var currentDate = moment(date, 'DD-MM-YYYY');
                return currentDate.isBetween(startDate, endDate, null, '[]');
            }


            function applyTableFilters() {
                var filteredJobOrder = $('#job_orders').val();
                var filteredPurchaseType = $('#purchase_type option:selected').text();
                var filteredDateRange = $('#date_range').val();

                $('#purchase_table_body tr').each(function() {
                    debugger
                    var jobOrder = $(this).find('.job-order-column').text().trim();
                    var purchaseType = $(this).find('td.purchaseType')
                        .text();
                    var date = $(this).find('td.formattedDate')
                        .text();

                    if ((filteredJobOrder && jobOrder !== filteredJobOrder) ||
                        (filteredPurchaseType !== 'All' && purchaseType !== filteredPurchaseType)
                    ) {
                        $(this).hide();
                    } else if (filteredDateRange) {
                        var startDate = moment(filteredDateRange.split(' - ')[0], 'YYYY-MM-DD');
                        var endDate = moment(filteredDateRange.split(' - ')[1], 'YYYY-MM-DD');
                        var currentDate = moment(date, 'YYYY-MM-DD');
                        if (!currentDate.isBetween(startDate, endDate, null, '[]')) {
                            $(this).hide();
                        } else {
                            $(this).show();
                        }
                    } else {
                        $(this).show();
                    }
                });
                calculateTotalAmount();
            }


            $(document).on("click", ".purchase_export", function() {
                var job_order = $('#job_orders').val();
                var date_range = $('#date_range').val();
                var export_url = "{{ url('purchases_export/report') }}";
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
