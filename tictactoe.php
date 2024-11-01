<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Tic Tac Toe</title>
    <link rel="stylesheet" href="static/style.css">
    <script src="tictactoe.js" defer></script>
</head>
<body>
    <div id="gameBoard"></div>
    <a href="dashboard.php">Back to Dashboard</a>
</body>
</html>
