@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<form id="sign_in" method="POST">
    @csrf
    <div class="msg">Sign in to start your session</div>
    @error('email')
        <div class="alert bg-red alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ $message }}
        </div>
    @enderror
    @error('password')
        <div class="alert bg-red alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ $message }}
        </div>
    @enderror
    <div class="input-group">
        <span class="input-group-addon">
            <i class="material-icons">person</i>
        </span>
        <div class="form-line">
            <input type="email" class="form-control" name="email" placeholder="E-mail" required autofocus>
        </div>
    </div>
    <div class="input-group">
        <span class="input-group-addon">
            <i class="material-icons">lock</i>
        </span>
        <div class="form-line">
            <input type="password" class="form-control" name="password" placeholder="Password" required>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-8 p-t-5">
            <input type="checkbox" name="rememberme" id="rememberme" class="filled-in chk-col-lime" {{ old('remember') ? 'checked' : '' }}>
            <label for="rememberme">{{ __('Remember Me') }}</label>
        </div>
        <div class="col-xs-4">
            <button class="btn btn-block bg-green waves-effect" type="submit">{{ __('SIGN IN') }}</button>
        </div>
    </div>
    @if (Route::has('password.request'))
    <div class="row m-t-15 m-b--20">
        <div class="col-xs-6 align-right">
            <a href="{{ route('password.request') }}">{{ __('Forgot Password?') }}</a>
        </div>
    </div>
    @endif
</form>
@endsection
