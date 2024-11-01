<?php
session_start();
require 'db.php';
require 'PHPGangsta/GoogleAuthenticator.php'; // Include the library

$g = new PHPGangsta_GoogleAuthenticator();
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = $_POST['otp'];
    $username = $_SESSION['username']; // Retrieve username from session
    $secret = $_SESSION['google_auth_secret']; // Retrieve the secret from the session
    $password = $_SESSION['hashed_password']; // Retrieve the hashed password from the session

    // Verify the OTP using the secret stored in the session
    if ($g->verifyCode($secret, $otp)) {
        // If valid, save the user to the database
        $conn = getDbConnection();

        $stmt = $conn->prepare("INSERT INTO users (username, password, google_auth_secret) VALUES (?, ?, ?)");
        try {
            $stmt->execute([$username, $password, $secret]);
            
            // Log the user in by setting session variables
            $_SESSION['user_id'] = $conn->lastInsertId(); // Get the last inserted ID

            // Clear sensitive data from the session after successful registration
            unset($_SESSION['google_auth_secret'], $_SESSION['hashed_password']);

            // Redirect to the dashboard
            header("Location: dashboard.php");
            exit;
        } catch (PDOException $e) {
            $error = "Error saving user to the database.";
        }
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
        <?php if ($error): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>
    </form>
</body>
</html>
