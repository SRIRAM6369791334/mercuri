<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Quotation Created</title>
</head>
<body>
    <h2>Quotation Created</h2>
    <p>A new quotation has been created with the following details:</p>

    <ul>
        <li><strong>Quotation No:</strong> {{ $mailData['quotationno'] }}</li>
        <li><strong>Client Name:</strong> {{ $mailData['clientname'] }}</li>
        <li><strong>Grand Total:</strong> {{ $mailData['grandtotal'] }}</li>
        <li><strong>Created At:</strong> {{ $mailData['created_at'] }}</li>
    </ul>

    <p>Thank you!</p>
</body>
</html>
