<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}"><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Hezecom: Laravel Project and Admin Maker</title>
    <link rel="shortcut icon" type="image/png" sizes="16x16" href="{{ asset('public/templates/admin/images/favicon.png') }}" />
    <link href="{{ asset('public/templates/frontend/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('public/templates/frontend/assets/vendor/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet" type="text/css">
    <link href="{{ asset('public/templates/frontend/assets/vendor/magnific-popup/magnific-popup.css')}}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/templates/frontend/assets/css/owl.carousel.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/templates/frontend/assets/css/slicknav.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('public/templates/frontend/assets/css/animate.css')}}">
    <link href="{{ asset('public/templates/frontend/assets/css/styles.css')}}" rel="stylesheet">
</head>
<body>
<header id="header-wrap">
    <nav class="navbar navbar-expand-md fixed-top scrolling-navbar nav-bg">
        <div class="container">
            <div class="navbar-header">
                <a class="navbar-brand" href="{{ url('/home') }}">
                    <img src="{{ asset('public/templates/frontend/assets/img/logo.png')}}" alt="">
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#main-menu" aria-controls="main-menu" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
            <div class="collapse navbar-collapse" id="main-menu">
                <ul class="navbar-nav mr-auto w-100 justify-content-end">
                    <li><a class="nav-link active" href="{{ url('/') }}">Home</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">About Us</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="{{ url('/aboutus') }}">Who We Are</a>
                            <a class="dropdown-item" href="#">Company Profile</a>
                            <a class="dropdown-item" href="#">Portfolio</a>
                            <a class="dropdown-item" href="#">Services</a>
                        </div>
                    </li>
                    <li><a class="nav-link" href="{{ url('/aboutus') }}">Services</a></li>
                    <li><a class="nav-link" href="#">Products</a></li>
                    <li><a class="nav-link" href="#">Contact</a></li>
                    <li><a class="nav-link" href="{{route('front.posts.index')}}">Blog</a></li>
                    @if (Route::has('login'))
                      @auth
                        <li><a class="nav-link" href="{{ url('/home') }}">Account</a></li>
                      @else
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Account</a>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="{{ route('register') }}">Register</a>
                                <a class="dropdown-item" href="{{ route('login') }}">Login</a>
                            </div>
                        </li>
                        @endauth
                    @endif
                </ul>
            </div>

            {{--Mobile Menu--}}
            <ul class="hmobile-menu">
                <li><a class="active" href="{{ url('/') }}">Home</a></li>
                <li>
                    <a href="#">About Us</a>
                    <ul>
                        <li><a href="{{ url('/aboutus') }}">Who We Are</a></li>
                        <li><a href="#">Company Profile</a></li>
                        <li><a href="#">Portfolio</a></li>
                        <li><a href="#">Services</a></li>
                    </ul>
                </li>
                <li><a href="{{ url('/aboutus') }}">Services</a></li>
                <li><a href="#">Products</a></li>
                <li><a href="{{route('front.posts.index')}}">Blog</a></li>
                <li><a href="#">Contact</a></li>
                @if (Route::has('login'))
                  @auth
                    <li><a href="{{ url('/home') }}">Account</a></li>
                  @else
                    <li>
                        <a href="#">Account</a>
                        <ul>
                            <li><a href="{{ route('register') }}">Reister</a></li>
                            <li><a href="{{ route('login') }}">Login</a></li>
                        </ul>
                    </li>
                  @endauth
                @endif
            </ul>

        </div>
    </nav>
</header>

@yield('content')


<!--==========================
    Footer
  ============================-->
<footer id="footer">
    <div class="footer-top">
        <div class="container">
            <div class="row">

                <div class="col-lg-3 col-md-6 footer-info">
                    <h3>About Us</h3>
                    <p>Tale esse invidunt pri id. Decore patrioque incorrupte ex mei. Labitur eripuit vis in, ut mei viris verterem deseruisse, melius prodesset gloriatur nam ad. In dicta zril congue pro, legere ornatus eu sit. Quem assentior id cum, ius suas salutatus cu, mei sapientem forensibus scribentur an.</p>
                </div>

                <div class="col-lg-3 col-md-6 footer-links">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="#">Home</a></li>
                        <li><a href="#">About us</a></li>
                        <li><a href="#">Services</a></li>
                        <li><a href="#">Terms of service</a></li>
                        <li><a href="#">Privacy policy</a></li>
                    </ul>
                </div>

                <div class="col-lg-3 col-md-6 footer-contact">
                    <h4>Get In Touch</h4>
                    <p>
                        J255 Maiz Street <br>
                        Miami, MA 6526-65<br>
                        United States <br>
                        <strong>Phone:</strong> +1 8773 97736 34<br>
                        <strong>Email:</strong> info@company.com<br>
                    </p>

                    <div class="social-links">
                        <a href="#" class="twitter"><i class="fa fa-twitter"></i></a>
                        <a href="#" class="facebook"><i class="fa fa-facebook"></i></a>
                        <a href="#" class="instagram"><i class="fa fa-instagram"></i></a>
                        <a href="#" class="google-plus"><i class="fa fa-google-plus"></i></a>
                        <a href="#" class="linkedin"><i class="fa fa-linkedin"></i></a>
                    </div>

                </div>

                <div class="col-lg-3 col-md-6 footer-newsletter">
                    <h4>Newsletter</h4>
                    <p>Et solum affert admodum sit, sit diceret aliquid definiebas ea. Fabulas intellegebat ei nam, eum an verear tincidunt referrentur. Postea aeterno pertinax in quo, cum ridens verear scribentur at.</p>
                    <form action="" method="post">
                        <input type="email" name="email" placeholder="Enter email"><input type="submit" value="Join">
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="container">
        <div class="copyright">
            © Copyright 2018 <a href="https://hezecom.com/"> Hezecom</a> All Rights Reserved
        </div>
    </div>
</footer><!-- #footer -->

<a href="#" class="back-to-top" style="display: none;"><i class="fa fa-chevron-up"></i></a>

<!-- Bootstrap core JavaScript -->
<script src="{{ asset('public/templates/frontend/assets/vendor/jquery/jquery.min.js')}}"></script>
<script src="{{ asset('public/templates/frontend/assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{ asset('public/templates/frontend/assets/vendor/magnific-popup/jquery.magnific-popup.min.js')}}"></script>
<script src="{{ asset('public/templates/frontend/assets/js/owl.carousel.js')}}"></script>
<script src="{{ asset('public/templates/frontend/assets/js/smoothscroll.js')}}"></script>
<script src="{{ asset('public/templates/frontend/assets/js/janimate.min.js')}}"></script>
<script src="{{ asset('public/templates/frontend/assets/js/jquery.slicknav.js')}}"></script>
<script src="{{ asset('public/templates/frontend/assets/js/custom.js')}}"></script>
</body>
</html>