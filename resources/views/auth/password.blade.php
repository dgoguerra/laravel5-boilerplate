@extends('base_layout')

@section('content')

    <div class="row flex-center">
        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
            @if (isset($errors) && count($errors) > 0)
                <div class="alert alert-danger">
                    <i class="fa fa-warning"></i> {{ $errors->first() }}
                </div>
            @endif

            @if (Session::get('status'))
                <div class="alert alert-success">
                    <i class="fa fa-check"></i> {{ Session::get('status') }}
                </div>
            @endif

            <form class="form" method="POST" action="{{ route('auth.reset_password.email') }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group">
                    <input type="email" class="form-control" name="email" placeholder="Email" value="{{ old('email') }}">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-block btn-primary">Send Password Reset Link</button>
                </div>
            </form>
        </div>
    </div>

@endsection('content')
