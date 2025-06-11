<?php

//Files where we store our data
define('TASKS_FILE', __DIR__ . '/tasks.txt');        
define('SUBSCRIBERS_FILE', __DIR__ . '/subscribers.txt');    
define('PENDING_SUBSCRIPTIONS_FILE', __DIR__ . '/pending_subscriptions.txt');  

//Load email settings
require_once __DIR__ . '/mail/config.php';

//Create empty files if they don't exist
function initializeFiles() {
	$files = [TASKS_FILE, SUBSCRIBERS_FILE, PENDING_SUBSCRIPTIONS_FILE];
	foreach ($files as $file) {
		if (!file_exists($file)) {
			file_put_contents($file, '');
		}
	}
}

initializeFiles();


function addTask(string $task_name): bool {
	$tasks = getAllTasks();
	foreach ($tasks as $task) {
		if ($task['name'] === $task_name) {
			return false;
		}
	}
	
	$task_id = uniqid();
	$new_task = [
		'id' => $task_id,
		'name' => $task_name,
		'completed' => false
	];
	
	$tasks[] = $new_task;
	file_put_contents(TASKS_FILE, json_encode($tasks));
	return $task_id;
}


function getAllTasks(): array {
	if (!file_exists(TASKS_FILE) || empty(file_get_contents(TASKS_FILE))) {
		return [];
	}
	return json_decode(file_get_contents(TASKS_FILE), true) ?? [];
}


function markTaskAsCompleted(string $task_id, bool $is_completed): bool {
	$tasks = getAllTasks();
	foreach ($tasks as &$task) {
		if ($task['id'] === $task_id) {
			$task['completed'] = $is_completed;
			break;
		}
	}
	file_put_contents(TASKS_FILE, json_encode($tasks));
	return true;
}

function deleteTask(string $task_id): bool {
	$tasks = getAllTasks();
	$tasks = array_filter($tasks, function($task) use ($task_id) {
		return $task['id'] !== $task_id;
	});
	file_put_contents(TASKS_FILE, json_encode(array_values($tasks)));
	return true;
}


function generateVerificationCode(): string {
	return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

//Add a new email to our reminder list
function subscribeEmail(string $email): array {
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		return ['success' => false, 'message' => 'Invalid email format'];
	}
	
	$subscribers = file_exists(SUBSCRIBERS_FILE) ? file(SUBSCRIBERS_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
	if (in_array($email, $subscribers)) {
		return ['success' => false, 'message' => 'Email already subscribed'];
	}
	
	$pending = file_exists(PENDING_SUBSCRIPTIONS_FILE) ? file(PENDING_SUBSCRIPTIONS_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
	if (in_array($email, $pending)) {
		return ['success' => false, 'message' => 'Email verification pending'];
	}
	
	$code = generateVerificationCode();
	file_put_contents(PENDING_SUBSCRIPTIONS_FILE, $email . ':' . $code . "\n", FILE_APPEND);
	
	$verification_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/verify.php?email=" . urlencode($email) . "&code=" . $code;
	$subject = "Verify subscription to Task Planner";
	$message = '<p>Click the link below to verify your subscription to Task Planner:</p>
<p><a href="' . $verification_link . '">Verify Subscription</a></p>';

	if (!sendMail($email, $subject, $message)) {
		return ['success' => false, 'message' => 'Failed to send verification email'];
	}

	return ['success' => true, 'message' => 'Verification email sent'];
}

//Check if verification code is correct
function verifySubscription(string $email, string $code): bool {
	$pending = getPendingSubscriptions();
	if (!isset($pending[$email]) || $pending[$email] !== $code) {
		return false;
	}
	
	$subscribers = getSubscribers();
	if (!in_array($email, $subscribers)) {
		$subscribers[] = $email;
		file_put_contents(SUBSCRIBERS_FILE, implode("\n", $subscribers) . "\n");
	}
	
	//Remove from pending list
	$lines = file(PENDING_SUBSCRIPTIONS_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	$new_lines = array_filter($lines, function($line) use ($email) {
		return !str_starts_with($line, $email . ':');
	});
	file_put_contents(PENDING_SUBSCRIPTIONS_FILE, implode("\n", $new_lines) . "\n");
	
	return true;
}

//Remove an email from our reminder list
function unsubscribeEmail(string $email): bool {
	$subscribers = getSubscribers();
	$subscribers = array_filter($subscribers, function($sub) use ($email) {
		return $sub !== $email;
	});
	file_put_contents(SUBSCRIBERS_FILE, implode("\n", $subscribers) . "\n");
	return true;
}

//Functions to handle email reminders
function sendTaskReminders(): void {
	$tasks = getAllTasks();
	//Get only tasks that are not done
	$pending_tasks = array_filter($tasks, function($task) {
		return !$task['completed'];
	});
	
	if (empty($pending_tasks)) {
		return;
	}
	
	$subscribers = getSubscribers();
	foreach ($subscribers as $email) {
		sendTaskEmail($email, $pending_tasks);
	}
}

//Send reminder email to one person
function sendTaskEmail(string $email, array $pending_tasks): bool {
	$subject = "Task Planner - Pending Tasks Reminder";
	
	//email message format
	$message = '<h2>Pending Tasks Reminder</h2>';
	$message .= '<p>You have the following pending tasks:</p>';
	$message .= '<ul>';
	foreach ($pending_tasks as $task) {
		$message .= '<li>' . htmlspecialchars($task['name']) . '</li>';
	}
	$message .= '</ul>';
	
	//unsubscribe link
	$host = php_sapi_name() === 'cli' ? 'localhost' : $_SERVER['HTTP_HOST'];
	$unsubscribe_link = "http://" . $host . "/php-assignment/src/unsubscribe.php?email=" . urlencode($email);
	$message .= '<p><a href="' . $unsubscribe_link . '">Unsubscribe from reminders</a></p>';
	
	return sendMail($email, $subject, $message);
}

//Get list of all verified subscribers
function getSubscribers(): array {
	if (!file_exists(SUBSCRIBERS_FILE)) {
		return [];
	}
	return file(SUBSCRIBERS_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
}

//Get list of emails waiting for verification
function getPendingSubscriptions(): array {
	if (!file_exists(PENDING_SUBSCRIPTIONS_FILE)) {
		return [];
	}
	
	$pending = [];
	$lines = file(PENDING_SUBSCRIPTIONS_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	foreach ($lines as $line) {
		list($email, $code) = explode(':', $line);
		$pending[$email] = $code;
	}
	return $pending;
}
