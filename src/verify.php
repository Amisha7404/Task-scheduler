<?php
require_once 'functions.php';

// TODO: Implement verification logic.
$email = $_GET['email'] ?? '';
$code  = $_GET['code'] ?? '';

if ($email && $code) {
    if (verifySubscription($email, $code)) {
        echo " Subscription verified successfully!";
    } else {
        echo " Invalid verification link or code.";
    }
} else {
    echo " Missing email or verification code.";
}
?>

<!DOCTYPE html>
<html>
<head>
	<!-- Implement Header ! -->
</head>
<body>
	<!-- Do not modify the ID of the heading -->
	<h2 id="verification-heading">Subscription Verification</h2>
	<!-- Implemention body -->
</body>
</html>