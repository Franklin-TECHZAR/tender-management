@extends('layouts.master')
@section('title', 'vendor_payment')

@section('content')

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-6">
                            <div class="title">
                                <h4>Payments</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ url('dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item">
                                        <a href="{{ url('vendor_payment') }}?show=New">Vendor Payment</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        {{ $vendor_payment->agency_name }}
                                    </li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-6 text-right">
                            <a href="{{ url('vendor_payment/payment-export') . '/' . $vendor_payment->id }}"
                                class="btn btn-success">
                                <i class="bi bi-file-earmark-excel"></i> Export
                            </a>
                            <button class="btn btn-primary add-btn" data-toggle="modal" data-target="#vendor_payment-modal">
                                <i class="bi-plus-circle"></i> New Payment
                            </button>
                        </div>
                    </div>
                </div>
                <div class="pd-20 bg-white border-radius-4 box-shadow">
                    <div id="html_content"></div>
                    <br>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="vendor_payment-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title-label">
                        New Payment
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        ×
                    </button>
                </div>
                <input type="hidden" id="edit_id">
                <form id="vendor_payment-form">
                    @csrf
                    <input type="hidden" name="vendor_payment_id" id="vendor_payment_id" value="{{ $vendor_payment->id }}">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" class="form-control" name="date" id="date" required>
                        </div>
                        <div class="form-group">
                            <label>Amount For</label>
                            <select class="form-control" name="payment_for" id="payment_for" required>
                                <option value="">Select Amount For</option>
                                <option>ED Amount</option>
                                <option>PG Amount</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" class="form-control" name="amount" id="amount" required>
                        </div>
                        <div class="form-group">
                            <label>Type</label>
                            <select class="form-control" name="type" id="type" required>
                                <option value="">Select Type</option>
                                <option>Credit</option>
                                <option>Debit</option>
                            </select>
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
                            <label for="payment_details">Payment Details</label>
                            <textarea class="form-control" name="payment_details" id="payment_details" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" id="description" required></textarea>
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
    <script>
        $(document).ready(function() {
            fetch_table();
        });
        $(document).on("click", ".add-btn", function() {
            $("#vendor_payment-form")[0].reset();
        });
        $("#vendor_payment-form").validate({
            submitHandler: function(form) {
                $("#submit-btn").prop("disabled", true);
                var data = new FormData(form);
                var url = "{{ url('vendor_payment/payment-store') }}";
                $.ajax({
                    type: "POST",
                    url: url,
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $("#vendor_payment-modal").modal("hide");
                        $("#submit-btn").prop("disabled", false);
                        fetch_table();
                        toastr.success(response.message);
                    },
                    error: function(code) {
                        toastr.error(code.statusText);
                    },
                });
                return false;
            }
        });

        $(document).on("click", ".delete-btn", function() {
            var edit_id = $(this).data('id');
            $("#edit_id").val(edit_id);
            $("#delete-confirm-text").text("Are you confirm to Delete this Payment Log");
            $("#delete-confirm-modal").modal("show");
        });

        $(document).on("click", "#confirm-yes-btn", function() {
            var edit_id = $("#edit_id").val();
            $("#confirm-yes-btn").prop("disabled", true);

            $.ajax({
                url: "{{ url('vendor_payment/remove-payment-log') }}/" + edit_id,
                method: "GET",
                dataType: "json",
                success: function(response) {
                    fetch_table();
                    $("#confirm-yes-btn").prop("disabled", false);
                },
                error: function(code) {
                    toastr.error(code.statusText);
                },
            });
        });

        function fetch_table() {
            var vendor_payment_id = $("#vendor_payment_id").val();

            var html_content = `<h5 class="text-center">Please Wait..</h5>`;

            $("#html_content").html(html_content);

            $.ajax({
                type: "GET",
                url: "{{ url('vendor_payment/fetch-payment-log') }}",
                data: {
                    vendor_payment_id: vendor_payment_id
                },
                dataType: "json",
                success: function(main_array) {
                    html_content = '';

                    if (main_array.length == 0) {

                        html_content = `<h5 class="text-center">No Payment Log Found</h5>`;

                    } else {

                        for (var i = 0; i < (main_array.length); i++) {
                            html_content += `<h5 class="text-center p-2">${main_array[i].payment_for}</h5>
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>S.NO</th>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th class="text-right">Credit</th>
                                            <th class="text-right">Debit</th>
                                            <th class="text-right">Balance</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                <tbody>`;

                            let data = main_array[i].data;

                            for (var j = 0; j < (data.length - 1); j++) {
                                html_content += `<tr>
                                                <td>${j+1}</td>
                                                <td>${data[j].date}</td>
                                                <td>${data[j].description}</td>
                                                <td>${data[j].credit}</td>
                                                <td>${data[j].debit}</td>
                                                <td>${data[j].balance}</td>
                                                <td>
                                                    <button class="btn btn-danger btn-sm delete-btn" data-id="${data[j].id}"><i class="fa fa-trash"></i></button>
                                                </td>
                                            </tr>`;
                            }

                            html_content += `<tr>
                                    <td colspan='3'>${data[j].description}</td>
                                    <td>${data[j].credit}</td>
                                    <td>${data[j].debit}</td>
                                    <td>${data[j].balance}</td>
                                    <td></td>
                                </tr>`;

                            html_content += `</tbody>
                            </table><br>`;
                        }
                    }
                    $("#html_content").html(html_content);
                },
                error: function(code) {
                    toastr.error(code.statusText);
                },
            });

        }
    </script>
@endsection
