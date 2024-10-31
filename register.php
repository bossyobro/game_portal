<?php
// register.php
session_start();
require 'db.php';
require 'PHPGangsta/GoogleAuthenticator.php'; // Include the PhPGangsta library

$g = new PHPGangsta_GoogleAuthenticator();
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password

    $conn = getDbConnection();
    
    // Generate a secret key
    $secret = $g->createSecret();

    $stmt = $conn->prepare("INSERT INTO users (username, password, google_auth_secret) VALUES (?, ?, ?)");
    
    try {
        $stmt->execute([$username, $password, $secret]);
        
        // Generate QR code URL
        $qrCodeUrl = $g->getQRCodeGoogleUrl($username, $secret, 'YourAppName');

        // Store the QR code URL in the session
        $_SESSION['qrCodeUrl'] = $qrCodeUrl;

        header("Location: verify_2fa.php");
        exit;
    } catch (PDOException $e) {
        $error = "Username already exists.";
    }
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
