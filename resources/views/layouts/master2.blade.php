<!DOCTYPE html>
<html  lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="generator" content="MSSC">
    <meta name="twitter:card" content="summary_large_image"/>
    <meta name="twitter:image:src" content="assets/images/index-meta.jpeg">
    <meta property="og:image" content="assets/images/index-meta.jpeg">
    <meta name="twitter:title" content="Marian Shrine Spirituality Center">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1">
    <link rel="shortcut icon" href="{{ asset('ui-kit2/assets/images/logo-500x124.png') }}" type="image/x-icon">
    <meta name="description" content="Marian Shrine Spirituality Center">

    <!--favicon-->
    <link rel="icon" href="{{ asset('ui-kit/images/logo.png') }}" type="image/png" />



    <title>@yield('title', 'Marian Shrine Spirituality Center')</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    {{--    <link rel="stylesheet" href="{{ asset('ui-kit2/assets/tether/tether.min.css') }}">--}}
    {{--    <link rel="stylesheet" href="{{ asset('ui-kit2/assets/bootstrap/css/bootstrap.min.css') }}">--}}
    {{--    <link rel="stylesheet" href="{{ asset('ui-kit2/assets/bootstrap/css/bootstrap-grid.min.css') }}">--}}
    {{--    <link rel="stylesheet" href="{{ asset('ui-kit2/assets/bootstrap/css/bootstrap-reboot.min.css') }}">--}}
    {{--    <link rel="stylesheet" href="{{ asset('ui-kit2/assets/formstyler/jquery.formstyler.css') }}">--}}
    {{--    <link rel="stylesheet" href="{{ asset('ui-kit2/assets/formstyler/jquery.formstyler.theme.css') }}">--}}
    {{--    <link rel="stylesheet" href="{{ asset('ui-kit2/assets/datepicker/jquery.datetimepicker.min.css') }}">--}}
    <link rel="stylesheet" href="{{ asset('ui-kit2/assets/dropdown/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('ui-kit2/assets/socicon/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('ui-kit2/assets/theme/css/style.css') }}">
    <link rel="preload" href="https://fonts.googleapis.com/css?family=Jost:100,200,300,400,500,600,700,800,900,100i,200i,300i,400i,500i,600i,700i,800i,900i&display=swap" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Jost:100,200,300,400,500,600,700,800,900,100i,200i,300i,400i,500i,600i,700i,800i,900i&display=swap"></noscript>
    <link rel="preload" as="style" href="{{ asset('ui-kit2/assets/mobirise/css/mbr-additional.css') }}"><link rel="stylesheet" href="{{ asset('ui-kit2/assets/mobirise/css/mbr-additional.css') }}" type="text/css">

    <script>
        function ToggleMenu(value){
            if (value == 2){
                $('#day_of_the_week').removeClass('hide')
                $('#date').addClass('hide')
            } else if (value == 3){
                $('#date').removeClass('hide')
                $('#day_of_the_week').addClass('hide')
            } else {
                $('#date').addClass('hide')
                $('#day_of_the_week').addClass('hide')
            }
        }
    </script>
    <style>
        .hide{
            display: none;
        }
    </style>

    {{--    <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>--}}

</head>
<body>
<section class="menu cid-s48OLK6784" nonce="menu" id="menu1-h">
    <nav class="navbar navbar-dropdown navbar-fixed-top navbar-expand-lg">
        <div class="container">
            <div class="navbar-brand">
                <span class="navbar-logo">
                <img src="{{ asset('ui-kit2/assets/images/logo-500x124.png') }}" alt="" style="height: 4.1rem;">
                </span>
            </div>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <div class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav nav-dropdown nav-right" data-app-modern-menu="true">
                    <li class="nav-item"><a class="nav-link link text-black display-4" href="{{ route('home.landing') }}">Apply For a Pledge</a></li>
                    <li class="nav-item"><a class="nav-link link text-black display-4" href="">Donate Now</a></li>
                    @auth()
                    <li class="nav-item"><a class="nav-link link text-black display-4" href="{{ route('home.dashboard') }}">Dashboard</a></li>
                    @endauth
                    <!-- Authentication Links -->
                    @guest()
                        <li class="nav-item"><a class="nav-link link text-black display-4" href="{{ route('login') }}">Login</a></li>
                    @else
                        <li class="nav-item"><a class="nav-link link text-black display-4" href="{{ route('logout') }}">Logout ({{ Auth::user()->name }})</a></li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
</section>


<section class="header13 cid-syzTSBLp6k mbr-parallax-background" id="header13-n">
    <div class="mbr-overlay" style="opacity: 0.6; background-color: rgb(34, 165, 229);"></div>
    <div class="align-center container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">
                <h1 class="mbr-section-title mbr-fonts-style mb-3 display-1"><strong>Give a Hand</strong></h1>
                <h2 class="mbr-section-subtitle mbr-fonts-style mb-3 display-2">Make the world a better Place</h2>
            </div>
        </div>
    </div>
</section>


@yield('content')

<section class="image2 cid-syzXC7iqk7" id="image2-r">

    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 col-lg-5">
                <div class="image-wrapper">
                    <img src="{{ asset('ui-kit2/assets/images/icons-circle-492x434.png') }}" alt="MSSC">
                    <p class="mbr-description mbr-fonts-style mt-2 align-center display-4">Donate Today</p>
                </div>
            </div>
            <div class="col-12 col-lg">
                <div class="text-wrapper">
                    <h3 class="mbr-section-title mbr-fonts-style mb-3 display-5"><strong>Help Us Achieve our</strong><br><strong>Land Purchase Project.</strong><br><strong>We target to raise 0.4B</strong></h3>
                    <p class="mbr-text mbr-fonts-style display-7">We seek funds to purchase land for buildings. <br><br>You are the special type of person that changes lives, lifts people up, and makes the world a better place. Thank you for your donation and your association with our cause.</p>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="footer3 cid-s48P1Icc8J mbr-reveal" nonce="footers" id="footer3-i">
    <div class="container">
        <div class="media-container-row align-center mbr-white">
            <div class="row row-copirayt">
                <p class="mbr-text mb-0 mbr-fonts-style mbr-white align-center display-7"> © Copyright {{ now()->year }} MSSC | All Rights Reserved | Powered By
                    <a href="https://bitwise.co.ke" target="_blank">Bitwise Digital Solutions</a>
                </p>
            </div>
        </div>
    </div>
</section>

</body>
</html>
