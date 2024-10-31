<?php
session_start();
require 'db.php';
require 'PHPGangsta/GoogleAuthenticator.php'; // Include the library

$g = new PHPGangsta_GoogleAuthenticator();
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = $_POST['otp'];
    $username = $_SESSION['username']; // Get the username from the session
    $secret = $_SESSION['google_auth_secret']; // Get the secret from the session

    // Verify the OTP using the secret stored in the session
    if ($g->verifyCode($secret, $otp)) {
        // If valid, save the user to the database
        $conn = getDbConnection();
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password

        $stmt = $conn->prepare("INSERT INTO users (username, password, google_auth_secret) VALUES (?, ?, ?)");
        try {
            $stmt->execute([$username, $password, $secret]);
            
            // Log the user in by setting session variables
            $_SESSION['user_id'] = $conn->lastInsertId(); // Get the last inserted ID
            $_SESSION['username'] = $username; // Set session username

            // Clear the secret from session after successful registration
            unset($_SESSION['google_auth_secret']);
            
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
        <input type="hidden" name="password" value="<?php echo htmlspecialchars($_POST['password']); ?>"> <!-- Pass the password to save -->
        <button type="submit">Verify</button>
        <?php if (isset($error)): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>
    </form>
</body>
</html>
