@extends('layouts.master')
@section('title', 'Purchase Create')

@section('content')
    <style>
        .textbox-right {
            text-align: right;
        }
    </style>
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-6">
                            <div class="title">
                                <h4>{{ $purchase->id ? 'Edit Purchase (Invoice)' : 'Create Purchase (Invoice)' }}</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ url('dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="{{ url('purchase') }}">Purchase</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        {{ $purchase->id ? 'Edit Purchase' : 'Create Purchase' }}
                                    </li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-6 text-right">
                            <button id="save_btn" class="btn btn-primary">
                                <i class="fa fa-floppy-o"></i>
                                {{ $purchase->id ? 'Update' : 'Save' }}
                            </button>
                        </div>
                    </div>
                </div>
                <div class="pd-20 bg-white border-radius-4 box-shadow">
                    {{-- <form id="purchase-form" method="POST" action="{{ url('purchase/submit') }}"> --}}
                    <form id="purchase-form" method="POST"
                        action="{{ $purchase->id ? route('purchase.edit', $purchase->id) : route('purchase.store') }}">
                        @csrf
                        <input type="hidden" name="edit_id" value="{{ $purchase->id }}">
                        <input type="hidden" name="final_total" id="final_total_input">
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label>Job Order</label>
                                <select class="form-control" id="job_order" name="job_order">
                                    <option value="">Select Job Order</option>
                                    @foreach ($job_orders as $jo)
                                        <option value="{{ $jo->id }}"
                                            {{ $purchase->job_order_id == $jo->id ? 'selected' : '' }}>{{ $jo->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label>Invoice No</label>
                                <input type="text" class="form-control" name="invoice_no" id="invoice_no"
                                    value="{{ isset($purchase) ? $purchase->invoice_no : '' }}">

                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-6 form-group">
                                <label>Type</label>
                                <select class="form-control" name="type" id="type">
                                    <option value="" disabled selected hidden>Select Type</option>
                                    @foreach ($PurchaseTypes as $type)
                                        <option value="{{ $type->id }}"
                                            {{ $purchase->type == $type->id ? 'selected' : '' }}>{{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6 form-group">
                                <label>Date</label>
                                <input type="date" class="form-control" name="date" id="date"
                                    value="{{ $purchase->date }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-header p-0">
                                        <label class="col-form-label text-primary ml-3">Bill To </label>
                                    </div>
                                    <div class="card-body p-3" style="min-height: 170px;">
                                        <h5>{{ $company_settings->name }}</h5>
                                        <span>{{ $company_settings->address }}</span><br>
                                        <b>Mobile : {{ $company_settings->mobile }}</b><br>
                                        {{-- <b>Email : {{ $company_settings->email }}</b><br> --}}
                                        <b>GST Number : {{ $company_settings->gst_number }}</b>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="card">
                                    <div class="card-header p-0">
                                        <label class="col-form-label text-primary ml-3">Vendor / Dealer </label>
                                    </div>
                                    <div class="card-body p-3" style="min-height: 170px;">
                                        <div class="form-group">
                                            <select class="form-control" id="vendor" name="vendor">
                                                <option value="">Select Vendor</option>
                                                @foreach ($vendors as $ven)
                                                    <option value="{{ $ven->id }}"
                                                        {{ $purchase->vendor_id == $ven->id ? 'selected' : '' }}>
                                                        {{ $ven->agency_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div id="vendor_view"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <table class="table table-bordered mt-3">
                            <thead>
                                <tr>
                                    <th style="width: 7%">S.NO</th>
                                    <th>Material / Product</th>
                                    <th style="width: 9%">Qty</th>
                                    <th style="width: 9%">Unit Price</th>
                                    <th style="width: 12%">Amount</th>
                                    <th style="width: 10%">GST</th>
                                    <th style="width: 12%">Sub Total</th>
                                    <th style="width: 7%">
                                        <button class="btn btn-sm btn-success" id="add_row_btn"><i
                                                class="bi bi-plus-square"></i></button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="products_table_body">
                                @foreach ($purchase->invoiceProduct as $key => $invoiceProduct)
                                    <tr id="tr{{ $key + 1 }}">
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <select name="material[]" class="form-control material"
                                                data-row_no="{{ $key + 1 }}">
                                                <option value="">Select Material</option>
                                                @foreach ($materials as $mat)
                                                    <option value="{{ $mat->id }}"
                                                        {{ $invoiceProduct->material_id == $mat->id ? 'selected' : '' }}>
                                                        {{ $mat->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" name="material_id[]"
                                                value="{{ $invoiceProduct->material_id }}">
                                        </td>
                                        <td>
                                            <input type="text" name="qty[]" class="form-control qty"
                                                data-row_no="{{ $key + 1 }}"
                                                value="{{ $invoiceProduct->quantity }}">
                                        </td>
                                        <td>
                                            <input type="text" name="unit[]" class="form-control unit"
                                                value="{{ $invoiceProduct->unit }}">
                                        </td>
                                        <td>
                                            <input type="text" name="amount[]" class="form-control amount"
                                                value="{{ $invoiceProduct->amount }}" data-row_no="{{ $key + 1 }}">
                                        </td>
                                        <td>
                                            <input type="text" name="gst[]" class="form-control gst"
                                                value="{{ $invoiceProduct->gst }}" data-row_no="{{ $key + 1 }}">
                                        </td>
                                        <td>
                                            <input type="text" name="total[]" class="form-control total"
                                                value="{{ $invoiceProduct->total }}" data-row_no="{{ $key + 1 }}">
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-danger"
                                                onclick="remove_row({{ $key + 1 }})">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>

                            <tfoot>
                                <tr>
                                    <th colspan="4"><span class="pull-right">Final Total : </span></th>
                                    <th><input type="text" class="form-control textbox-right" id="total_amount"
                                            readonly>
                                    </th>
                                    <th><input type="text" class="form-control textbox-right" id="total_gst" readonly>
                                    </th>
                                    <th><input type="text" class="form-control textbox-right" id="final_total"
                                            readonly>
                                    </th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('addscript')
    <script type="text/javascript">
        var row_no = 0;
        $(document).on("change", "#vendor", function() {
            var vendor = $("#vendor").val();
            $.ajax({
                url: "{{ url('vendors/fetch-edit') }}/" + vendor,
                dataType: "json",
                success: function(response) {
                    var html_content = '';
                    html_content += "<span>" + response.address + "</span><br>";
                    html_content += "<b>Mobile : " + response.mobile + "</b><br>";
                    html_content += "<b>GST Number : " + response.gst_number + "</b>";
                    $("#vendor_view").html(html_content);
                },
                error: function(code) {
                    toastr.error(code.statusText);
                },
            });
        });

        // $(document).on("change", ".material", function() {
        //     var material_id = this.value;
        //     var row_id = $(this).data("row_no");
        //     $.ajax({
        //         url: "{{ url('materials/fetch-edit') }}/" + material_id,
        //         dataType: "json",
        //         success: function(response) {
        //             $("#unit" + row_id).val(response.unit_type);
        //         },
        //         error: function(code) {
        //             alert(code.statusText);
        //         },
        //     });
        // });


        $(document).on("input", ".amount, .qty", function() {
            var row_id = $(this).closest('tr').attr('id').replace('tr', '');
            var amount = parseFloat($(this).closest('tr').find(".amount").val()) ||
                0;
            var qty = parseFloat($(this).closest('tr').find(".qty").val()) ||
                0;
            console.log("Row ID:", row_id);
            console.log("Amount:", amount);
            console.log("Quantity:", qty);
            var gst_percentage = 18;
            var gst_value = (amount * qty) * (gst_percentage / 100);
            var total_amount = amount * qty + gst_value;
            $(this).closest('tr').find(".gst").val(gst_value.toFixed(2));
            $(this).closest('tr').find(".total").val(total_amount.toFixed(2));
            calculate();
            count();
        });


        $(document).on("input", ".unit, .qty", function() {
            var row_id = $(this).closest('tr').attr('id').replace('tr', '');
            var unit_price = parseFloat($(this).closest('tr').find(".unit").val()) || 0;
            var qty = parseFloat($(this).closest('tr').find(".qty").val()) || 0;
            console.log("Row ID:", row_id);
            console.log("Unit Price:", unit_price);
            console.log("Quantity:", qty);
            var gst_percentage = 18;
            var amount = unit_price * qty;
            var gst_value = (amount * gst_percentage / 100);
            var total_amount = amount + gst_value;
            $(this).closest('tr').find(".amount").val(amount.toFixed(2));
            $(this).closest('tr').find(".gst").val(gst_value.toFixed(2));
            $(this).closest('tr').find(".total").val(total_amount.toFixed(2));
            calculate();
            count();
        });

        $(document).on("change", ".gst", function() {
            var row_id = $(this).data("row_no");
            var gst_value = this.value;
            $(this).val(parseFloat(gst_value).toFixed(2));
            var amount = $("#amount" + row_id).val();
            var total_amount = parseFloat(amount) + parseFloat(gst_value);
            $("#total" + row_id).val(parseFloat(total_amount).toFixed(2));
            calculate();
            count();
        });


        // $(document).on("input", ".amount, .qty", function() {
        //     debugger
        //     var row_id = $(this).data("row_no");
        //     var amount = $("#amount" + row_id).val();
        //     var qty = $("#qty" + row_id).val();
        //     console.log("Row ID:", row_id);
        //     console.log("Amount:", amount);
        //     console.log("Quantity:", qty);
        //     var gst_percentage = 18;
        //     var gst_value = (amount / 100) * gst_percentage;
        //     var total_amount = (parseFloat(amount) * parseFloat(qty)) + parseFloat(gst_value);
        //     $("#gst" + row_id).val(gst_value);
        //     $("#total" + row_id).val(total_amount);
        //     calculate();
        //     count();
        // });
        // $(document).on("change", ".qty", function() {
        //     debugger
        //     var qty_value = this.value;
        //     var row_id = $(this).data("row_no");
        //     var qty = $("#qty" + row_id).val();
        //     var gst_percentage = 18;
        //     var amount = $("#amount" + row_id).val();
        //     var gst_value = (amount / 100) * gst_percentage;
        //     var total_amount = (parseFloat(amount) * parseFloat(qty)) + parseFloat(gst_value);
        //     $("#gst" + row_id).val(gst_value);
        //     $("#total" + row_id).val(total_amount);
        //     calculate();
        // });

        $(document).on("change", ".qty", function() {
            calculate();
            count();
        });


        function count() {
            var total_qty = 0;
            $(".qty").each(function() {
                var qty = $(this).val();
                if (!isNaN(parseFloat(qty))) {
                    total_qty += parseFloat(qty);
                } else {
                    console.log('Invalid quantity value.');
                }
            });

            $("#total_qty").val(total_qty.toFixed(2));
            console.log('total_qty', total_qty);
        }



        $(document).ready(function() {
            calculate();
            @if (!$purchase->id)
                add_row();
            @endif
            calculate();
            $("#add_row_btn").click(function(event) {
                event.preventDefault();
                add_row();
                calculate();
            });
        });


        var materials = [
            @foreach ($materials as $mat)
                {
                    id: '{{ $mat->id }}',
                    name: '{{ $mat->name }}'
                },
            @endforeach
        ];


        function add_row() {
            row_no++;
            var html_text = `<tr id="tr${row_no}">
        <th><input type="text" class="form-control sno" readonly></th>
        <th>
            <select name="material[]" class="form-control material" id="material${row_no}" data-row_no="${row_no}">
                <option value="">Select Materials</option>`;
            materials.forEach(function(material) {
                html_text += `<option value="${material.id}">${material.name}</option>`;
            });
            html_text += `</select>
        </th>
        <th><input type="text" name="qty[]" class="form-control qty" id="qty${row_no}" data-row_no="${row_no}"></th> <!-- Make sure data-row_no is set here -->
        <th><input type="text" name="unit[]" class="form-control unit textbox-right" id="unit${row_no}"></th>
        <th><input type="text" name="amount[]" class="form-control amount textbox-right" id="amount${row_no}" data-row_no="${row_no}"></th>
        <th><input type="text" name="gst[]" class="form-control gst textbox-right" id="gst${row_no}" data-row_no="${row_no}"></th>
        <th><input type="text" name="total[]" class="form-control total textbox-right" id="total${row_no}" data-row_no="${row_no}" readonly></th>
        <th>
            <button class="btn btn-sm btn-danger" onClick=remove_row(${row_no})><i class="bi bi-trash3"></i></button>
        </th>
    </tr>`;
            $("#products_table_body").append(html_text);
            sno_arrange();
        }


        function remove_row(row_id) {
            $("#tr" + row_id).remove();
            sno_arrange();
            calculate();
        }

        function sno_arrange() {
            var sno = 0;
            $(".sno").each(function() {
                sno++;
                $(this).val(sno);
            });
        }

        $(document).ready(function() {
            calculate();

            @if ($purchase->id)
                $("#products_table_body tr").each(function() {
                    calculate($(this));
                });
            @endif
        });

        @if ($purchase->id)
            var total_amount = 0;
            var total_gst = 0;
            var final_total = 0;

            $("#products_table_body tr").each(function() {
                calculate($(this));
            });
        @endif

        function calculate() {
            var total_amount = 0;
            var total_gst = 0;
            var final_total = 0;

            $("#products_table_body tr").each(function() {
                var amountInput = $(this).find("input[name='amount[]']");
                var gstInput = $(this).find("input[name='gst[]']");
                var qtyInput = $(this).find("input[name='qty[]']");

                var amount = amountInput.val();
                var gst = gstInput.val();
                var qty = qtyInput.val();
                console.log('qty', qty);

                if (!isNaN(parseFloat(amount)) && !isNaN(parseFloat(gst)) && !isNaN(parseFloat(qty))) {
                    total_amount += parseFloat(amount);
                    var total = (parseFloat(amount) + parseFloat(gst));
                    console.log('totalamount',total);
                    final_total += total;
                    total_gst += parseFloat(gst);
                } else {
                    console.log('Invalid amount, gst, or quantity value.');
                }
            });

            $("#total_amount").val(total_amount.toFixed(2));
            console.log('total_amount', total_amount);
            $("#total_gst").val(total_gst.toFixed(2));
            console.log('total_gst', total_gst);
            $("#final_total").val(final_total.toFixed(2));
            console.log('final_total', final_total);
            $("#final_total_input").val(final_total.toFixed(2));
        }
    </script>

    <script>
        $("#purchase-form").validate({
            submitHandler: function(form) {
                $("#save_btn").prop("disabled", true);
                var data = new FormData(form);
                var url = "{{ url('purchase/store') }}";
                $.ajax({
                    type: "POST",
                    url: url,
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function() {
                        $("#save_btn").prop("disabled", false);
                        window.location.href = "{{ url('purchase') }}";
                    },
                    error: function(code) {
                        alert(code.statusText);
                    },
                });
                return false;
            }
        });

        $(document).on("click", "#save_btn", function() {
            var vendor = $("#vendor").val();
            var job_order = $("#job_order").val();

            var vendor_error = 0;
            if (vendor === "") {
                toastr.error("Select Vendor");
                vendor_error++;
            }
            if (job_order === "") {
                toastr.error("Select Job Order");
                vendor_error++;
            }

            var material_error = 0;
            $(".material").each(function() {
                if (this.value === "") {
                    material_error++;
                }
            });
            if (material_error != 0) {
                toastr.error("Select All Materials");
            }

            var qty_error = 0;
            $(".qty").each(function() {
                if (this.value === "" || isNaN(this.value)) {
                    qty_error++;
                }
            });
            if (qty_error != 0) {
                toastr.error("Enter Valid Quantity");
            }

            var amount_error = 0;
            $(".amount").each(function() {
                if (this.value === "" || isNaN(this.value)) {
                    amount_error++;
                }
            });
            if (amount_error != 0) {
                toastr.error("Enter Valid Amount");
            }

            var gst_error = 0;
            $(".gst").each(function() {
                console.log(this.value);
                if (this.value === "" || isNaN(this.value)) {
                    gst_error++;
                }
            });
            if (gst_error != 0) {
                toastr.error("Enter Valid GST");
            }

            if (vendor_error == 0 && material_error == 0 && qty_error == 0 && amount_error == 0 && gst_error == 0) {
                $("#purchase-form").submit();
            }
        });
    </script>
    <script>
        var purchase = @json($purchase);
    </script>
@endsection
