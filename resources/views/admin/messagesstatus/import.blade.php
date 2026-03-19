@extends('layouts.app')
@section('title')
    @lang('app.header_title') | @lang('app.messagesstatus.title')
@endsection

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <div class="row mb-2 viewDetails">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <nav class="nav  justify-content-between">
                        <a class="navbar-brand">Import messagesstatus to database</a>
                        <div class="btn-group">
                            <a href="{{route('messagesstatus.index')}}" class="btn btn-info btn-sm" ><i class="fa fa-reply"></i> @lang('app.goback')</a>
                        </div>
                    </nav>
                </div>
                    <div class="card-body" style="padding: 10px 0 10px 0">
                        {!! Form::open(array('route' => 'messagesstatus.import.store','method'=>'POST','files'=>'true','id'=>'hezecomform')) !!}
                        <div class="row">
                            <div class="col-12">

                                <div class="hts-flash"></div>

                                <div class="form-group">
                                    {!! Form::label('messagesstatus_file','Select File to Import: (csv,xls, xlsx,)',['class'=>'col-md-3']) !!}
                                    <div class="col-md-9">
                                        {!! Form::file('messagesstatus_file', array('class' => 'form-control')) !!}
                                        {!! $errors->first('messagesstatus_file', '<p class="alert alert-danger">:message</p>') !!}
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group">
                                        {!! Form::submit('Import File',['class'=>'btn btn-primary']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
