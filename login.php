<?php
// login.php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = $_POST['password'];

        if (empty($username) || empty($password)) {
            throw new Exception('All fields are required');
        }

        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            $_SESSION['google_auth_secret'] = $user['google_auth_secret'];
            
            header("Location: verify_login.php");
            exit;
        } else {
            throw new Exception('Invalid credentials');
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" href="static/style.css">
</head>
<body>
    <div class="form-container">
        <form method="POST" action="">
            <h2>Login</h2>
            <input type="text" name="username" required placeholder="Username">
            <input type="password" name="password" required placeholder="Password">
            <button type="submit">Login</button>
            <?php if ($error): ?>
                <p style="color:red;"><?php echo $error; ?></p>
            <?php endif; ?>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a>.</p>
    </div>
</body>
</html>