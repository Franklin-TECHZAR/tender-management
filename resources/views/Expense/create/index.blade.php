@extends('layouts.master')
@section('title', 'Expenses')
@section('content')
<div class="main-container">
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-6">
                        <div class="title">
                            <h4>Expenses Management</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ url('admin/dashboard') }}">Home</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Create Expenses
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-6 text-right">
                        <button class="btn btn-primary add-btn" data-toggle="modal" data-target="#expense-modal">
                            <i class="bi bi-plus"></i> Create New
                        </button>
                    </div>
                </div>
            </div>
            <div class="pd-20 bg-white border-radius-4 box-shadow">
                <table class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th>S.NO</th>
                            <th>Job Order</th>
                            <th>Payment To</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                            {{-- <th>Description</th> --}}
                            <th>Payment Mode</th>
                            {{-- <th>Payment Details</th> --}}
                            <th width="100px">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="expense-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-title-label">
                    Create Expense
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    ×
                </button>
            </div>
            <form id="expense-form">
                @csrf
                <input type="hidden" name="edit_id" id="edit_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="job_order">Job Order</label>
                        <select class="form-control" name="job_order" id="job_order" required>
                            <option value="" disabled selected hidden>Select Job Order</option>
                            @foreach($tenders as $tender)
                                <option value="{{ $tender }}">{{ $tender }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Payment To</label>
                        <input type="text" class="form-control" name="payment_to" id="payment_to" required>
                    </div>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" class="form-control" name="date" id="date" required>
                    </div>
                    <div class="form-group">
                        <label>Type</label>
                        <select class="form-control" name="type" id="type" required>
                            <option value="" disabled selected hidden>Select Type</option>
                            @foreach($ExpenseType as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Amount</label>
                        <input type="number" class="form-control" name="amount" id="amount" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="description" id="description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="payment_mode">Payment Mode</label>
                        <select class="form-control" name="payment_mode" id="payment_mode" required>
                            <option value="" disabled selected hidden>Select Payment Mode</option>
                            <option value="Cash">Cash</option>
                            <option value="Check">Check</option>
                            <option value="Neft">Neft</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Payment Details</label>
                        <textarea class="form-control" name="payment_details" id="payment_details" required></textarea>
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
    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('expenses/create/fetch') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'job_order', name: 'job_order' },
            { data: 'payment_to', name: 'payment_to' },
            { data: 'date', name: 'date' },
            { data: 'type', name: 'type' },
            { data: 'amount', name: 'amount' },
            // { data: 'description', name: 'description' },
            { data: 'payment_mode', name: 'payment_mode' },
            // { data: 'payment_details', name: 'payment_details' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });

    $("#expense-form").validate({
        rules: {
            job_order: "required",
            payment_to: "required",
            date: "required",
            type: "required",
            amount: {
                required: true,
                number: true
            },
            // description: "required",
            payment_mode: "required",
            payment_details: "required"
        },
        messages: {
            job_order: "Please enter job order",
            payment_to: "Please enter payment to",
            date: "Please enter date",
            type: "Please enter type",
            amount: {
                required: "Please enter amount",
                number: "Please enter a valid number"
            },
            // description: "Please enter description",
            payment_mode: "Please enter payment mode",
            payment_details: "Please enter payment details"
        },
        submitHandler: function(form) {
            $("#submit-btn").prop("disabled", true);
            var data = new FormData(form);
            var url = "{{ url('expenses/create/store') }}";
            $.ajax({
                type: "POST",
                url: url,
                data: data,
                processData: false,
                contentType: false,
                success: function() {
                    $("#expense-modal").modal("hide");
                    table.clear().draw();
                    $("#submit-btn").prop("disabled", false);
                },
                error: function(code) {
                    alert(code.statusText);
                },
            });
            return false;
        }
    });

    $(document).on("click", ".edit-btn", function() {
        var edit_id = $(this).data('id');
        $("#edit_id").val(edit_id);
        $.ajax({
            url: "{{ url('expenses/create/fetch-edit') }}/" + edit_id,
            dataType: "json",
            success: function(response) {
                $("#job_order").val(response.job_order);
                $("#payment_to").val(response.payment_to);
                $("#date").val(response.date);
                $("#type").val(response.type);
                $("#amount").val(response.amount);
                $("#description").val(response.description);
                $("#payment_mode").val(response.payment_mode);
                $("#payment_details").val(response.payment_details);
                $("#modal-title-label").html('Edit Expense');
                $("#expense-modal").modal("show");
            },
            error: function(code) {
                alert(code.statusText);
            },
        });
    });

    $(document).on("click", ".add-btn", function() {
        $("#edit_id").val("");
        $("#expense-form")[0].reset();
        $("#modal-title-label").html('Create Expense');
    });

    $(document).on("click", ".delete-btn", function() {
        var edit_id = $(this).data('id');
        $("#edit_id").val(edit_id);
        $("#delete-confirm-text").text("Are you sure you want to delete this Expense?");
        $("#delete-confirm-modal").modal("show");
    });

    $(document).on("click", "#confirm-yes-btn", function() {
        var edit_id = $("#edit_id").val();
        $("#confirm-yes-btn").prop("disabled", true);
        $.ajax({
            url: "{{ url('expenses/create/delete') }}/" + edit_id,
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
</script>
@endsection
