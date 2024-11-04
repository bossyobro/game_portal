<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Application</title>
    <link rel="stylesheet" href="static/style.css">
</head>
<body>
    <div class="container">
        <h1>Welcome to Our Application</h1>
        <p>This is a simple application to demonstrate user authentication.</p>

        <?php if (isset($_SESSION['user_id'])): ?>
            <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>! You are logged in.</p>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <p>Please <a href="login.php">login</a> to access your account.</p>
        <?php endif; ?>
    </div>
</body>
</html>