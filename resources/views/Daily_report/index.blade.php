@extends('layouts.master')
@section('title', 'Labour Report')
@section('content')
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-6">
                            <div class="title">
                                <h4>Daily Work Management</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">
                                        <a href="{{ url('admin/dashboard') }}">Home</a>
                                    </li>
                                    <li class="breadcrumb-item active" aria-current="page">
                                        Daily Work Management
                                    </li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-md-6 d-flex justify-content-end align-items-center">
                            <button class="btn btn-success export-btn mr-2">
                                <i class="bi bi-file-earmark-excel"></i> Export
                            </button>
                            <button class="btn btn-primary add-btn" data-toggle="modal" data-target="#labour-report-modal">
                                <i class="bi bi-plus"></i> Create New
                            </button>
                        </div>
                    </div>
                </div>
                <div class="page-header">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="job_orders">Filter by Job Order:</label>
                                <select class="form-control" name="job_orders" id="job_orders" required>
                                    <option value="" selected>All</option>
                                    @foreach ($tenders as $id => $tenderName)
                                        <option value="{{ $id }}">{{ $tenderName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="date_range">Date Range:</label>
                                <input type="text" class="form-control" name="date_range" id="date_range">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="pd-20 bg-white border-radius-4 box-shadow">
                    <table class="table table-bordered data-table">
                        <thead>
                            <tr>
                                <th>S.NO</th>
                                <th>Job Order</th>
                                <th>Labour</th>
                                <th>Date</th>
                                <th>Description</th>
                                <th width="100px">Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="labour-report-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title-label">
                        Create Labour Report
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        ×
                    </button>
                </div>
                <form id="labour-report-form">
                    @csrf
                    <input type="hidden" name="edit_id" id="edit_id">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="job_order">Job Order</label>
                            <select class="form-control" name="job_order" id="job_order" required>
                                <option value="" disabled selected>Select Job Order</option>
                                @foreach ($tenders as $id => $tenderName)
                                    <option value="{{ $id }}">{{ $tenderName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="labour">Labour</label>
                            <select class="form-control" name="labour[]" id="labour" required multiple>
                                <option value="" disabled hidden>Select Labour</option>
                                @foreach ($Labour as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="date">Date</label>
                            <input type="date" class="form-control" name="date" id="date" required>
                            <div class="invalid-feedback">Please provide a valid date.</div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" name="description" id="description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer" id="modal-footer-buttons">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="close-btn">
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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(document).ready(function() {

            $('#labour').select2({
                width: '100%',
                tags: true
            });

            $('#date_range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });

            $('#date_range').on('apply.daterangepicker', function(ev, picker) {
                var startDate = picker.startDate;
                var endDate = picker.endDate;
                var currentDate = startDate.clone();
                var dateRangeString = '';

                while (currentDate.isSameOrBefore(endDate)) {
                    dateRangeString += currentDate.format('DD-MM-YYYY') + '|';
                    currentDate.add(1, 'day');
                }

                dateRangeString = dateRangeString.slice(0, -1);
                $(this).val(startDate.format('DD-MM-YYYY') + ' - ' + endDate.format('DD-MM-YYYY'));
                $(this).trigger('change');
                table.column(3).search(dateRangeString, true).draw();
            });


            $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
                table.column(3).search('').draw();
                calculateTotalAmount();
            });

            function calculateTotalAmount() {
                $.ajax({
                    url: "{{ url('salaries/fetch') }}",
                    success: function(response) {
                        var total = 0;
                        var filteredJobOrder = $('#job_orders').val();
                        var filteredDateRange = $('#date_range').val();

                        console.log('filteredDateRange', filteredDateRange);
                        console.log('response', response);
                        $.each(response.data, function(index, row) {
                            if (!row.deleted_at) {
                                var rowDate = new Date(row.date);
                                var startDate = new Date(filteredDateRange.split(' - ')[0]);
                                var endDate = new Date(filteredDateRange.split(' - ')[1]);

                                if ((filteredJobOrder === '' || row.job_order ===
                                        filteredJobOrder) &&
                                    (filteredDateRange === '' || (rowDate >= startDate &&
                                        rowDate <= endDate))) {
                                    var amount = parseFloat(row.amount.replace(/[^\d.]/g, ''));
                                    total += isNaN(amount) ? 0 : amount;
                                }
                            }
                        });

                        var formattedTotal = '₹ ' + total.toLocaleString('en-IN', {
                            maximumFractionDigits: 2,
                            minimumFractionDigits: 2
                        }) + ' /-';
                        $('#total_amount').val(formattedTotal);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }

            $('#job_orders').on('change', function() {
                table.column(1).search(this.value).draw();
                calculateTotalAmount();
            });

            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ url('labour_report/fetch') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'job_order',
                        name: 'job_order'
                    },
                    {
                        data: 'labour',
                        name: 'labour',
                    },
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'desc',
                        name: 'desc'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                columnDefs: [{
                    targets: 1,
                    visible: false
                }],
                footerCallback: function(row, date, end, display) {
                    calculateTotalAmount();
                }
            });


            $("#labour-report-form").validate({
                submitHandler: function(form) {
                    var data = new FormData(form);
                    var url = "{{ url('labour_report/store') }}";
                    $.ajax({
                        type: "POST",
                        url: url,
                        data: data,
                        processData: false,
                        contentType: false,
                        beforeSend: function() {
                            $("#submit-btn").prop("disabled", true);
                            $('.invalid-feedback').remove();
                            $('#date').removeClass('is-invalid');
                        },
                        success: function(response) {
                            if (response.status === 0) {
                                console.log('Error callback triggered');
                                $('#date').addClass('is-invalid').after(
                                    '<div class="invalid-feedback">' + response
                                    .message + '</div>');
                            } else {
                                table.clear().draw();
                                toastr.success(response.message);
                                console.log('this is not error page');
                                $("#labour-report-modal").modal("hide");
                            }
                        },
                        error: function(xhr) {
                            console.log('Error callback triggered');
                            console.log(xhr);
                            var error = xhr.responseJSON.message;
                            $('#date').addClass('is-invalid').after(
                                '<div class="invalid-feedback">' + error + '</div>');
                        },
                        complete: function() {
                            $("#submit-btn").prop("disabled", false);
                        }
                    });
                    return false;
                }
            });

            $(document).on("click", ".edit-btn", function() {
                var edit_id = $(this).data('id');
                $("#edit_id").val(edit_id);
                $.ajax({
                    url: "{{ url('labour_report/fetch-edit') }}/" + edit_id,
                    dataType: "json",
                    success: function(response) {
                        $("#job_order").val(response.job_order).prop('readonly', true);
                        var labours = response.labour_id.split(',').map(function(item) {
                            return item.trim();
                        });
                        $("#labour").val(labours).trigger('change').prop('disabled', false);
                        $("#date").val(response.date).prop('readonly', true);
                        $("#description").val(response.desc).prop('disabled', false);
                        $("#modal-title-label").html('Edit Labour Report');
                        $("#labour-report-modal").modal("show");
                        $("#modal-footer-buttons").show();
                    },
                    error: function(code) {
                        alert(code.statusText);
                    },
                });
            });

            $(document).on("click", ".add-btn", function() {
                $("#edit_id").val("");
                $("#labour-report-form")[0].reset();
                $("#job_order").prop('disabled', false);
                $("#datepicker").prop('disabled', false);
                $("#labour").prop('disabled', false);
                $("#description").prop('disabled', false);
                $("#modal-title-label").html('Create Labour Report');
                $("#modal-footer-buttons").show();

                $("#date").val("").prop('readonly', false);

                $("#labour").prop('disabled', false);
                $("#labour").trigger('change');
            });

            $(document).on("click", ".export-btn", function() {
                var job_order = $('#job_orders').val();
                var date_range = $('#date_range').val();
                var export_url = "{{ url('labour_export') }}";
                if (date_range) {
                    export_url += "?date_range=" + date_range;
                }
                if (job_order) {
                    export_url += (date_range ? "&" : "?") + "job_order=" + job_order;
                }
                window.location.href = export_url;
            });


            $(document).on("click", ".delete-btn", function() {
                var edit_id = $(this).data('id');
                $("#edit_id").val(edit_id);
                $("#delete-confirm-text").text("Are you sure you want to delete this Labour Report?");
                $("#delete-confirm-modal").modal("show");
            });

            $(document).on("click", "#confirm-yes-btn", function() {
                var edit_id = $("#edit_id").val();
                $("#confirm-yes-btn").prop("disabled", true);
                $.ajax({
                    url: "{{ url('labour_report/delete') }}/" + edit_id,
                    method: "GET",
                    dataType: "json",
                    success: function(response) {
                        table.clear().draw();
                        $("#confirm-yes-btn").prop("disabled", false);
                        toastr.success(response.message);
                    },
                    error: function(code) {
                        alert(code.statusText);
                    },
                });
            });
        });
    </script>
@endsection
