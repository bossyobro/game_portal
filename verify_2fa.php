<?php
session_start();
require 'db.php';
require 'PHPGangsta/GoogleAuthenticator.php'; // Include the library

$g = new PHPGangsta_GoogleAuthenticator();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = $_POST['otp'];
    $username = $_SESSION['username']; // Get the username from session
    $conn = getDbConnection();

    // Fetch the user's Google Auth secret
    $stmt = $conn->prepare("SELECT google_auth_secret FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Verify the OTP
    if ($g->verifyCode($user['google_auth_secret'], $otp)) {
        // If valid, log the user in
        $_SESSION['user_id'] = $user['id']; // Set session user ID
        $_SESSION['username'] = $username; // Set session username
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
