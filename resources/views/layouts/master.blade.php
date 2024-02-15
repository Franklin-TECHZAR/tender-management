<!DOCTYPE html>
<html>

<head>
    <!-- Basic Page Info -->
    <meta charset="utf-8" />
    <title>@yield('title')</title>

    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="{{ url('theme/vendors/styles/core.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ url('theme/vendors/styles/icon-font.min.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="{{ url('theme/src/plugins/jvectormap/jquery-jvectormap-2.0.3.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ url('theme/vendors/styles/style.css') }}" />


    <link rel="stylesheet" type="text/css"
        href="{{ url('theme/src/plugins/datatables/css/dataTables.bootstrap4.min.css') }}" />
    <link rel="stylesheet" type="text/css"
        href="{{ url('theme/src/plugins/datatables/css/responsive.bootstrap4.min.css') }}" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <style>
        .error {
            color: red;
        }

        .border-radius-100,
        .user-info-dropdown .dropdown-toggle .user-icon img {
            height: 100%;
            object-fit: cover;
        }

        th,
        td {
            white-space: nowrap;
        }

        .form-group {
            margin-bottom: 5px;
        }

        .custom-file-input,
        .custom-file-label,
        .custom-select,
        .form-control {
            height: 40px;
        }

        textarea.form-control {
            height: 100px;
        }

        .filter-btn {
            margin-top: 1.8rem !important;
            height: 41px;
        }
    </style>
    <style>
        .error {
            color: red;
        }

        .border-radius-100,
        .user-info-dropdown .dropdown-toggle .user-icon img {
            height: 100%;
            object-fit: cover;
        }

        th,
        td {
            white-space: nowrap;
        }

        .left-side-bar {
            background: #008000;;
            /* #142127 */
        }

        .btn-primary,
        .btn-primary:hover {
            background-color: #008000;
            border-color: #008000;
        }

        .breadcrumb-item.active {
            color: #008000;
        }

        .btn-link {
            color: #008000;
        }

        .page-item.active .page-link {
            background-color: #008000;
            border-color: #008000;
        }

        .custom-checkbox .custom-control-input:checked~.custom-control-label::before {
            background-color: #008000;
        }

        .custom-control-input:checked~.custom-control-label::before {
            border-color: #008000;
            background-color: #008000;
        }

        .custom-control-label::before {
            border: 2px solid #008000;
        }

        .nav-tabs.customtab .nav-item.show .nav-link,
        .nav-tabs.customtab .nav-link.active {
            color: #008000;
            border-bottom: 2px solid #008000;
        }

        .nav-tabs.customtab .nav-link:focus,
        .nav-tabs.customtab .nav-link:hover {
            color: #008000;
            border-bottom: 2px solid #008000;
        }

        .dropdown-item:focus,
        .dropdown-item:hover {
            color: #008000;
        }
        .text-primary {
            color: #008000!important;
        }
    </style>
</head>

<body>
    {{-- <div class="pre-loader">
        <div class="pre-loader-box">
            <div class="loader-logo">
                <img src="{{ url('images/logo-white.png') }}" alt="" />
            </div>
            <div class="loader-progress" id="progress_div">
                <div class="bar" id="bar1"></div>
            </div>
            <div class="percent" id="percent1">0%</div>
            <div class="loading-text">Loading...</div>
        </div>
    </div> --}}

    <div class="header">
        <div class="header-left">
            <div class="menu-icon bi bi-list"></div>
            <div class="search-toggle-icon bi bi-search" data-toggle="header_search"></div>
            <div class="header-search">
                <form>
                    <div class="form-group mb-0">
                        <i class="dw dw-search2 search-icon"></i>
                        <input type="text" class="form-control search-input" placeholder="Search Here" />
                        {{-- <div class="dropdown">
                            <a class="dropdown-toggle no-arrow" href="#" role="button" data-toggle="dropdown">
                                <i class="ion-arrow-down-c"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div class="form-group row">
                                    <label class="col-sm-12 col-md-2 col-form-label">From</label>
                                    <div class="col-sm-12 col-md-10">
                                        <input class="form-control form-control-sm form-control-line" type="text" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-12 col-md-2 col-form-label">To</label>
                                    <div class="col-sm-12 col-md-10">
                                        <input class="form-control form-control-sm form-control-line" type="text" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-sm-12 col-md-2 col-form-label">Subject</label>
                                    <div class="col-sm-12 col-md-10">
                                        <input class="form-control form-control-sm form-control-line" type="text" />
                                    </div>
                                </div>
                                <div class="text-right">
                                    <button class="btn btn-primary">Search</button>
                                </div>
                            </div>
                        </div> --}}
                    </div>
                </form>
            </div>
        </div>
        <div class="header-right">

            <div class="user-info-dropdown">
                <div class="dropdown">
                    <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                        <span class="user-icon">
                            @if (auth()->user()->profile)
                                <img src="{{ url('uploads/profile_photo') }}/{{ auth()->user()->profile }}"
                                    alt="" />
                            @else
                                <img src="{{ url('images/user-icon.jpeg') }}" alt="" />
                            @endif
                        </span>
                        <span class="user-name">{{ auth()->user()->name }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                        <a class="dropdown-item" href="{{ url('profile') }}"><i class="dw dw-user1"></i>
                            Profile</a>
                        <a class="dropdown-item" href="#"
                            onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();"><i
                                class="dw dw-logout"></i> Log Out</a>
                    </div>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.sidebar')

    <div class="mobile-menu-overlay"></div>


    @yield('content')

    <div class="modal fade" id="delete-confirm-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center font-18">
                    <h4 class="padding-top-30 mb-30 weight-500" id="delete-confirm-text">
                        Are you sure you want to continue?
                    </h4>
                    <div class="padding-bottom-30 text-center">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fa fa-times"></i> NO
                        </button>
                        <button type="button" id="confirm-yes-btn" class="btn btn-primary" data-dismiss="modal">
                            <i class="fa fa-check"></i> YES
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="footer-wrap pd-20 mb-20 card-box">
                Copyright 2023 -
                <a href="#" target="_blank">Tender Management System</a>
            </div>
        </div>
    </div> --}}

    <!-- js -->
    <script src="{{ url('theme/vendors/scripts/core.js') }}"></script>
    <script src="{{ url('theme/vendors/scripts/script.min.js') }}"></script>
    <script src="{{ url('theme/vendors/scripts/process.js') }}"></script>
    <script src="{{ url('theme/vendors/scripts/layout-settings.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

    <script src="{{ url('theme/src/plugins/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ url('theme/src/plugins/datatables/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ url('theme/src/plugins/datatables/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ url('theme/src/plugins/datatables/js/responsive.bootstrap4.min.js') }}"></script>

    {{-- toastr --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    @yield('addscript')
</body>

</html>
