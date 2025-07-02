<?php
require_once 'functions.php';

// TODO: Implement the unsubscription logic.
$email = $_GET['email'] ?? '';

if ($email) {
    if (unsubscribeEmail($email)) {
        echo "🚫 You have been unsubscribed.";
    } else {
        echo "⚠️ This email is not subscribed.";
    }
} else {
    echo "❌ Missing email.";
}

?>

<!DOCTYPE html>
<html>
<head>
	<!-- Implement Header ! -->
</head>
<body>
	<!-- Do not modify the ID of the heading -->
	<h2 id="unsubscription-heading">Unsubscribe from Task Updates</h2>
	<!-- Implemention body -->
</body>
</html>
