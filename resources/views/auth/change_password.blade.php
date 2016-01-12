@extends('base_layout')

@section('content')

    <div class="row flex-center">
        <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
            @if (isset($errors) && count($errors) > 0)
                <div class="alert alert-danger">
                    <i class="fa fa-warning"></i> {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('settings.change_password') }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="form-group">
                    <input type="password" class="form-control" name="password" placeholder="New Password">
                </div>

                <div class="form-group">
                    <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm New Password">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-block btn-primary">Change Password</button>
                </div>
            </form>
        </div>
    </div>

@endsection('content')
