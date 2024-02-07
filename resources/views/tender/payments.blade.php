@extends('layouts.master')
@section('title', 'Tender')

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
                                        <a href="{{ url('tender') }}?show=New">Tenders</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        {{ $tender->name }}
                                    </li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-6 text-right">
                            <a href="{{ url('tender/payment-export')."/".$tender->id }}" class="btn btn-success">
                                <i class="bi bi-file-earmark-excel"></i> Export
                            </a>
                            <button class="btn btn-primary add-btn" data-toggle="modal" data-target="#tender-modal">
                                <i class="bi-plus-circle"></i> New Payment
                            </button>
                        </div>
                    </div>
                </div>
                <div class="pd-20 bg-white border-radius-4 box-shadow">
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
                        <tbody id="table-body">
                        </tbody>
                    </table>
                    <br>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tender-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
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
                <form id="tender-form">
                    @csrf
                    <input type="hidden" name="tender_id" id="tender_id" value="{{ $tender->id }}">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" class="form-control" name="date" id="date" required>
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
            $("#tender-form")[0].reset();
        });
        $("#tender-form").validate({
            submitHandler: function(form) {
                $("#submit-btn").prop("disabled", true);
                var data = new FormData(form);
                var url = "{{ url('tender/payment-store') }}";
                $.ajax({
                    type: "POST",
                    url: url,
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $("#tender-modal").modal("hide");
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
                url: "{{ url('tender/remove-payment-log') }}/" + edit_id,
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
            var tender_id = $("#tender_id").val();

            var html_content = `<tr>
                                <td class="text-center" colspan='7'>Please Wait</td>
                            </tr>`;
            $("#table-body").html(html_content);

            $.ajax({
                type: "GET",
                url: "{{ url('tender/fetch-payment-log') }}",
                data: {
                    tender_id: tender_id
                },
                dataType: "json",
                success: function(data) {
                    html_content = '';
                    if (data.length == 0) {
                        html_content += `<tr>
                                <td class="text-center" colspan='7'>No Payment Log Found</td>
                            </tr>`;

                    } else {
                        for (var i = 0; i < (data.length - 1); i++) {
                            html_content += `<tr>
                                <td>${i+1}</td>
                                <td>${data[i].date}</td>
                                <td>${data[i].description}</td>
                                <td>${data[i].credit}</td>
                                <td>${data[i].debit}</td>
                                <td>${data[i].balance}</td>
                                <td>
                                    <button class="btn btn-danger btn-sm delete-btn" data-id="${data[i].id}"><i class="fa fa-trash"></i></button>
                                </td>
                            </tr>`;
                        }

                        html_content += `<tr>
                                <td colspan='3'>${data[i].description}</td>
                                <td>${data[i].credit}</td>
                                <td>${data[i].debit}</td>
                                <td>${data[i].balance}</td>
                                <td></td>
                            </tr>`;

                    }
                    $("#table-body").html(html_content);
                },
                error: function(code) {
                    toastr.error(code.statusText);
                },
            });

        }
    </script>
@endsection
