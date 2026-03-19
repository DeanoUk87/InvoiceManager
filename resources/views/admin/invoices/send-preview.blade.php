@extends('layouts.app')

@section('title')
    @lang('app.header_title') | @lang('main.invoices.title')
@endsection

@section('content')
    {{--@if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif--}}
    <div class="row mb-2 htsDisplay">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header" style="">
                    <nav class="nav  justify-content-between">
                        @if(request()->input('sending'))
                        <a class="navbar-brand text-success">Now Sending Invoices to Customers Email</a>
                        @else
                            <a class="navbar-brand">Send Invoice to Customers</a>
                        @endif
                    </nav>
                </div>
                <div class="card-body text-center">
                    @if(Auth::user()->hasAnyRole(['admin','admin2']))
                        @if($invoices>0 && !request()->input('sending'))
                            <h4>You have {{$invoices}} total invoices left to be sent to customers</h4>
                            {{--<h4 class="text-danger">The invoices will be sent in {{ceil($invoices/$limit)}} batches</h4>--}}
                            <a href="{{route('invoices.massmail')}}" class="btn btn-success btn-lg hts-loading">
                                <span class="fa fa-send-o"></span> Click here to start sending
                            </a>
                        @elseif($invoices>0 && request()->input('sending'))
                            <h4>You have {{$invoices}} total invoices left to be sent to customers</h4>
                            <p>You do not need to do anything, your invoices would be sent automatically and you will be notify here once done.</p>
                            <p class="text-danger lead">This page automatically refreshes every 1 minute to check sending progress</p>
                        @else
                            <h4 class="text-success">All invoices has been sent to customers with emails</h4>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts');
@if(Auth::user()->hasAnyRole(['admin','admin2']))
    <script type="text/javascript">
      setTimeout(function () { location.reload(true); }, 60*1000); //convert to milliseconds
    </script>
@endif
@endsection


