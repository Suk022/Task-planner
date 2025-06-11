<?php
require_once 'functions.php';

$email = $_GET['email'] ?? '';
if (empty($email)) {
    die('Invalid unsubscribe link');
}

$success = unsubscribeEmail($email);
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Unsubscribe</title>
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
	<h1>Unsubscribe</h1>
	
	<?php if ($success): ?>
	<div class="message success">
		<h2>Unsubscribe Successful!</h2>
		<p>You have been successfully unsubscribed from task reminders.</p>
	</div>
	<?php else: ?>
	<div class="message error">
		<h2>Unsubscribe Failed</h2>
		<p>There was an error processing your unsubscribe request. Please try again later.</p>
	</div>
	<?php endif; ?>
	
	<p><a href="index.php">Return to Task Planner</a></p>
</body>
</html>
