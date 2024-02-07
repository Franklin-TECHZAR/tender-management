@extends('layouts.master')
@section('title', 'Labours')

@section('content')

<div class="main-container">
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-6">
                        <div class="title">
                            <h4>Labours</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ url('admin/dashboard') }}">Home</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Labours
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-6 text-right">
                        <button class="btn btn-primary add-btn" data-toggle="modal" data-target="#labour-modal">
                            <i class="bi-plus-circle"></i> Create New
                        </button>
                    </div>
                </div>
            </div>
            <div class="pd-20 bg-white border-radius-4 box-shadow">

                <table class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th>S.NO</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Mobile</th>
                            {{-- <th>Address</th> --}}
                            <th width="100px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="labour-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
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
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" id="name" required>
                    </div>
                    <div class="form-group">
                        <label>Type</label>
                        <input type="text" class="form-control" name="type" id="type" required>
                    </div>
                    <div class="form-group">
                        <label>Mobile</label>
                        <input type="text" class="form-control" name="mobile" id="mobile" required>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea class="form-control" name="address" id="address"></textarea>
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
        ajax: "{{ url('labours/fetch') }}",
        columns: [{
                data: 'DT_RowIndex',
                name: 'id'
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'type',
                name: 'type'
            },
            {
                data: 'mobile',
                name: 'mobile'
            },
            // {
            //     data: 'address',
            //     name: 'address'
            // },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },
        ]
    });

    $("#labour-form").validate({
        rules: {
            mobile: {
                digits: true,
                minlength: 10,
                maxlength: 10
            }
        },
        submitHandler: function(form) {
            $("#submit-btn").prop("disabled", true);
            var data = new FormData(form);
            var url = "{{ url('labours/store') }}";
            $.ajax({
                type: "POST",
                url: url,
                data: data,
                processData: false,
                contentType: false,
                success: function() {
                    $("#labour-modal").modal("hide");
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
            url: "{{ url('labours/fetch-edit') }}/" + edit_id,
            dataType: "json",
            success: function(response) {
                $("#name").val(response.name);
                $("#type").val(response.type);
                $("#mobile").val(response.mobile);
                $("#address").val(response.address);
                $("#modal-title-label").html('Edit Labour');
                $("#labour-modal").modal("show");
            },
            error: function(code) {
                alert(code.statusText);
            },
        });
    });

    $(document).on("click", ".add-btn", function() {
            $("#edit_id").val("");
            $("#labour-form")[0].reset();
            $("#modal-title-label").html('Create Labour');
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
            url: "{{ url('labours/delete') }}/" + edit_id,
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
