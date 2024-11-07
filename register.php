<?php
// register.php
session_start();
require 'db.php';
require 'PHPGangsta/GoogleAuthenticator.php';

$g = new PHPGangsta_GoogleAuthenticator();
$error = '';

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
            
            // Redirect to 2FA verification
            header("Location: verify_2fa.php");
            exit;
        }
    } catch (PDOException $e) {
        $error = "Registration failed: " . $e->getMessage();
    }
}
?>