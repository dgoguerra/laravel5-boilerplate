<p>Hi {{ $user->name }},</p>

<p>
    You have created an account with the email <strong>{{ $user->email }}</strong>.
    To confirm your account, please follow <a href="{{ route('auth.register.confirm', [$user->mail_confirm_token]) }}">this link</a>.
</p>
