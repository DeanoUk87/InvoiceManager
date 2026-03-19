<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Laravel Application">
    <meta name="author" content="Hezecom">
    <meta name="url" content="https://www.hezecom.com">
    <title>@yield('title')</title>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/vendor/bootstrap4/css/bootstrap.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/vendor/fontawesome4/css/font-awesome.min.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{ asset('public/templates/admin/assets/css/auth.css') }}"/>
    <link rel="shortcut icon" href="{{ asset('public/templates/admin/images/favicon.png') }}" />
</head>
<body>
@yield('content')
<script type="text/javascript" src="{{asset('public/vendor/jquery/jquery-3.2.1.min.js')}}"></script>
<script type="text/javascript" src="{{asset('public/vendor/popper/popper.min.js')}}"></script>
<script type="text/javascript" src="{{asset('public/vendor/bootstrap4/js/bootstrap.min.js')}}"></script>
@yield('scripts')
</body>
</html>
