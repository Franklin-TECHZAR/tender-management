<?php

$url_segments = request()->segments();

$dashboard = '';
$tender = '';

$purchase = '';
$expenses = '';
$purchase_dept = '';
$vendor_payment = '';
$salaries = '';
$labour_report = '';
$return_balance = '';
$permissions = '';

$masters = '';
$materials = '';
$labours = '';
$expenses_type = '';
$purchase_type = '';
$vendors = '';
$balance_log = '';

$users = '';
$users_roles = '';
$roles = '';

$report = '';
$expenses_report = '';
$salaries_report = '';
$purchases_report = '';

if (isset($url_segments[0]) && $url_segments[0] == 'dashboard') {
    $dashboard = 'active';
}
if (isset($url_segments[0]) && $url_segments[0] == 'tender') {
    $tender = 'active';
}
if (isset($url_segments[0]) && $url_segments[0] == 'expenses') {
    $expenses = 'active';
}
if (isset($url_segments[0]) && $url_segments[0] == 'salaries') {
    $salaries = 'active';
}
if (isset($url_segments[0]) && $url_segments[0] == 'purchase_dept') {
    $purchase_dept = 'active';
}
if (isset($url_segments[0]) && $url_segments[0] == 'vendor_payment') {
    $vendor_payment = 'active';
}
if (isset($url_segments[0]) && $url_segments[0] == 'purchase') {
    $purchase = 'active';
}
if (isset($url_segments[0]) && $url_segments[0] == 'labour_report') {
    $labour_report = 'active';
}
if (isset($url_segments[0]) && $url_segments[0] == 'return_balance') {
    $return_balance = 'active';
}

if (isset($url_segments[0]) && $url_segments[0] == 'users') {
    $users_roles = 'active';
    $users = 'active';
}
if (isset($url_segments[0]) && $url_segments[0] == 'roles') {
    $users_roles = 'active';
    $roles = 'active';
}
if (isset($url_segments[0]) && $url_segments[0] == 'permissions') {
    $users_roles = 'active';
    $permissions = 'active';
}
if (isset($url_segments[0]) && $url_segments[0] == 'balance_log') {
    $balance_log = 'active';
}
if (isset($url_segments[0]) && $url_segments[0] == 'labours') {
    $masters = 'active';
    $labours = 'active';
}

if (isset($url_segments[0]) && $url_segments[0] == 'materials') {
    $masters = 'active';
    $materials = 'active';
}

if (isset($url_segments[0]) && $url_segments[0] == 'vendors') {
    $masters = 'active';
    $vendors = 'active';
}

if (isset($url_segments[0]) && $url_segments[0] == 'expenses_type') {
    $masters = 'active';
    $expenses_type = 'active';
}

if (isset($url_segments[0]) && $url_segments[0] == 'purchase_type') {
    $masters = 'active';
    $purchase_type = 'active';
}

if (isset($url_segments[0]) && $url_segments[0] == 'salaries_report') {
    $report = 'active';
    $salaries_report = 'active';
}

if (isset($url_segments[0]) && $url_segments[0] == 'purchases_report') {
    $report = 'active';
    $purchases_report = 'active';
}

if (isset($url_segments[0]) && $url_segments[0] == 'expenses_report') {
    $report = 'active';
    $expenses_report = 'active';
}

if (isset($url_segments[0]) && $url_segments[0] == 'report') {
    $report = 'active';
}

?>
<style>
    .my-brand-logo {
        background: #fff;
        text-align: center;
        margin: 10px;
        border-radius: 10px;
        padding: 10px;
    }

    .my-logo {
        width: 150px !important;
    }

    .my-logo-text {
        font-size: 18px;
        margin-left: 10px;
        text-align: center;
        margin-top: 5px;
        text-shadow:
            1px 1px 2px black,
            0 0 1em blue,
            0 0 0.2em blue;
        color: white;
        font-family: Georgia,
            serif;
    }
