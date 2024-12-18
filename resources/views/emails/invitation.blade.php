<html>
<head>
    <title>Project Invitation</title>
</head>
<body>
    <h2>Hello {{ $userName }},</h2>

    <p>You have been invited to join the project <strong>{{ $projectName }}</strong>.</p>

    <p>To accept the invitation, click the link below:</p>

    <p>
        <a href="{{ $invitationLink }}">Accept Invitation</a>
    </p>

    <p>If you do not wish to join, you can ignore this message.</p>

    <!-- <p>Best Regards,<br>Your Team</p> -->
</body>
</html>
