@extends('layouts.guest')

@section('title', 'Verifying E-mail')

@section('content')
@if (session('resent'))
<div class="alert alert-success" role="alert">
    {{ __('A fresh verification link has been sent to your email address.') }}
</div>
@endif

{{ __('Before proceeding, please check your email for a verification link.') }}
{{ __('If you did not receive the email') }}, please contact the owner/manager of your branch to manually verified it or you can <a href="{{ route('verification.resend') }}">{{ __('click here to request another') }}</a>.
@endsection
