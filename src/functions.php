<?php

/**
 * Adds a new task to the task list
 * 
 * @param string $task_name The name of the task to add.
 * @return bool True on success, false on failure.
 */
function addTask(string $task_name ): bool {
	$file  = __DIR__ . '/tasks.txt';
	// TODO: Implement this function
	$tasks = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
	foreach ($tasks as $task) {
        list($id, $name, $completed) = explode('|', $task);
        if (trim($name) === trim($task_name)) {
            return false; // Duplicate found
        }
    }
	$new_id = count($tasks) > 0 ? (int)explode('|', end($tasks))[0] + 1 : 1;
    $new_task = "$new_id|" . trim($task_name) . "|0";
	return file_put_contents($file, $new_task . PHP_EOL, FILE_APPEND | LOCK_EX) !== false;
}

/**
 * Retrieves all tasks from the tasks.txt file
 * 
 * @return array Array of tasks. -- Format [ id, name, completed ]
 */
function getAllTasks(): array {
	$file = __DIR__ . '/tasks.txt';
	// TODO: Implement this function
	 if (!file_exists($file)) return [];

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $tasks = [];

    foreach ($lines as $line) {
        list($id, $name, $completed) = explode('|', $line);
        $tasks[] = [(int)$id, $name, (int)$completed];
    }

    return $tasks;
}

/**
 * Marks a task as completed or uncompleted
 * 
 * @param string  $task_id The ID of the task to mark.
 * @param bool $is_completed True to mark as completed, false to mark as uncompleted.
 * @return bool True on success, false on failure
 */
function markTaskAsCompleted( string $task_id, bool $is_completed ): bool {
	$file  = __DIR__ . '/tasks.txt';
	// TODO: Implement this function
	 if (!file_exists($file)) return false;

    $updated = false;
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $new_lines = [];

    foreach ($lines as $line) {
        list($id, $name, $completed) = explode('|', $line);
        if ((int)$id === (int)$task_id) {
            $new_lines[] = "$id|$name|" . ($is_completed ? '1' : '0');
            $updated = true;
        } else {
            $new_lines[] = $line;
        }
    }

    if ($updated) {
        file_put_contents($file, implode(PHP_EOL, $new_lines) . PHP_EOL, LOCK_EX);
    }

    return $updated;
}

/**
 * Deletes a task from the task list
 * 
 * @param string $task_id The ID of the task to delete.
 * @return bool True on success, false on failure.
 */
function deleteTask( string $task_id ): bool {
	$file  = __DIR__ . '/tasks.txt';
	// TODO: Implement this function
	if (!file_exists($file)) return false;

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $new_lines = [];
    $deleted = false;

    foreach ($lines as $line) {
        list($id, $name, $completed) = explode('|', $line);
        if ((int)$id === (int)$task_id) {
            $deleted = true; // Skip adding this line
        } else {
            $new_lines[] = $line;
        }
    }

    if ($deleted) {
        file_put_contents($file, implode(PHP_EOL, $new_lines) . PHP_EOL, LOCK_EX);
    }

    return $deleted;
}

/**
 * Generates a 6-digit verification code
 * 
 * @return string The generated verification code.
 */
function generateVerificationCode(): string {
	// TODO: Implement this function
	return str_pad((string)rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

/**
 * Subscribe an email address to task notifications.
 *
 * Generates a verification code, stores the pending subscription,
 * and sends a verification email to the subscriber.
 *
 * @param string $email The email address to subscribe.
 * @return bool True if verification email sent successfully, false otherwise.
 */
function subscribeEmail( string $email ): bool {
	 $pending_file = __DIR__ . '/pending_subscriptions.txt';
	// TODO: Implement this function
	$subscribers_file = __DIR__ . '/subscribers.txt';
	if (file_exists($subscribers_file)) {
        $subscribers = file($subscribers_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (in_array($email, $subscribers)) return false;
    }
	$code = generateVerificationCode();
    $entry = "$email|$code";
	file_put_contents($pending_file, $entry . PHP_EOL, FILE_APPEND | LOCK_EX);
	$verification_link = "http://yourdomain.com/verify.php?email=" . urlencode($email) . "&code=$code";
    $subject = "Verify your subscription";
    $message = "Click the link to verify your email:\n$verification_link";

    return mail($email, $subject, $message);
}

/**
 * Verifies an email subscription
 * 
 * @param string $email The email address to verify.
 * @param string $code The verification code.
 * @return bool True on success, false on failure.
 */
function verifySubscription(string $email, string $code): bool {
	$pending_file     = __DIR__ . '/pending_subscriptions.txt';
	$subscribers_file = __DIR__ . '/subscribers.txt';
	// TODO: Implement this function
	if (!file_exists($pending_file)) return false;

    $lines = file($pending_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $new_lines = [];
    $verified = false;
    foreach ($lines as $line) {
        list($e, $c) = explode('|', $line);
        if ($e === $email && $c === $code) {
            // Add to verified subscribers
            file_put_contents($subscribers_file, $email . PHP_EOL, FILE_APPEND | LOCK_EX);
            $verified = true;
        } else {
            $new_lines[] = $line;
        }
    }

    if ($verified) {
        file_put_contents($pending_file, implode(PHP_EOL, $new_lines) . PHP_EOL, LOCK_EX);
    }

    return $verified;
}
/**
 * Unsubscribes an email from the subscribers list
 * 
 * @param string $email The email address to unsubscribe.
 * @return bool True on success, false on failure.
 */
function unsubscribeEmail( string $email ): bool {
	 $file = __DIR__ . '/subscribers.txt';
	// TODO: Implement this function
	if (!file_exists($file)) return false;

    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $new_lines = [];
    $removed = false;

    foreach ($lines as $line) {
        if ($line !== $email) {
            $new_lines[] = $line;
        } else {
            $removed = true;
        }
    }

    if ($removed) {
        file_put_contents($file, implode(PHP_EOL, $new_lines) . PHP_EOL, LOCK_EX);
    }

    return $removed;
}

/**
 * Sends task reminders to all subscribers
 * Internally calls  sendTaskEmail() for each subscriber
 */
function sendTaskReminders(): void {
	$subscribers_file = __DIR__ . '/subscribers.txt';
	// TODO: Implement this function
	if (!file_exists($subscribers_file)) return;

    $subscribers = file($subscribers_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $all_tasks = getAllTasks();

    // Filter pending tasks (completed = 0)
    $pending_tasks = array_filter($all_tasks, fn($task) => $task[2] == 0);

    foreach ($subscribers as $email) {
        sendTaskEmail($email, $pending_tasks);
	}
}

/**
 * Sends a task reminder email to a subscriber with pending tasks.
 *
 * @param string $email The email address of the subscriber.
 * @param array $pending_tasks Array of pending tasks to include in the email.
 * @return bool True if email was sent successfully, false otherwise.
 */
function sendTaskEmail( string $email, array $pending_tasks ): bool {
	$subject = 'Task Planner - Pending Tasks Reminder';
	// TODO: Implement this function
	if (empty($pending_tasks)) return false;

    $subject = "⏰ Task Reminder: You have pending tasks";
    $body = "Hi,\n\nHere are your pending tasks:\n";

    foreach ($pending_tasks as $task) {
        $body .= "- " . $task[1] . "\n"; // task[1] = task name
    }

    // Generate unsubscribe link
    $unsubscribe_link = "http://yourdomain.com/unsubscribe.php?email=" . urlencode($email);
    $body .= "\n\nTo unsubscribe, click here:\n$unsubscribe_link";

    // Send the email (use proper headers in production)
    return mail($email, $subject, $body);
}
