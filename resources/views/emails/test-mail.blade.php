@extends('emails.layouts.base')

@section('content')
    <p>{{ __('messages.Hello') }},</p>

    <p>{{ __('messages.This is a test email to confirm your mail configuration is working correctly.') }}</p>

    <p>
        {{ __('messages.Sent from') }} <strong>{{ $appName }}</strong><br>
        {{ __('messages.Sent at') }}: {{ $sentAt }}
    </p>

    <p>{{ __('messages.Thank you for using our application!') }}</p>
@endsection
