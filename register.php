<?php
// register.php
session_start();
require 'db.php';
require 'PHPGangsta/GoogleAuthenticator.php';

$g = new PHPGangsta_GoogleAuthenticator();
$error = '';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    // Username validation
    if (strlen($username) < 3 || strlen($username) > 20) {
        $error = "Username must be between 3 and 20 characters";
    }
    
    // Password strength validation
    if (strlen($password) < 8 || 
        !preg_match("#[0-9]+#", $password) || 
        !preg_match("#[a-zA-Z]+#", $password)) {
        $error = "Password must be at least 8 characters and include both letters and numbers";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    
    try {
        $conn = getDbConnection();
        
        // Check if username already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            $error = "Username already exists";
        } else {
            // Generate a secret for Google Authenticator
            $secret = $g->createSecret();
            
            // Store user with the secret
            $stmt = $conn->prepare("INSERT INTO users (username, password, google_auth_secret) VALUES (?, ?, ?)");
            $stmt->execute([$username, $password, $secret]);
            
            // Get the user ID
            $userId = $conn->lastInsertId();
            
            // Set session variables
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;
            $_SESSION['google_auth_secret'] = $secret;
            $_SESSION['qrCodeUrl'] = $g->getQRCodeGoogleUrl($username, $secret, "GamePortal");
            header("Location: verify_2fa.php");
            exit;
        }
    } catch (PDOException $e) {
        $error = "Registration failed: " . $e->getMessage();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Game Portal</title>
    <link rel="stylesheet" href="static/style.css">
</head>
<body>
    <div class="form-container">
        <h2>Register for Game Portal</h2>
        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit">Register</button>
        </form>
        <?php if ($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
    </div>
</body>
</html>