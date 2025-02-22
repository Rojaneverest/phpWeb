<?php
include 'includes/db.php';

$confirmation = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['honeypot'])) {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $message = $conn->real_escape_string(trim($_POST['message']));

    
    if (!empty($name) && !empty($message)) {
        
        $conn->query("INSERT INTO contacts (name, email, message) VALUES ('$name', '$email', '$message')");
        
        $confirmation = 'Thank you for your message!';
        
        
        if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $to = 'admin@example.com';
            $subject = 'New Contact Form Submission';
            $body = "Name: $name\nEmail: $email\nMessage: $message";
            $headers = "From: $email";
            
            
            mail($to, $subject, $body, $headers);
        }
    } else {
        $confirmation = 'Please provide name and message.';
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Frontend Challenge</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="container">
        <section class="contact-form">
            <h2>Contact Us</h2>
            <?php if ($confirmation): ?>
                <p class="confirmation"><?php echo $confirmation; ?></p>
            <?php endif; ?>
            <form id="contact-form" method="POST">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email (Optional)</label>
                    <input type="email" id="email" name="email">
                </div>
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" required></textarea>
                </div>
                <!-- Honeypot (Anti-Spam) -->
                <input type="text" name="honeypot" class="honeypot" style="display: none;">
                <button type="submit">Send</button>
            </form>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="js/app.js" defer></script>
</body>
</html>