@extends('layouts.app')

@section('title')
    @lang('app.header_title') | @lang('main.invoices.title')
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    <div class="row mb-2 htsDisplay">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header" style="">
                    <nav class="nav  justify-content-between">
                        <a class="navbar-brand">Message Info</a>
                    </nav>
                </div>
                <div class="card-body text-center">
                    @if(($invoices)>0)
                        <h4>You have {{$invoices}} total invoices left to be sent to customers</h4>
                        <p>You do not need to do anything, your invoices would be sent automatically and you get a notification here once done.</p>
                    @else
                        <h4 class="text-danger">All invoices has been sent to customers with emails</h4>
                    @endif
                    <hr>
                    {{--@foreach($emailSent as $mail)
                        {!! $mail !!}<br>
                    @endforeach--}}
                </div>
            </div>
        </div>
    </div>
@endsection

