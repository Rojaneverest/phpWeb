<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'includes/db.php';
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

<?php include 'includes/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logosipsum - Outsource Payment Collection</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <section id="home" class="banner">
    <div class="container banner-container">
        <div class="banner-content">
            <h1>Get instant cash flow with invoice factoring</h1>
            <p>Why wait? Get same-day funding and a faster way to access cash flow.</p>
            <button class="banner-btn">Get started</button>
            <div class="dots">
                <span class="dot active"></span>
                <span class="dot"></span>
                <span class="dot"></span>
            </div>
        </div>
        <div class="banner-graphics">
            <img src="assets/images/slider1.png" alt="Decorative graphics">
        </div>
    </div>
</section>

        <section class="payment-section">
            <h2>Outsource payment collection</h2>
            <p>Faster and flexible access to cash flow from one or all of your invoices.</p>
            <div class="icon-grid">
                <div class="icon-item">
                    <img src="assets/images/Shape.png" alt="Feature Icon" class="icon-img">
                    <h3>Access up to $100,000</h3>
                    <p>We fund each invoice once approved and collect payment to optimize your cash flow.*</p>
                </div>
                <div class="icon-item">
                    <img src="assets/images/Shape.png" alt="Feature Icon" class="icon-img">
                    <h3>You choose invoices to get paid</h3>
                    <p>Self-serve online or via texting 24/7, or connect from your CRM or invoicing platforms.</p>
                </div>
                <div class="icon-item">
                    <img src="assets/images/Shape.png" alt="Feature Icon" class="icon-img">
                    <h3>Simple pricing</h3>
                    <p>Only pay for what you use. There are no costs if you don’t need us.</p>
                </div>
                <div class="icon-item">
                    <img src="assets/images/Shape.png" alt="Feature Icon" class="icon-img">
                    <h3>Click and quick</h3>
                    <p>Only pay for what you use. There are no costs if you don’t need us.</p>
                </div>
                <div class="icon-item">
                    <img src="assets/images/Shape.png" alt="Feature Icon" class="icon-img">
                    <h3>Flexible</h3>
                    <p>Only pay for what you use. There are no costs if you don’t need us.</p>
                </div>
                <div class="icon-item">
                    <img src="assets/images/Shape.png" alt="Feature Icon" class="icon-img">
                    <h3>Invest in your business</h3>
                    <p>Only pay for what you use. There are no costs if you don’t need us.</p>
                </div>
            </div>
        </section>

        <section class="task-manager-section">
            <h2>Task Manager</h2>
            <p>Your daily to-do list</p>
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

        <?php
        $confirmation = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['honeypot'])) {
            $name = $conn->real_escape_string(trim($_POST['name']));
            $email = $conn->real_escape_string(trim($_POST['email']));
            $message = $conn->real_escape_string(trim($_POST['message']));

            if (!empty($name) && !empty($email) && !empty($message) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $conn->query("INSERT INTO contacts (name, email, message) VALUES ('$name', '$email', '$message')");

                $to = 'rojan@rojan.com';
                $subject = 'New Contact Form Submission';
                $body = "Name: $name\nEmail: $email\nMessage: $message";
                $headers = "From: $email";
                mail($to, $subject, $body, $headers);

                $confirmation = 'Thank you for your message!';
            } else {
                $confirmation = 'Please fill in all fields correctly.';
            }
        }
        ?>
        <section class="contact-section">
            <h2>Contact Us</h2>
            <p>Speak with our team to see how we can help your business.</p>
            <?php if ($confirmation): ?>
                <p class="confirmation"><?php echo $confirmation; ?></p>
            <?php endif; ?>
            <form id="contact-form" method="POST">
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="message">Business or Company Name</label>
                    <textarea id="message" name="message" required></textarea>
                </div>
                <input type="text" name="honeypot" class="honeypot" style="display: none;">
                <button type="submit">Submit</button>
            </form>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="js/app.js" defer></script>
</body>
</html>