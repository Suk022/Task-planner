<?php
require_once 'functions.php';

//Validates verification paras
$email = $_GET['email'] ?? '';
$code = $_GET['code'] ?? '';

if (empty($email) || empty($code)) {
    die('Invalid verification link');
}

$success = verifySubscription($email, $code);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Email Verification</title>
	<style>
		body {
			font-family: Arial, sans-serif;
			max-width: 600px;
			margin: 0 auto;
			padding: 20px;
			text-align: center;
		}
		.message {
			padding: 20px;
			border-radius: 4px;
			margin: 20px 0;
		}
		.success {
			background-color: #d4edda;
			color: #155724;
			border: 1px solid #c3e6cb;
		}
		.error {
			background-color: #f8d7da;
			color: #721c24;
			border: 1px solid #f5c6cb;
		}
	</style>
</head>
<body>
	<h1>Email Verification</h1>
	
	<?php if ($success): ?>
	<div class="message success">
		<h2>Verification Successful!</h2>
		<p>Your email has been successfully verified. You will now receive task reminders.</p>
	</div>
	<?php else: ?>
	<div class="message error">
		<h2>Verification Failed</h2>
		<p>The verification link is invalid or has expired. Please try subscribing again.</p>
	</div>
	<?php endif; ?>
	
	<p><a href="index.php">Return to Task Planner</a></p>
</body>
</html>