@extends('layouts.master')
@section('title', 'Materials')

@section('content')

<div class="main-container">
    <div class="pd-ltr-20 xs-pd-20-10">
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-6">
                        <div class="title">
                            <h4>Materials</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ url('admin/dashboard') }}">Home</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    Materials
                                </li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-6 text-right">
                        <button class="btn btn-primary add-btn" data-toggle="modal" data-target="#material-modal">
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
                            <th>Unit Type</th>
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

<div class="modal fade" id="material-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal-title-label">
                    Create Material
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    ×
                </button>
            </div>
            <form id="material-form">
                @csrf
                <input type="hidden" name="edit_id" id="edit_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" id="name" required>
                    </div>
                    <div class="form-group">
                        <label>Unit Type</label>
                        <input type="text" class="form-control" name="unit_type" id="unit_type" required>
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
        ajax: "{{ url('materials/fetch') }}",
        columns: [{
                data: 'DT_RowIndex',
                name: 'id'
            },
            {
                data: 'name',
                name: 'name'
            },
            {
                data: 'unit_type',
                name: 'unit_type'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },
        ]
    });

    $("#material-form").validate({
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
            var url = "{{ url('materials/store') }}";
            $.ajax({
                type: "POST",
                url: url,
                data: data,
                processData: false,
                contentType: false,
                success: function() {
                    $("#material-modal").modal("hide");
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
            url: "{{ url('materials/fetch-edit') }}/" + edit_id,
            dataType: "json",
            success: function(response) {
                $("#name").val(response.name);
                $("#unit_type").val(response.unit_type);
                $("#modal-title-label").html('Edit Material');
                $("#material-modal").modal("show");
            },
            error: function(code) {
                alert(code.statusText);
            },
        });
    });

    $(document).on("click", ".add-btn", function() {
        $("#edit_id").val("");
        $("#material-form")[0].reset();
        $("#modal-title-label").html('Create Material');
    });

    $(document).on("click", ".delete-btn", function() {
            var edit_id = $(this).data('id');
            $("#edit_id").val(edit_id);
            $("#delete-confirm-text").text("Are you confirm to Delete this Material");
            $("#delete-confirm-modal").modal("show");
        });

        $(document).on("click", "#confirm-yes-btn", function() {
            var edit_id = $("#edit_id").val();
            $("#confirm-yes-btn").prop("disabled", true);

            $.ajax({
                url: "{{ url('materials/delete') }}/" + edit_id,
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
