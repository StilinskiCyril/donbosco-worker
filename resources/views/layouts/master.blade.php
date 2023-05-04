<!doctype html>
<html lang="en">


<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="icon" href="{{ asset('ui-kit/images/logo.png') }}" type="image/png" />
    <!--plugins-->
    <link href="{{ asset('ui-kit/plugins/vectormap/jquery-jvectormap-2.0.2.css') }}" rel="stylesheet"/>
    <link href="{{ asset('ui-kit/plugins/simplebar/css/simplebar.css') }}" rel="stylesheet" />
    <link href="{{ asset('ui-kit/plugins/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet" />
    <link href="{{ asset('ui-kit/plugins/metismenu/css/metisMenu.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('ui-kit/plugins/datatable/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('ui-kit/plugins/highcharts/css/highcharts.css') }}" rel="stylesheet" />
    <!-- loader-->
    <link href="{{ asset('ui-kit/css/pace.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('ui-kit/js/pace.min.js') }}"></script>
    <!-- Bootstrap CSS -->
    <link href="{{ asset('ui-kit/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&amp;display=swap" rel="stylesheet">
    <link href="{{ asset('ui-kit/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('ui-kit/css/icons.css') }}" rel="stylesheet">
    <!-- Theme Style CSS -->
    <link rel="stylesheet" href="{{ asset('ui-kit/css/dark-theme.css') }}" />
    <link rel="stylesheet" href="{{ asset('ui-kit/css/semi-dark.css') }}" />
    <link rel="stylesheet" href="{{ asset('ui-kit/css/header-colors.css') }}" />

    <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

    <title>@yield('title', 'Home')</title>

</head>

<body>
<!--wrapper-->
<div class="wrapper">
    <!--sidebar wrapper -->
    @include('layouts.sidebar')
    <!--end sidebar wrapper -->

    <!--start topbar -->
    @include('layouts.topbar')
    <!--end topbar -->

    <!--start page wrapper -->
    @yield('content')
    <!--end page wrapper -->

    <!--start overlay-->
    <div class="overlay toggle-icon"></div>
    <!--end overlay-->
    <!--Start Back To Top Button--> <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
    <!--End Back To Top Button-->

    @include('layouts.footer')

</div>
<!--end wrapper-->

<!-- Bootstrap JS -->
<script src="{{ asset('ui-kit/js/bootstrap.bundle.min.js') }}"></script>
<!--plugins-->
<script src="{{ asset('ui-kit/js/jquery.min.js') }}"></script>
<script src="{{ asset('ui-kit/plugins/simplebar/js/simplebar.min.js') }}"></script>
<script src="{{ asset('ui-kit/plugins/metismenu/js/metisMenu.min.js') }}"></script>
<script src="{{ asset('ui-kit/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('ui-kit/plugins/vectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
<script src="{{ asset('ui-kit/plugins/vectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
<script src="{{ asset('ui-kit/plugins/chartjs/js/Chart.min.js') }}"></script>
<script src="{{ asset('ui-kit/plugins/chartjs/js/Chart.extension.js') }}"></script>
<script src="{{ asset('ui-kit/plugins/sparkline-charts/jquery.sparkline.min.js') }}"></script>

<!--Morris JavaScript -->
<script src="{{ asset('ui-kit/plugins/raphael/raphael-min.js') }}"></script>
<script src="{{ asset('ui-kit/plugins/morris/js/morris.js') }}"></script>
<script src="{{ asset('ui-kit/js/index.js') }}"></script>

<!--app JS-->
<script src="{{ asset('ui-kit/js/app.js') }}"></script>

<script src="{{ asset('ui-kit/js/index2.js') }}"></script>
<script src="{{ asset('ui-kit/plugins/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('ui-kit/plugins/datatable/js/dataTables.bootstrap5.min.js') }}"></script>

<!-- highcharts js -->
<script src="{{ asset('ui-kit/plugins/highcharts/js/highcharts.js') }}"></script>
<script src="{{ asset('ui-kit/plugins/highcharts/js/highcharts-more.js') }}"></script>
<script src="{{ asset('ui-kit/plugins/highcharts/js/variable-pie.js') }}"></script>
<script src="{{ asset('ui-kit/plugins/highcharts/js/solid-gauge.js') }}"></script>
<script src="{{ asset('ui-kit/plugins/highcharts/js/highcharts-3d.js') }}"></script>
<script src="{{ asset('ui-kit/plugins/highcharts/js/cylinder.js') }}"></script>
<script src="{{ asset('ui-kit/plugins/highcharts/js/funnel3d.js') }}"></script>
<script src="{{ asset('ui-kit/plugins/highcharts/js/exporting.js') }}"></script>
<script src="{{ asset('ui-kit/plugins/highcharts/js/export-data.js') }}"></script>
<script src="{{ asset('ui-kit/plugins/highcharts/js/accessibility.js') }}"></script>
<script src="{{ asset('ui-kit/plugins/highcharts/js/highcharts-custom.script.js') }}"></script>

</body>


</html>
