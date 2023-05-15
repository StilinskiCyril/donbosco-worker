<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="icon" href="{{ asset('ui-kit/images/logo.png') }}" type="image/png" />
    <!--plugins-->
    <link href="{{ asset('ui-kit/plugins/simplebar/css/simplebar.css') }}" rel="stylesheet" />
    <link href="{{ asset('ui-kit/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet" />
    <link href="{{ asset('ui-kit/plugins/metismenu/css/metisMenu.min.css') }}" rel="stylesheet" />
    <!-- loader-->
    <link href="{{ asset('ui-kit/css/pace.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('ui-kit/js/pace.min.js') }}"></script>
    <!-- Bootstrap CSS -->
    <link href="{{ asset('ui-kit/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&amp;display=swap" rel="stylesheet">
    <link href="{{ asset('ui-kit/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('ui-kit/css/icons.css') }}" rel="stylesheet">
    <title>{{ env('APP_NAME') }} | Register</title>
</head>

<body class="">
<!--wrapper-->
<div class="wrapper">
    <div class="section-authentication-signin d-flex align-items-center justify-content-center my-5 my-lg-0">
        <div class="container-fluid">
            <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3">
                <div class="col mx-auto">
                    <div class="mb-4 text-center">
                        <a href="{{ route('home.landing-page') }}">
                            <img src="{{ asset('ui-kit/images/logo.png') }}" width="180" alt="Logo" />
                        </a>
                    </div>
                    <div class="card shadow-none">
                        <div class="card-body">
                            <div class="border p-4 rounded">
                                <div class="form-body">
                                    <form action="{{ route('register') }}" method="post" class="row g-3">
                                        @csrf
                                        <div class="col-12">
                                            <label for="inputName" class="form-label">Enter Name</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" id="inputName" required>
                                            @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <label for="inputMsisdn" class="form-label">Enter Msisdn/ Phone</label>
                                            <input type="text" class="form-control @error('msisdn') is-invalid @enderror" name="msisdn" value="{{ old('msisdn') }}" id="inputMsisdn" required>
                                            @error('msisdn')
                                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <label for="inputEmailAddress" class="form-label">Enter Email Address</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" id="inputEmailAddress" required>
                                            @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                        <div class="col-12">
                                            <label for="inputChoosePassword" class="form-label">Enter Password</label>
                                            <div class="input-group" id="show_hide_password">
                                                <input type="password" class="form-control border-end-0 @error('password') is-invalid @enderror" name="password" id="inputChoosePassword" required> <a href="javascript:;" class="input-group-text bg-transparent"><i class='bx bx-hide'></i></a>
                                                @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label for="inputConfirmPassword" class="form-label">Confirm Password</label>
                                            <div class="input-group" id="show_hide_password">
                                                <input type="password" class="form-control border-end-0" name="password_confirmation" id="inputConfirmPassword" required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="d-grid">
                                                <button type="submit" class="btn btn-primary"><i class="bx bxs-lock-open"></i>Sign Up</button>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <a href="{{ route('login') }}"><p class="text-success text-center">Already Registered? Click Here To Sign In</p></a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--end row-->
        </div>
    </div>
</div>
<!--end wrapper-->
<!-- Bootstrap JS -->
<script src="{{ asset('ui-kit/js/bootstrap.bundle.min.js') }}"></script>
<!--plugins-->
<script src="{{ asset('ui-kit/js/jquery.min.js') }}"></script>
<script src="{{ asset('ui-kit/plugins/simplebar/js/simplebar.min.js') }}"></script>
<script src="{{ asset('ui-kit/plugins/metismenu/js/metisMenu.min.js') }}"></script>
<script src="{{ asset('ui-kit/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
<!--Password show & hide js -->
<script>
    $(document).ready(function () {
        $("#show_hide_password a").on('click', function (event) {
            event.preventDefault();
            if ($('#show_hide_password input').attr("type") == "text") {
                $('#show_hide_password input').attr('type', 'password');
                $('#show_hide_password i').addClass("bx-hide");
                $('#show_hide_password i').removeClass("bx-show");
            } else if ($('#show_hide_password input').attr("type") == "password") {
                $('#show_hide_password input').attr('type', 'text');
                $('#show_hide_password i').removeClass("bx-hide");
                $('#show_hide_password i').addClass("bx-show");
            }
        });
    });
</script>
<!--app JS-->
<script src="{{ asset('ui-kit/js/app.js') }}"></script>
</body>


</html>