</style>
<div class="left-side-bar">
    <div class="my-brand-logo">
        <a href="{{ url('admin') }}">
            <img src="{{ url('images/logo-white.png') }}" class="my-logo" alt="" />
            <br>
            <h4 class="my-logo-text">Alpha Power Projects</h4>
        </a>
        <div class="close-sidebar" data-toggle="left-sidebar-close">
            <i class="ion-close-round"></i>
        </div>
    </div>
    <div class="menu-block customscroll">
        <div class="sidebar-menu">
            <ul id="accordion-menu">
                <li>
                    <a href="{{ url('dashboard') }}"
                        class="@if ($dashboard) active @endif dropdown-toggle no-arrow">
                        <span class="micon bi bi-speedometer"></span><span class="mtext">Dashboard</span>
                    </a>
                </li>
                {{-- Debug user's permissions --}}
                {{-- {{ dd(Auth::user()->getAllPermissions()->pluck('name')) }} --}}

                {{-- Debug user's roles --}}
                {{-- {{ dd(Auth::user()->roles()->pluck('name')) }} --}}

                {{-- {{dd(Auth::user()->role_id);}} --}}
                {{-- @php
                    // Auth::user()->assignRole('Account Dept');
                    //   Auth::user()->givePermissionTo('tender-view');
                @endphp --}}

                {{-- {{ dd(Auth::user()->hasPermissionTo('tender-view')) }} --}}
                {{-- {{ dd(Auth::user()->getAllPermissions()) }} --}}
                {{-- {{ dd(Auth::user()->hasAnyPermission('tender-view')) }} --}}
                {{-- {{ dd(Auth::user()->getAllPermissions()->pluck('name')) }} --}}
                {{-- {{ dd(Auth::user()->getAllPermissions()->pluck('name')) }} --}}
                @if (Auth::user()->role_id == 1 && Auth::user()->hasPermissionTo('tender-view'))
                    <li>
                        <a href="{{ url('tender') }}?show=New"
                            class="@if ($tender) active @endif dropdown-toggle no-arrow">
                            <span class="micon">
                                <i class="icon-copy fa fa-gavel" aria-hidden="true"></i>
                            </span><span class="mtext">Tender</span>
                        </a>
                    </li>
                @endif

                @if (Auth::user()->role_id == 1 && Auth::user()->hasPermissionTo('purchase-view'))
                    <li>
                        <a href="{{ url('purchase') }}"
                            class="@if ($purchase) active @endif dropdown-toggle no-arrow">
                            <span class="micon">
                                <i class="icon-copy bi bi-cart" aria-hidden="true"></i>
                            </span><span class="mtext">Purchase</span>
                        </a>
                    </li>
                @endif

                @if (Auth::user()->role_id == 1 && Auth::user()->hasPermissionTo('expenses-view'))
                    <li>
                        <a href="{{ url('expenses') }}"
                            class="@if ($expenses) active @endif dropdown-toggle no-arrow">
                            <span class="micon">
                                <i class="icon-copy fa fa-money" aria-hidden="true"></i>
                            </span><span class="mtext">Expenses</span>
                        </a>
                    </li>
                @endif

                @if (Auth::user()->role_id == 1 && Auth::user()->hasPermissionTo('salaries-view'))
                    <li>
                        <a href="{{ url('salaries') }}"
                            class="@if ($salaries) active @endif dropdown-toggle no-arrow">
                            <span class="micon">
                                <i class="icon-copy fa fa-dollar" aria-hidden="true"></i>
                            </span><span class="mtext">Salary</span>
                        </a>
                    </li>
                @endif

                @if (Auth::user()->role_id == 1 && Auth::user()->hasPermissionTo('labour_report-view'))
                    <li>
                        <a href="{{ url('labour_report') }}"
                            class="@if ($labour_report) active @endif dropdown-toggle no-arrow">
                            <span class="micon">
                                <i class="icon-copy fa fa-calendar" aria-hidden="true"></i>
                            </span><span class="mtext">Daily Work Report</span>
                        </a>
                    </li>
                @endif

                @if (Auth::user()->role_id == 1 && Auth::user()->hasPermissionTo('balance_log-view'))
                    <li>
                        <a href="{{ url('balance_log') }}"
                            class="@if ($balance_log) active @endif dropdown-toggle no-arrow">
                            <span class="micon">
                                <i class="icon-copy fa fa-balance-scale" aria-hidden="true"></i>
                            </span><span class="mtext">Balance Log</span>
                        </a>
                    </li>
                @endif


                @if (Auth::user()->role_id == 1 && Auth::user()->hasPermissionTo('return_balance-view'))
                    <li>
                        <a href="{{ url('return_balance') }}"
                            class="@if ($return_balance) active @endif dropdown-toggle no-arrow">
                            <span class="micon">
                                <i class="icon-copy fa fa-undo" aria-hidden="true"></i>
                            </span><span class="mtext">Return Balance</span>
                        </a>
                    </li>
                @endif

                @if (Auth::user()->role_id == 1 && Auth::user()->hasPermissionTo('purchase_dept-view'))
                    <li>
                        <a href="{{ url('purchase_dept') }}"
                            class="@if ($purchase_dept) active @endif dropdown-toggle no-arrow">
                            <span class="micon">
                                <i class="icon-copy bi bi-credit-card" aria-hidden="true"></i>
                            </span><span class="mtext">Purchase Dept</span>
                        </a>
                    </li>
                @endif

                @if (Auth::user()->role_id == 1 && Auth::user()->hasPermissionTo('vendor_payment-view'))
                    <li>
                        <a href="{{ url('vendor_payment') }}"
                            class="@if ($vendor_payment) active @endif dropdown-toggle no-arrow">
                            <span class="micon">
                                <i class="icon-copy bi bi-cash" aria-hidden="true"></i>
                            </span><span class="mtext">Vendor Payment</span>
                        </a>
                    </li>
                @endif

                @if (Auth::user()->role_id == 1 ||
                        Auth::user()->hasPermissionTo('report-view') ||
                        Auth::user()->hasPermissionTo('salary_report-view') ||
                        Auth::user()->hasPermissionTo('expense_report-view') ||
                        Auth::user()->hasPermissionTo('purchase_report-view'))

                    @if (Auth::user()->hasPermissionTo('report-view'))
                        <li class="dropdown @if ($report) show @endif">
                            <a href="javascript:;" class="dropdown-toggle">
                                <span class="micon">
                                    <i class="icon-copy fa fa-file-text" aria-hidden="true"></i>
                                </span>
                                <span class="mtext">Report</span>
                            </a>

                            @if (Auth::user()->hasPermissionTo('salary_report-view'))
                                <ul class="submenu"
                                    style="display:@if ($report) block; @else none; @endif">
                                    <li><a class="@if ($salaries_report) active @endif"
                                            href="{{ url('salaries_report') }}">Salary Report</a></li>
                                </ul>
                            @endif

                            @if (Auth::user()->hasPermissionTo('expense_report-view'))
                                <ul class="submenu"
                                    style="display:@if ($report) block; @else none; @endif">
                                    <li><a class="@if ($expenses_report) active @endif"
                                            href="{{ url('expenses_report') }}">Expense Report</a></li>
                                </ul>
                            @endif

                            @if (Auth::user()->hasPermissionTo('purchase_report-view'))
                                <ul class="submenu"
                                    style="display:@if ($report) block; @else none; @endif">
                                    <li><a class="@if ($purchases_report) active @endif"
                                            href="{{ url('purchases_report') }}">Purchase Report</a></li>
                                </ul>
                            @endif
                        </li>
                    @endif
                @endif

                @if (Auth::user()->role_id == 1 ||
                        Auth::user()->hasPermissionTo('users-roles-view') ||
                        Auth::user()->hasPermissionTo('users-view') ||
                        Auth::user()->hasPermissionTo('permissions-view') ||
                        Auth::user()->hasPermissionTo('roles-view'))
                    @if (Auth::user()->hasPermissionTo('users-roles-view'))
                        <li class="dropdown @if ($users_roles) show @endif">
                            <a href="javascript:;" class="dropdown-toggle">
                                <svg xmlns="http://www.w3.org/2000/svg" style="margin-left: 5px" width="10"
                                    height="10" fill="currentColor" class="bi bi-person-fill-gear micon"
                                    viewBox="0 0 22 22">
                                    <path
                                        d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-9 8c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4m9.886-3.54c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.045c-.613-.18-.613-1.048 0-1.229l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382zM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0" />
                                </svg>
                                <span class="mtext">Users & Roles</span>
                            </a>
                            @if (Auth::user()->hasPermissionTo('users-view'))
                                <ul class="submenu"
                                    style="display:@if ($users_roles) block; @else none; @endif">
                                    <li><a class="@if ($users) active @endif"
                                            href="{{ url('users') }}">Users</a></li>
                                </ul>
                            @endif
                            @if (Auth::user()->hasPermissionTo('permissions-view'))
                                <ul class="submenu"
                                    style="display:@if ($users_roles) block; @else none; @endif">
                                    <li><a class="@if ($permissions) active @endif"
                                            href="{{ url('permissions') }}">Permissions</a></li>
                                </ul>
                            @endif
                            @if (Auth::user()->hasPermissionTo('roles-view'))
                                <ul class="submenu"
                                    style="display:@if ($users_roles) block; @else none; @endif">
                                    <li><a class="@if ($roles) active @endif"
                                            href="{{ url('roles') }}">Roles</a></li>
                                </ul>
                            @endif
                        </li>
                    @endif
                @endif


                @if (Auth::user()->role_id == 1 ||
                        Auth::user()->hasPermissionTo('master-view') ||
                        Auth::user()->hasPermissionTo('labours-view') ||
                        Auth::user()->hasPermissionTo('materials-view') ||
                        Auth::user()->hasPermissionTo('vendors/dealer-view') ||
                        Auth::user()->hasPermissionTo('expenses_type-view') ||
                        Auth::user()->hasPermissionTo('purchase_type-view'))
                    @if (Auth::user()->hasPermissionTo('master-view'))
                        <li class="dropdown @if ($masters) show @endif">
                            <a href="javascript:;" class="dropdown-toggle">
                                <span class="micon">
                                    <i class="icon-copy fa fa-users" aria-hidden="true"></i>
                                </span>
                                <span class="mtext">Masters</span>
                            </a>
                            @if (Auth::user()->hasPermissionTo('labours-view'))
                                <ul class="submenu"
                                    style="display:@if ($masters) block; @else none; @endif">
                                    <li><a class="@if ($labours) active @endif"
                                            href="{{ url('labours') }}">Labours</a></li>
                                </ul>
                            @endif
                            @if (Auth::user()->hasPermissionTo('materials-view'))
                                <ul class="submenu"
                                    style="display:@if ($masters) block; @else none; @endif">
                                    <li><a class="@if ($materials) active @endif"
                                            href="{{ url('materials') }}">Material</a></li>
                                </ul>
                            @endif
                            @if (Auth::user()->hasPermissionTo('vendors/dealer-view'))
                                <ul class="submenu"
                                    style="display:@if ($masters) block; @else none; @endif">
                                    <li><a class="@if ($vendors) active @endif"
                                            href="{{ url('vendors') }}">Vendors / Dealer</a></li>
                                </ul>
                            @endif
                            @if (Auth::user()->hasPermissionTo('expenses_type-view'))
                                <ul class="submenu"
                                    style="display:@if ($masters) block; @else none; @endif">
                                    <li><a class="@if ($expenses_type) active @endif"
                                            href="{{ url('expenses_type') }}">Expenses Type</a></li>
                                </ul>
                            @endif
                            @if (Auth::user()->hasPermissionTo('purchase_type-view'))
                                <ul class="submenu"
                                    style="display:@if ($masters) block; @else none; @endif">
                                    <li><a class="@if ($purchase_type) active @endif"
                                            href="{{ url('purchase_type') }}">Purchase Type</a></li>
                                </ul>
                            @endif
                        </li>
                    @endif
                @endif
            </ul>
        </div>
    </div>
</div>
