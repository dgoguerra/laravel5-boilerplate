@extends('base_layout')

@section('content')

    <div class="row flex-center">
        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
            @if (isset($errors) && count($errors) > 0)
                <div class="alert alert-danger">
                    <i class="fa fa-warning"></i> {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('auth.reset_password') }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <input type="hidden" name="token" value="{{ $token }}">

                 <div class="form-group">
                    <input type="email" class="form-control" name="email" placeholder="Email" value="{{ old('email') }}">
                </div>

                 <div class="form-group">
                    <input type="password" class="form-control" name="password" placeholder="Password">
                </div>

                 <div class="form-group">
                    <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-block btn-primary">Reset Password</button>
                </div>
            </form>
        </div>
    </div>

@endsection('content')
