<?php
// db.php

function getDbConnection() {
    $host = 'localhost';
    $dbname = 'game_portal';
    $username = 'game_user';
    $password = 'key';

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        exit;
    }
}

