<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Drawing Approval Status</title>
</head>
<body style="font-family: Arial, sans-serif; font-size:14px; color:#333;">

    <h3>
        Drawing {{ ucfirst($status) }}
    </h3>

    <p>
        The design drawings for the following lead have been
        <strong>{{ $status }}</strong>.
    </p>

    <table cellpadding="6">
        <tr>
            <td><strong>Lead ID</strong></td>
            <td>:</td>
            <td>{{ $lead_id }}</td>
        </tr>
    </table>

    <p style="margin-top:15px;">
        Please log in to the system to proceed with the next steps.
    </p>

    <p style="font-size:12px;color:#777;">
        This is an automated notification. Please do not reply.
    </p>

</body>
</html>
