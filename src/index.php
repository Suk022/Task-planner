<?php
require_once 'functions.php';

$message = isset($_GET['message']) ? $_GET['message'] : '';
$message_type = isset($_GET['type']) ? $_GET['type'] : '';

//handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['task-name'])) {
        $task_name = trim($_POST['task-name']);
        if (!empty($task_name)) {
            addTask($task_name);
        }
    }
    
    if (isset($_POST['email'])) {
        $email = trim($_POST['email']);
        if (!empty($email)) {
            $result = subscribeEmail($email);
            // Redirect to avoid resubmission on refresh
            header('Location: index.php?message=' . urlencode($result['message']) . '&type=' . ($result['success'] ? 'success' : 'error'));
            exit;
        }
    }
}

// Process AJAX requests
if (isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'mark_completed':
            if (isset($_POST['task_id']) && isset($_POST['completed'])) {
                markTaskAsCompleted($_POST['task_id'], $_POST['completed'] === 'true');
            }
            break;
            
        case 'delete_task':
            if (isset($_POST['task_id'])) {
                deleteTask($_POST['task_id']);
            }
            break;
    }
    
    exit(json_encode(['success' => true]));
}

$tasks = getAllTasks();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Planner</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .task-item {
            display: flex;
            align-items: center;
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .task-item.completed {
            background-color: #f0f0f0;
            text-decoration: line-through;
        }
        .task-item input[type="checkbox"] {
            margin-right: 10px;
        }
        .delete-task {
            margin-left: auto;
            color: red;
            cursor: pointer;
        }
        .section {
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .message {
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
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
    <div class="container">
        <h1>Task Planner</h1>
        
        <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <?php endif; ?>
        
        <div class="task-section">
            <div class="section">
                <h2>Add New Task</h2>
                <form method="POST">
                    <input type="text" name="task-name" id="task-name" placeholder="Enter new task" required>
                    <button type="submit" id="add-task">Add Task</button>
                </form>
            </div>
            
            <div class="section">
                <h2>Task List</h2>
                <ul class="tasks-list">
                    <?php foreach ($tasks as $task): ?>
                    <li class="task-item <?php echo $task['completed'] ? 'completed' : ''; ?>" data-task-id="<?php echo htmlspecialchars($task['id']); ?>">
                        <input type="checkbox" class="task-status" <?php echo $task['completed'] ? 'checked' : ''; ?>>
                        <span class="task-name"><?php echo htmlspecialchars($task['name']); ?></span>
                        <button class="delete-task">Delete</button>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="section">
                <h2>Subscribe to Task Reminders</h2>
                <form method="POST">
                    <input type="email" name="email" placeholder="Enter your email" required>
                    <button type="submit" id="submit-email">Subscribe</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            //Handling task completion
            document.querySelectorAll('.task-status').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const taskItem = this.closest('.task-item');
                    const taskId = taskItem.dataset.taskId;
                    
                    fetch('index.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=mark_completed&task_id=${taskId}&completed=${this.checked}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            taskItem.classList.toggle('completed', this.checked);
                        }
                    });
                });
            });
            
            //Handling task deletion
            document.querySelectorAll('.delete-task').forEach(button => {
                button.addEventListener('click', function() {
                    const taskItem = this.closest('.task-item');
                    const taskId = taskItem.dataset.taskId;
                    
                    fetch('index.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=delete_task&task_id=${taskId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            taskItem.remove();
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>
