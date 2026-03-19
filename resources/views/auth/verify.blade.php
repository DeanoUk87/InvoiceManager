@extends('layouts.form')


<title>
    @lang('app.header_title') | @lang('app.users.title')
</title>

@section('content')
    <div class="container-scroller">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="row mb-2 htsDisplay">
                        <div class="col-lg-12">
                            <div class="card">
                                <div class="card-header" style="">
                                    <nav class="nav  justify-content-between">
                                        <a class="navbar-brand">{{ __('Verify Your Email Address') }}</a>
                                    </nav>
                                </div>
                                <div class="card-body">
                                    @if (session('resent'))
                                        <div class="alert alert-success" role="alert">
                                            {{ __('A fresh verification link has been sent to your email address.') }}
                                        </div>
                                    @endif
                                    <div style="font-size: 18px">
                                        <p>{{ __('Before proceeding, please check your email for a verification link.') }}</p>
                                        <p>{{ __('If you did not receive the email') }}</p>
                                        <p><a href="{{ route('verification.resend') }}" class="btn btn-info btn-lg">{{ __('Click here to resend verification code') }}</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

