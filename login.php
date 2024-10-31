<?php
// login.php
session_start();
require 'db.php';

$error = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // Store user ID and username in the session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $username;

        // Store the user's Google Authenticator secret in session for verification
        $_SESSION['google_auth_secret'] = $user['google_auth_secret'];

        // Redirect to OTP verification page
        header("Location: verify_login.php");
        exit;
    } else {
        $error = "Invalid username or password.";
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
    </div>
</body>
</html>
