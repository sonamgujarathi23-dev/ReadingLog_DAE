<?php
date_default_timezone_set('America/New_York'); // change if your time zone is different
$host = '127.0.0.1';
$port = 3306;          // change to 8889 if your MAMP MySQL port is 8889
$db   = 'reading_log';
$user = 'root';
$pass = 'root';        // MAMP default password

try {
  $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die('Database connection failed: ' . $e->getMessage());
}
