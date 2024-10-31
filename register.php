<?php
// register.php
session_start();
require 'db.php';
require 'PHPGangsta/GoogleAuthenticator.php'; // Include the library

$g = new PHPGangsta_GoogleAuthenticator(); // Initialize the Google Authenticator
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password

    // Generate a secret for Google Authenticator
    $secret = $g->createSecret();

    // Store the secret in the session for the verification phase
    $_SESSION['google_auth_secret'] = $secret;
    $_SESSION['username'] = $username; // Set username in session for verification

    // Generate QR code URL for Google Authenticator
    $_SESSION['qrCodeUrl'] = $g->getQRCodeGoogleUrl($username, $secret);

    // Redirect to the OTP verification page
    header("Location: verify_2fa.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register</title>
    <link rel="stylesheet" href="static/style.css">
</head>
<body>
    <div class="form-container">
        <form method="POST" action="">
            <h2>Register</h2>
            <input type="text" name="username" required placeholder="Username">
            <input type="password" name="password" required placeholder="Password">
            <button type="submit">Register</button>
            <?php if ($error): ?>
                <p style="color:red;"><?php echo $error; ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
