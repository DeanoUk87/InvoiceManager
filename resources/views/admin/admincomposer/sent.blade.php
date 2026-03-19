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
                    @if(($pending/$limit)>0)
                        <h4>You have {{$pending}} total messages left to be sent to customers</h4>
                    <a href="{{route('admincomposer.send.mail',['id'=>$id])}}" class="btn btn-success btn-lg hts-loading">
                        <span class="fa fa-send-o"></span> Click here to continue sending
                    </a>
                    @else
                        <h4 class="text-danger">All pending messages has been sent to customers with emails</h4>
                    @endif
                    <hr>
                    @foreach($emailSent as $mail)
                        {!! $mail !!}<br>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
        
