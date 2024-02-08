@extends('layouts.master')
@section('title', 'Purchase Create')

@section('content')

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
                            <a href="{{ url('purchase/create') }}" class="btn btn-primary">
                                <i class="fa fa-floppy-o"></i> Save
                            </a>
                        </div>
                    </div>
                </div>
                <div class="pd-20 bg-white border-radius-4 box-shadow">

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
                                <th>Product</th>
                                <th style="width: 9%">Qty</th>
                                <th style="width: 9%">Unit</th>
                                <th style="width: 12%">Amount</th>
                                <th style="width: 10%">GST</th>
                                <th style="width: 12%">Total</th>
                                <th style="width: 7%">
                                    <button class="btn btn-sm btn-success"><i class="bi bi-plus-square"></i></button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th><input type="text" class="form-control"></th>
                                <th><input type="text" class="form-control"></th>
                                <th><input type="text" class="form-control"></th>
                                <th><input type="text" class="form-control"></th>
                                <th><input type="text" class="form-control"></th>
                                <th><input type="text" class="form-control"></th>
                                <th><input type="text" class="form-control"></th>
                                <th>
                                    <button class="btn btn-sm btn-danger"><i class="bi bi-trash3"></i></button>
                                </th>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th>Total</th>
                                <th><input type="text" class="form-control"></th>
                                <th><input type="text" class="form-control"></th>
                                <th><input type="text" class="form-control"></th>
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
    </script>
@endsection
