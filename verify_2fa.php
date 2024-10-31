<?php
session_start();
require 'db.php';
require 'PHPGangsta/GoogleAuthenticator.php';

$g = new PHPGangsta_GoogleAuthenticator();
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = $_POST['otp'];
    $username = $_SESSION['username'];
    $conn = getDbConnection();

    $stmt = $conn->prepare("SELECT id, google_auth_secret FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {

        if ($g->verifyCode($user['google_auth_secret'], $otp)) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username; 
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid OTP.";
        }
    } else {
        $error = "User not found.";
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
    <h2>Scan the QR Code and enter the OTP</h2>
    <img src="<?php echo $_SESSION['qrCodeUrl']; ?>" alt="QR Code">
    <form method="POST">
        <input type="text" name="otp" required placeholder="Enter OTP">
        <button type="submit">Verify</button>
        <?php if (isset($error)): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>
    </form>
</body>
</html>
