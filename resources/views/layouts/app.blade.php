<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link rel="shortcut icon" type="image/png" sizes="16x16" href="{{ asset('public/templates/admin/images/favicon.png') }}" />
   @include('partials.appcss')
</head>
<body>

<!-- ======= Top Navigation ======= -->
@include('partials.nav-top')
<!-- ====== Right Navigation ======== -->
@include('partials.nav-right')
<!-- ======== Left Navigation ======== -->
@include('partials.nav-left')
<!-- ====== Content ===== -->
<section class="main-container container-fluid" >
    @yield('content')
</section>

<footer class="footer">
    <span>Copyright &copy; {{\Carbon\Carbon::now()->format('Y')}} <a href="#">DNWeb</a></span>
</footer>
@include('partials.appjs')
@yield('scripts')
</body>
</html>
