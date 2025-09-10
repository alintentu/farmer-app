<!DOCTYPE html>
<html>
<body>
    <p>Hello {{ $user->name }},</p>
    <p>You have been invited to join the Farmer App platform. Click the link below to accept your invitation and set your password:</p>
    <p><a href="{{ $inviteUrl }}">Accept Invitation</a></p>
    <p>If you did not expect this email, you can ignore it.</p>
</body>
</html>
