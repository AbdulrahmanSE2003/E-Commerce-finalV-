<?php
// session_start(); // For cart storage
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'ecommerce_db';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>