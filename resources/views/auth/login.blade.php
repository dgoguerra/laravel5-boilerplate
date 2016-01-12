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
                    <i class="fa fa-check"></i> {!! Session::get('status') !!}
                </div>
            @endif

            <form method="POST" action="{{ route('auth.login') }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group">
                    <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}">
                </div>

                <div class="form-group">
                    <input type="password" name="password" class="form-control" placeholder="Password">
                </div>

                <div class="form-group checkbox text-center">
                    <label>
                        <input type="checkbox" name="remember"> Remember Me
                    </label>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-block btn-primary">Login</button>
                </div>

                <small><a href="{{ route('auth.reset_password.email.show') }}">Forgot password?</a></small>
                <br/>
                <small><a href="{{ route('auth.register.show') }}">Do not have an account?</a></small>
            </form>
        </div>
    </div>

@endsection('content')
