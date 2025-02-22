<?php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $response = ['success' => false];

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $task_name = $conn->real_escape_string($_POST['task_name']);
                $conn->query("INSERT INTO tasks (task_name) VALUES ('$task_name')");
                $response['success'] = true;
                $response['id'] = $conn->insert_id;
                break;

            case 'complete':
                $id = (int)$_POST['id'];
                $result = $conn->query("SELECT completed FROM tasks WHERE id = $id");
                $completed = $result->fetch_assoc()['completed'] ? 0 : 1;
                $conn->query("UPDATE tasks SET completed = $completed WHERE id = $id");
                $response['success'] = true;
                break;

            case 'delete':
                $id = (int)$_POST['id'];
                $conn->query("DELETE FROM tasks WHERE id = $id");
                $response['success'] = true;
                break;
        }
    } elseif (isset($_POST['action']) && $_POST['action'] === 'reorder') {
        $data = json_decode(file_get_contents('php://input'), true);
        foreach ($data['order'] as $item) {
            $id = (int)$item['id'];
            $order = (int)$item['order'];
            $conn->query("UPDATE tasks SET task_order = $order WHERE id = $id");
        }
        $response['success'] = true;
    }

    echo json_encode($response);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks - Frontend Challenge</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="container">
        <section class="task-manager">
            <h2>Task Manager</h2>
            <form id="task-form" class="task-form">
                <input type="text" id="task-input" name="task_name" placeholder="Enter a task" required>
                <button type="submit">Add Task</button>
            </form>
            <ul id="task-list" class="task-list">
                <?php
                $result = $conn->query("SELECT * FROM tasks ORDER BY task_order ASC");
                while ($row = $result->fetch_assoc()) {
                    $completed = $row['completed'] ? 'completed' : '';
                    echo "
                        <li class='task-item $completed' data-id='{$row['id']}'>
                            <span>{$row['task_name']}</span>
                            <button class='complete-btn'>✓</button>
                            <button class='delete-btn'>✗</button>
                        </li>
                    ";
                }
                $conn->close();
                ?>
            </ul>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="js/app.js" defer></script>
</body>
</html>