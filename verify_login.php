<?php
session_start();
require 'db.php';
require 'PHPGangsta/GoogleAuthenticator.php'; // Include the library

$g = new PHPGangsta_GoogleAuthenticator();
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = $_POST['otp'];

    // Get the Google Authenticator secret from the session
    $secret = $_SESSION['google_auth_secret'];

    // Verify the OTP using the secret stored in the session
    if ($g->verifyCode($secret, $otp)) {
        // If valid, log the user in by setting session variables
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid OTP.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Verify 2FA</title>
    <link rel="stylesheet" href="static/style.css">
</head>
<body>
    <h2>Enter the OTP from Google Authenticator</h2>
    <form method="POST">
        <input type="text" name="otp" required placeholder="Enter OTP">
        <button type="submit">Verify</button>
        <?php if ($error): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>
    </form>
</body>
</html>
