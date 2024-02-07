@extends('layouts.master')
@section('title', 'Vendor')

@section('content')

    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-6">
                            <div class="title">
                                <h4>Vendors / Dealer</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ url('admin/dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        Vendors
                                    </li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-6 text-right">
                            <button class="btn btn-primary add-btn" data-toggle="modal" data-target="#vendor-modal">
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
                                <th>Agency/Store Name</th>
                                <th>Contact Name</th>
                                <th>Mobile</th>
                                <th>City</th>
                                {{-- <th>Address</th> --}}
                                <th>GST Number</th>
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

    <div class="modal fade" id="vendor-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title-label">
                        Create Vendor
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        ×
                    </button>
                </div>
                <form id="vendor-form">
                    @csrf
                    <input type="hidden" name="edit_id" id="edit_id">
                    <input type="hidden" name="edit_status" id="edit_status">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="agency_name">Agency/Store Name</label>
                            <input type="text" class="form-control" name="agency_name" id="agency_name" required>
                        </div>
                        <div class="form-group">
                            <label for="contact_name">Contact Name</label>
                            <input type="text" class="form-control" name="contact_name" id="contact_name" required>
                        </div>
                        <div class="form-group">
                            <label for="mobile">Mobile</label>
                            <input type="text" class="form-control" name="mobile" id="mobile" required>
                        </div>
                        <div class="form-group">
                            <label for="city">City</label>
                            <input type="text" class="form-control" name="city" id="city" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea class="form-control" name="address" id="address"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="gst_number">GST Number</label>
                            <input type="text" class="form-control" name="gst_number" id="gst_number" required>
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
            ajax: "{{ url('vendors/fetch') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'id',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'agency_name',
                    name: 'agency_name'
                },
                {
                    data: 'contact_name',
                    name: 'contact_name'
                },
                {
                    data: 'mobile',
                    name: 'mobile'
                },
                {
                    data: 'city',
                    name: 'city'
                },
                // {
                //     data: 'address',
                //     name: 'address'
                // },
                {
                    data: 'gst_number',
                    name: 'gst_number'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        $("#vendor-form").validate({
            rules: {
                agency_name: "required",
                contact_name: "required",
                mobile: {
                    required: true,
                    digits: true,
                    minlength: 10,
                    maxlength: 10
                },
                city: "required",
                // address: "required",
                gst_number: "required",
            },
            messages: {
                agency_name: "Please enter Agency/Store Name",
                contact_name: "Please enter Contact Name",
                mobile: {
                    required: "Please enter Mobile Number",
                    digits: "Please enter only digits",
                    minlength: "Mobile number must be at least 10 digits long",
                    maxlength: "Mobile number cannot exceed 10 digits"
                },
                city: "Please enter City",
                // address: "Please enter Address",
                gst_number: "Please enter GST Number",
            },

            submitHandler: function(form) {
                $("#submit-btn").prop("disabled", true);
                var data = new FormData(form);
                var url = "{{ url('vendors/store') }}";
                $.ajax({
                    type: "POST",
                    url: url,
                    data: data,
                    processData: false,
                    contentType: false,
                    success: function() {
                        $("#vendor-modal").modal("hide");
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
                url: "{{ url('vendors/fetch-edit') }}/" + edit_id,
                dataType: "json",
                success: function(response) {
                    $("#agency_name").val(response.agency_name);
                    $("#contact_name").val(response.contact_name);
                    $("#mobile").val(response.mobile);
                    $("#city").val(response.city);
                    $("#address").val(response.address);
                    $("#gst_number").val(response.gst_number);

                    $("#modal-title-label").html('Edit Vendor');
                    $("#vendor-modal").modal("show");
                },
                error: function(code) {
                    alert(code.statusText);
                },
            });
        });

        $(document).on("click", ".add-btn", function() {
            $("#edit_id").val("");
            $("#vendor-form")[0].reset();
            $("#modal-title-label").html('Create Vendor');
        });

        $(document).on("click", ".delete-btn", function() {
            var edit_id = $(this).data('id');
            $("#edit_id").val(edit_id);
            $("#delete-confirm-text").text("Are you confirm to Delete this Vendor");
            $("#delete-confirm-modal").modal("show");
        });

        $(document).on("click", "#confirm-yes-btn", function() {
            var edit_id = $("#edit_id").val();
            $("#confirm-yes-btn").prop("disabled", true);

            $.ajax({
                url: "{{ url('vendors/delete') }}/" + edit_id,
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
