@extends('layouts.app')

@section('title')
    @lang('app.header_title') | @lang('app.settings')
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <div class="row mb-2 htsDisplay">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header" style="">
                    <nav class="nav  justify-content-between">
                        <a class="navbar-brand">@lang('app.commands')</a>
                        <a href="{{route('settings.index')}}" class="btn btn-info btn-sm"><i class="fa fa-reply"></i> @lang('app.settings')</a>
                    </nav>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-5">
                            <a href="{{route('artisan.commands','cache:clear')}}" class="btn btn-info">Clear application cache</a>
                        </div>
                        <div class="col-md-5">
                            <a href="{{route('artisan.commands','key:generate')}}" class="btn btn-info">Reset application key</a>
                        </div>
                    </div><!--/row-->

                    <div class="row mb-4">
                        <div class="col-md-5">
                            <a href="{{route('artisan.commands','config:cache')}}" class="btn btn-success">Create a cache file for faster configuration loading</a>
                        </div>
                        <div class="col-md-5">
                            <a href="{{route('artisan.commands','config:clear')}}" class="btn btn-success">Remove the configuration cache file</a>
                        </div>
                    </div><!--/row-->

                    <div class="row mb-4">
                        <div class="col-md-5">
                            <a href="{{route('artisan.commands','auth:clear-resets')}}" class="btn btn-primary">Flush expired password reset tokens</a>
                        </div>
                       {{-- <div class="col-md-5">
                            <a href="{{route('artisan.commands','config:clear')}}" class="btn btn-primary">Remove the configuration cache file</a>
                        </div>--}}
                    </div><!--/row-->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>

    </script>
@endsection

