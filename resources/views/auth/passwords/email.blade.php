@extends('layouts.guest')

@section('title', 'Reset Password')

@section('content')
<form id="forgot_password" method="POST" action="{{ route('password.email') }}">
    @csrf
    <div class="msg">
        {{ __('Enter your email address that you used to register. We\'ll send you an email with your username and a
        link to reset your password.') }}
    </div>
    @if (session('status'))
        <div class="alert bg-green alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ session('status') }}
        </div>
    @endif
    @error('email')
        <div class="alert bg-red alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            {{ $message }}
        </div>
    @enderror
    <div class="input-group">
        <span class="input-group-addon">
            <i class="material-icons">email</i>
        </span>
        <div class="form-line">
            <input type="email" class="form-control" name="email" placeholder="Email" required autofocus>
        </div>
    </div>

    <button class="btn btn-block btn-lg bg-green waves-effect" type="submit">{{ __('RESET MY PASSWORD') }}</button>

    <div class="row m-t-20 m-b--5 align-center">
        <a href="{{ route('login') }}">{{ __('Sign In!') }}</a>
    </div>
</form>
@endsection
