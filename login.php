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

    // Debugging outputs
    if ($user) {
        echo "Stored hash: " . $user['password'] . "<br>";
        echo "Entered password: " . $password . "<br>";

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Store user ID and username in the session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            $_SESSION['authenticated'] = false;
            // Store the user's Google Authenticator secret in session for verification
            $_SESSION['google_auth_secret'] = $user['google_auth_secret'];

            // Redirect to OTP verification page
            header("Location: verify_login.php");
            exit;
        } else {
            $error = "Password verification failed. Please check the entered password.";
        }
    } else {
        $error = "User  not found. Please check the username.";
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