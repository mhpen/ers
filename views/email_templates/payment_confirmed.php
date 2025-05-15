<!DOCTYPE html>
<html>
<head>
    <style>
        /* Add your email styles here */
    </style>
</head>
<body>
    <h2>Payment Confirmed</h2>
    <p>Dear <?php echo $data['participant_name']; ?>,</p>
    
    <p>Your payment for <strong><?php echo $data['event_title']; ?></strong> has been confirmed.</p>
    
    <h3>Event Details:</h3>
    <ul>
        <li>Date: <?php echo $data['event_date']; ?></li>
        <li>Location: <?php echo $data['location']; ?></li>
        <li>Amount Paid: ₱<?php echo number_format($data['amount'], 2); ?></li>
        <li>Reference Number: <?php echo $data['reference_number']; ?></li>
    </ul>

    <p>Please find your event QR code attached to this email. You'll need to present this for check-in at the event.</p>

    <p>Thank you for your registration!</p>
</body>
</html> 