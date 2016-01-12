<p>Hi {{ $user->name }},</p>

<p>You have requested to use <strong>{{ $user->email_to_change }}</strong> instead of <strong>{{ $user->email }}</strong> as your email address.</p>

<p>You will receive all new messages in this address. Please follow <a href="{{ route('settings.change_email.confirm', [$user->email_change_token]) }}">this link</a> to confirm that you are the owner of this address.</p>

<p>Upon confirmation, any new messages will be received in your new email address.</p>
