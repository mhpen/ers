<!DOCTYPE html>
<html>
<head>
    <style>
        /* Add your email styles here */
    </style>
</head>
<body>
    <h2>Payment Declined</h2>
    <p>Dear <?php echo $data['participant_name']; ?>,</p>
    
    <p>Unfortunately, your payment for <strong><?php echo $data['event_title']; ?></strong> has been declined.</p>
    
    <p><strong>Reason:</strong> <?php echo $data['remarks']; ?></p>
    
    <p>Please submit a new payment or contact support for assistance.</p>
</body>
</html> 