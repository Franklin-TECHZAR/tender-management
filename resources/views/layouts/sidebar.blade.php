<?php

$url_segments = request()->segments();

$dashboard = '';
$tender = '';

$labours = '';
$users = '';
$users_roles = '';

if (isset($url_segments[1]) && $url_segments[1] == 'dashboard') {
    $dashboard = 'active';
}


if (isset($url_segments[1]) && $url_segments[1] == 'users') {
    $users_roles = 'active';
    $users = 'active';
}

if (isset($url_segments[1]) && $url_segments[1] == 'labours') {
    $labours = 'active';
}

?>
<div class="left-side-bar">
    <div class="brand-logo">
        <a href="{{ url('admin') }}">
            <img src="{{ url('images/logo-white.png') }}" alt="" class="dark-logo" />
            <img src="{{ url('images/logo-white.png') }}" alt="" class="light-logo" />
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

                <li>
                    <a href="{{ url('tender') }}"
                        class="@if ($tender) active @endif dropdown-toggle no-arrow">
                        <span class="micon">
                            <i class="icon-copy fa fa-gavel" aria-hidden="true"></i>
                        </span><span class="mtext">Tender</span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('users') }}"
                        class="@if ($users) active @endif dropdown-toggle no-arrow">
                        <span class="micon">
                            <i class="icon-copy fa fa-address-card-o" aria-hidden="true"></i>
                        </span><span class="mtext">Users list</span>
                    </a>
                </li>

                <li class="dropdown @if ($users_roles) show @endif">
                    <a href="javascript:;" class="dropdown-toggle">
                        <svg xmlns="http://www.w3.org/2000/svg" style="margin-left: 5px" width="10" height="10"
                            fill="currentColor" class="bi bi-person-fill-gear micon" viewBox="0 0 22 22">
                            <path
                                d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0m-9 8c0 1 1 1 1 1h5.256A4.5 4.5 0 0 1 8 12.5a4.5 4.5 0 0 1 1.544-3.393Q8.844 9.002 8 9c-5 0-6 3-6 4m9.886-3.54c.18-.613 1.048-.613 1.229 0l.043.148a.64.64 0 0 0 .921.382l.136-.074c.561-.306 1.175.308.87.869l-.075.136a.64.64 0 0 0 .382.92l.149.045c.612.18.612 1.048 0 1.229l-.15.043a.64.64 0 0 0-.38.921l.074.136c.305.561-.309 1.175-.87.87l-.136-.075a.64.64 0 0 0-.92.382l-.045.149c-.18.612-1.048.612-1.229 0l-.043-.15a.64.64 0 0 0-.921-.38l-.136.074c-.561.305-1.175-.309-.87-.87l.075-.136a.64.64 0 0 0-.382-.92l-.148-.045c-.613-.18-.613-1.048 0-1.229l.148-.043a.64.64 0 0 0 .382-.921l-.074-.136c-.306-.561.308-1.175.869-.87l.136.075a.64.64 0 0 0 .92-.382zM14 12.5a1.5 1.5 0 1 0-3 0 1.5 1.5 0 0 0 3 0" />
                        </svg>
                        <span class="mtext">Users & Roles</span>
                    </a>
                    <ul class="submenu" style="display:@if ($users) block; @else none; @endif">
                        <li><a class="@if ($users) active @endif"
                                href="{{ url('users') }}">Users</a></li>
                    </ul>
                </li>
                <li class="dropdown @if ($labours) show @endif">
                    <a href="javascript:;" class="dropdown-toggle">
                        <span class="micon">
                            <i class="icon-copy fa fa-users" aria-hidden="true"></i>
                        </span><span class="mtext">Master</span>
                    </a>
                    <ul class="submenu" style="display:@if ($labours) block; @else none; @endif">
                        <li><a class="@if ($labours) active @endif" href="{{ url('labours') }}">Labour</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
