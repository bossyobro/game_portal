<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Our Game Portal</title>
    <link rel="stylesheet" href="static/style.css">
</head>
<body>
    <div class="container">
        <h1>Welcome to Our Game Portal</h1>
        <p>This portal offers a variety of mini games for your enjoyment.</p>
        <p>Log in to access your profile and track your scores!</p>

        <?php if (isset($_SESSION['user_id'])): ?>
            <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>! You are logged in.</p>
            <nav>
                <a href="dashboard.php">Dashboard</a>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            </nav>
        <?php else: ?>
            <p>Please <a href="login.php">login</a> or <a href="register.php">register</a> to access your account.</p>
        <?php endif; ?>
    </div>
</body>
</html>