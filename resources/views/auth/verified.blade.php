@extends('layouts.form')

<title>
    @lang('app.header_title') | Account Verification Success
</title>

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="row mb-2 htsDisplay">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header" style="">
                                <nav class="nav  justify-content-between">
                                    <a class="navbar-brand">Account Verification Success</a>
                                </nav>
                            </div>
                            <div class="card-body">
                                <h4>You have successfuly verify your account</h4>
                                <p><a href="{{url('home')}}" class="btn btn-success">Proceed to your Account</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
