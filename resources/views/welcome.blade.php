@extends('base_layout')

@section('content')
    @if (isset($errors) && count($errors) > 0)
        <div class="row">
            <div class="col-xs-12 col-lg-8 col-lg-offset-2">
                <div class="alert alert-danger">
                    <i class="fa fa-warning"></i> {{ $errors->first() }}
                </div>
            </div>
        </div>
    @endif

    @if(Session::get('status'))
        <div class="row">
            <div class="col-xs-12 col-lg-8 col-lg-offset-2">
                <div class="alert alert-success">
                    <i class="fa fa-check"></i> {{ Session::get('status') }}
                </div>
            </div>
        </div>
    @endif

    <div class="flex-center">
        <div class="laravel-title">Laravel 5</div>
    </div>
@endsection('content')
