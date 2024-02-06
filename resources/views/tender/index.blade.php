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
                                <h4>Tenders</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ url('admin/dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        Tenders
                                    </li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-6 text-right">
                            <button class="btn btn-primary add-btn" data-toggle="modal" data-target="#tender-modal">
                                <i class="bi-plus-circle"></i> Create New
                            </button>
                        </div>
                    </div>
                </div>
                <div class="pd-20 bg-white border-radius-4 box-shadow">

                    <div class="tab mb-3">
                        <ul class="nav nav-tabs customtab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link @if ($show == 'New') active @endif" href="?show=New"
                                    role="tab" aria-selected="true"> <i class="bi bi-check2"></i> New</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link @if ($show == 'Job Orders') active @endif" href="?show=Job Orders"
                                    role="tab" aria-selected="false"> <i class="bi bi-check2-all"></i> Job Orders</a>
                            </li>
                        </ul>
                    </div>

                    <table class="table table-bordered data-table">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>Name</th>
                                <th>City</th>
                                <th>Budget</th>
                                <th>Status</th>
                                <th width="100px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
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
                        Create Tender
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        ×
                    </button>
                </div>
                <form id="tender-form">
                    @csrf
                    <input type="hidden" name="edit_id" id="edit_id">
                    <input type="hidden" name="edit_status" id="edit_status">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" id="name" required>
                        </div>
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" class="form-control" name="city" id="city" required>
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <textarea class="form-control" name="address" id="address" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Budget</label>
                            <input type="number" class="form-control" name="budget" id="budget" required>
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <textarea class="form-control" name="description" id="description"></textarea>
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

    <div class="modal fade" id="status-confirm-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center font-18">
                    <h4 class="padding-top-30 mb-30 weight-500" id="status-confirm-text">
                        Are you sure you want to continue?
                    </h4>
                    <div class="padding-bottom-30 text-center">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fa fa-times"></i> NO
                        </button>
                        <button type="button" id="status-confirm-btn" class="btn btn-primary" data-dismiss="modal">
                            <i class="fa fa-check"></i> YES
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('addscript')
    <script type="text/javascript">
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ url('tender/fetch') }}?show={{ $show }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'id',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'city',
                    name: 'city'
                },
                {
                    data: 'budget_text',
                    name: 'budget'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $("#tender-form").validate({
            submitHandler: function(form) {
                $("#submit-btn").prop("disabled", true);
                var data = new FormData(form);
                var url = "{{ url('tender/store') }}";
                $.ajax({
                    type: "POST",
                    url: url,
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function() {
                        $("#tender-modal").modal("hide");
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
                url: "{{ url('tender/fetch-edit') }}/" + edit_id,
                dataType: "json",
                success: function(response) {

                    $("#name").val(response.name);
                    $("#city").val(response.city);
                    $("#address").val(response.address);
                    $("#budget").val(response.budget);
                    $("#description").val(response.description);

                    $("#modal-title-label").html('Edit Tender');
                    $("#tender-modal").modal("show");
                },
                error: function(code) {
                    alert(code.statusText);
                },
            });
        });

        $(document).on("click", ".add-btn", function() {
            $("#edit_id").val("");
            $("#tender-form")[0].reset();
            $("#modal-title-label").html('Create Tender');
        });

        $(document).on("click", ".delete-btn", function() {
            var edit_id = $(this).data('id');
            $("#edit_id").val(edit_id);
            $("#delete-confirm-text").text("Are you confirm to Delete this Tender");
            $("#delete-confirm-modal").modal("show");
        });

        $(document).on("click", "#confirm-yes-btn", function() {
            var edit_id = $("#edit_id").val();
            $("#confirm-yes-btn").prop("disabled", true);

            $.ajax({
                url: "{{ url('tender/delete') }}/" + edit_id,
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

        // Status Change

        $(document).on("click", ".change-status-btn", function() {
            var edit_id = $(this).data('id');
            var status = $(this).data('status');
            $("#edit_id").val(edit_id);
            $("#edit_status").val(status);
            $("#status-confirm-text").text("Are you confirm to " + status + " this Tender");
            $("#status-confirm-modal").modal("show");
        });

        $(document).on("click", "#status-confirm-btn", function() {
            var edit_id = $("#edit_id").val();
            var status = $("#edit_status").val();
            $("#status-confirm-btn").prop("disabled", true);

            $.ajax({
                url: "{{ url('tender/chage-status') }}",
                data: {
                    edit_id: edit_id,
                    status: status
                },
                method: "GET",
                dataType: "json",
                success: function(response) {
                    table.clear().draw();
                    $("#status-confirm-btn").prop("disabled", false);
                },
                error: function(code) {
                    alert(code.statusText);
                },
            });
        });

        $(document).on("click", ".job-order-change-btn", function() {
            var edit_id = $(this).data('id');
            var status = $(this).data('status');
            $("#edit_id").val(edit_id);
            $("#edit_status").val(status);
            $("#status-confirm-text").text("Are you confirm to " + status + " this Tender");
            $("#status-confirm-modal").modal("show");
        });

    </script>
@endsection
