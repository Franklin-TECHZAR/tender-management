<!DOCTYPE html>
<html>

<head>
    <!-- Basic Page Info -->
    <meta charset="utf-8" />
    <title>{{ env('APP_NAME') }} | Login</title>

    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet" />

    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="{{ url('theme/vendors/styles/core.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ url('theme/vendors/styles/icon-font.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ url('theme/vendors/styles/style.css') }}" />
	<style>
		.my-logo
		{
			height: 100% !important;
		}
		.logo-text
		{
			font-size:22px;
			font-family: 'dropways';
			font-weight: bolder;
            margin-left: 10px;
		}
	</style>
</head>

<body class="login-page">
    <div class="login-header box-shadow">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="brand-logo">
                <a href="{{ url('/') }}">
                    <img src="{{ url('images/logo-white.png') }}" class="my-logo" alt="" />
					<h2 class="logo-text">Alpha Power Electromechanical Projects</h2>
                </a>
            </div>
            {{-- <div class="login-menu">
                <ul>
                    <li><a href="register.html">Register</a></li>
                </ul>
            </div> --}}
        </div>
    </div>
    <div class="login-wrap d-flex align-items-center flex-wrap justify-content-center">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 col-lg-7">
                    <img src="{{ url('theme') }}/vendors/images/login-page-img.png" alt="" />
                </div>
                <div class="col-md-6 col-lg-5">
                    <div class="login-box bg-white box-shadow border-radius-10">
                        <div class="login-title">
                            <h2 class="text-center text-primary">Login</h2>
                        </div>
                        <form action="{{ route('login.submit') }}" method="POST">
                            @csrf
                            @if (count($errors) > 0)
                                <div class = "alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if ($message = Session::get('error'))
                                <div class="alert alert-danger alert-block">
                                    <button type="button" class="close" data-dismiss="alert">×</button>
                                    {{ $message }}
                                </div>
                            @endif
                            {{-- <div class="select-role">
                                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                    <label class="btn active">
                                        <input type="radio" name="options" id="admin" />
                                        <div class="icon">
                                            <img src="{{ url('theme') }}/vendors/images/briefcase.svg" class="svg"
                                                alt="" />
                                        </div>
                                        <span>I'm</span>
                                        Manager
                                    </label>
                                    <label class="btn">
                                        <input type="radio" name="options" id="user" />
                                        <div class="icon">
                                            <img src="{{ url('theme') }}/vendors/images/person.svg" class="svg"
                                                alt="" />
                                        </div>
                                        <span>I'm</span>
                                        Employee
                                    </label>
                                </div>
                            </div> --}}
                            <div class="input-group custom">
                                <input type="text" class="form-control form-control-lg" name="email"
                                    placeholder="Username" />
                                <div class="input-group-append custom">
                                    <span class="input-group-text"><i class="icon-copy dw dw-user1"></i></span>
                                </div>
                            </div>
                            <div class="input-group custom">
                                <input type="password" name="password" class="form-control form-control-lg"
                                    placeholder="**********" />
                                <div class="input-group-append custom">
                                    <span class="input-group-text"><i class="dw dw-padlock1"></i></span>
                                </div>
                            </div>
                            <div class="row pb-30">
                                <div class="col-6">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="remember"
                                            id="customCheck1" />
                                        <label class="custom-control-label" for="customCheck1">Remember</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <input class="btn btn-primary btn-lg btn-block" type="submit" value="Sign In">
                                </div>
                            </div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- js -->
    <script src="{{ url('theme/vendors/scripts/core.js') }}"></script>
    <script src="{{ url('theme/vendors/scripts/script.min.js') }}"></script>
    <script src="{{ url('theme/vendors/scripts/process.js') }}"></script>
    <script src="{{ url('theme/vendors/scripts/layout-settings.js') }}"></script>

</body>

</html>
