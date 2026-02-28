<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>EasyColoc Invitation</title>
</head>

<body style="font-family: sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #4f46e5; color: white; padding: 30px; border-radius: 10px; text-align: center;">
        <h1 style="margin: 0;">EasyColoc</h1>
        <p style="margin: 10px 0 0;">Managing shared expenses made easy</p>
    </div>

    <div style="padding: 30px; border: 1px solid #e5e7eb; border-radius: 10px; margin-top: 20px;">
        <h2 style="margin-top: 0; color: #111827;">Hello!</h2>
        <p>You have been invited by <strong>{{ $invitation->inviter->name }}</strong> to join the colocation <strong>{{ $invitation->colocation->name }}</strong>.</p>

        @if($invitation->colocation->description)
        <div style="background-color: #f9fafb; padding: 15px; border-left: 4px solid #4f46e5; margin: 20px 0;">
            <p style="margin: 0; font-style: italic;">"{{ $invitation->colocation->description }}"</p>
        </div>
        @endif

        <div style="text-align: center; margin-top: 30px;">
            <a href="{{ route('invitations.show', $invitation->token) }}"
                style="background-color: #4f46e5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                View Invitation
            </a>
        </div>

        <p style="margin-top: 30px; font-size: 0.875rem; color: #6b7280;">
            This invitation will expire on {{ $invitation->expires_at->format('M d, Y') }}.
            If you were not expecting this invitation, you can safely ignore this email.
        </p>
    </div>

    <div style="text-align: center; margin-top: 20px; font-size: 0.75rem; color: #9ca3af;">
        &copy; {{ date('Y') }} EasyColoc. All rights reserved.
    </div>
</body>

</html>