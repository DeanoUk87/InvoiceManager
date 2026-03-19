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
                        <a class="navbar-brand">Send Message to Customers</a>
                    </nav>
                </div>
                <div class="card-body text-center">
                    @if(Auth::user()->hasAnyRole(['admin','admin2']))
                        @if(($pending/$limit)>0)
                            <h4>You have {{$pending}} total messages left to be sent to customers</h4>
                            <h4 class="text-danger">The messages will be sent in {{ceil($pending/$limit)}} batches</h4>
                            <a href="{{route('admincomposer.send.mail',['id'=>$id])}}" class="btn btn-success btn-lg hts-loading">
                                <span class="fa fa-send-o"></span> Click here to start sending
                            </a>
                        @else
                            <h4 class="text-danger">All Pending messages has been sent to customers with emails</h4>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
        
