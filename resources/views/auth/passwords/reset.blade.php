@extends('layouts.guest')

@section('title', 'Reset My Password')

@section('content')
<form id="sign_in" method="POST" action="{{ route('password.update') }}">
    @csrf
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
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="input-group">
        <span class="input-group-addon">
            <i class="material-icons">person</i>
        </span>
        <div class="form-line">
            <input type="email" class="form-control" name="email" placeholder="E-mail" value="{{ $email ?? old('email') }}" required autofocus>
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
    <div class="input-group">
        <span class="input-group-addon">
            <i class="material-icons">lock</i>
        </span>
        <div class="form-line">
            <input type="password" class="form-control" name="password_confirmation" placeholder="Retype Password" required>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <button class="btn btn-block bg-green waves-effect" type="submit">{{ __('RESET PASSWORD') }}</button>
        </div>
    </div>

    <div class="row m-t-20 m-b--5 align-center">
        <a href="{{ route('login') }}">{{ __('Sign In!') }}</a>
    </div>
</form>
@endsection
