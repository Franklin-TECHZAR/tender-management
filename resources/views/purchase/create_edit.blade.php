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
                                <h4>Create Purchase (Invoice)</h4>
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
                                        Create Purchase
                                    </li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-6 text-right">
                            <button id="save_btn" class="btn btn-primary">
                                <i class="fa fa-floppy-o"></i> Save
                            </button>
                        </div>
                    </div>
                </div>
                <div class="pd-20 bg-white border-radius-4 box-shadow">
                    <form id="purchase-form" method="POST" action="{{ url('purchase/submit') }}">
                        @csrf
                        <div class="row">
                            <div class="col-sm-6 form-group">
                                <label>Job Order</label>
                                <select class="form-control" id="job_order" name="job_order">
                                    <option value="">Select Job Order</option>
                                    @foreach ($job_orders as $jo)
                                        <option value="{{ $jo->id }}">{{ $jo->name }}</option>
                                    @endforeach
                                </select>
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
                                                    <option value="{{ $ven->id }}">{{ $ven->agency_name }}</option>
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
                                    <th style="width: 9%">Unit</th>
                                    <th style="width: 12%">Amount</th>
                                    <th style="width: 10%">GST</th>
                                    <th style="width: 12%">Total</th>
                                    <th style="width: 7%">
                                        <button class="btn btn-sm btn-success" id="add_row_btn"><i
                                                class="bi bi-plus-square"></i></button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="products_table_body">
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4"><span class="pull-right">Total : </span></th>
                                    <th><input type="text" class="form-control textbox-right" id="total_amount" readonly>
                                    </th>
                                    <th><input type="text" class="form-control textbox-right" id="total_gst" readonly>
                                    </th>
                                    <th><input type="text" class="form-control textbox-right" id="final_total" readonly>
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

        $(document).on("change", ".material", function() {
            var material_id = this.value;
            var row_id = $(this).data("row_no");
            $.ajax({
                url: "{{ url('materials/fetch-edit') }}/" + material_id,
                dataType: "json",
                success: function(response) {
                    $("#unit" + row_id).val(response.unit_type);
                },
                error: function(code) {
                    alert(code.statusText);
                },
            });
        });

        $(document).on("change", ".amount", function() {
            var amount = this.value;
            var row_id = $(this).data("row_no");
            var gst_percentage = 18;
            var gst_value = (amount / 100) * gst_percentage;
            var total_amount = parseFloat(amount) + parseFloat(gst_value);
            $("#gst" + row_id).val(gst_value);
            $("#total" + row_id).val(total_amount);
            calculate();
        });

        $(document).on("change", ".gst", function() {
            var row_id = $(this).data("row_no");
            var gst_value = this.value;
            var amount = $("#amount" + row_id).val();
            var total_amount = parseFloat(amount) + parseFloat(gst_value);
            $("#total" + row_id).val(total_amount);
            calculate();
        });

        $(document).ready(function() {
            add_row();
            $("#add_row_btn").click(function() {
                add_row();
            });
        });

        function add_row() {
            row_no++;
            var html_text = `<tr id="tr${row_no}">
                                <th><input type="text" class="form-control sno" readonly></th>
                                <th>
                                    <select name="material[]" class="form-control material" id="material${row_no}" data-row_no="${row_no}">
                                        <option value="">Select Materials</option>
                                        @foreach ($materials as $mat)
                                            <option value="{{ $mat->id }}">{{ $mat->name }}</option>
                                        @endforeach
                                    </select>
                                </th>
                                <th><input type="text" name="qty[]" class="form-control qty"></th>
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

        function calculate() {
            var total_amount = 0;
            var total_gst = 0;
            var final_total = 0;

            $(".amount").each(function() {
                if (this.value) {
                    total_amount = parseFloat(total_amount) + parseFloat(this.value);
                }
            });
            $(".gst").each(function() {
                if (this.value) {
                    total_gst = parseFloat(total_gst) + parseFloat(this.value);
                }
            });
            $(".total").each(function() {
                if (this.value) {
                    final_total = parseFloat(final_total) + parseFloat(this.value);
                }
            });

            $("#total_amount").val(total_amount);
            $("#total_gst").val(total_gst);
            $("#final_total").val(final_total);
        }
    </script>

    <script>
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
@endsection
