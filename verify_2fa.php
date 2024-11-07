<?php
// verify_2fa.php
session_start();
require 'db.php';
require 'PHPGangsta/GoogleAuthenticator.php';

$g = new PHPGangsta_GoogleAuthenticator();
$error = '';

// Check if the QR code URL exists in session
$qrCodeUrl = isset($_SESSION['qrCodeUrl']) ? $_SESSION['qrCodeUrl'] : null;
$secret = isset($_SESSION['google_auth_secret']) ? $_SESSION['google_auth_secret'] : null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = $_POST['otp'];

    if (!isset($_SESSION['google_auth_secret'])) {
        header("Location: login.php");
        exit;
    }
    
    if ($g->verifyCode($secret, $otp)) {
        $_SESSION['authenticated'] = true;
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
    <div class="form-container">
        <?php if ($qrCodeUrl): ?>
            <h2>Scan this QR Code with Google Authenticator</h2>
            <img src="<?php echo htmlspecialchars($qrCodeUrl); ?>" alt="QR Code">
            <p>Or enter this code manually: <?php echo htmlspecialchars($secret); ?></p>
        <?php endif; ?>
        
        <h2>Enter the OTP from Google Authenticator</h2>
        <form method="POST">
            <input type="text" name="otp" required placeholder="Enter OTP">
            <button type="submit">Verify</button>
            <?php if ($error): ?>
                <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>