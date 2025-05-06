<?php
session_start();
include 'config.php';

// // Check if the user is already logged in
// if (isset($_SESSION['user_id'])) {
//     header('Location: index.php');
//     exit();
// }

// Database connection details - should be in config.php
$host = 'localhost';
$username = 'root'; // Update with your database username
$password = ''; // Update with your database password if needed
$dbname = 'ecommerce_db'; // Your database name

// Handle form submission for login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login-email']) && isset($_POST['login-password'])) {
    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $email = $conn->real_escape_string($_POST['login-email']);
    $password = $_POST['login-password'];

    $password_hash = sha1($password);

    // Query to check user credentials
    $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify the password
        if ($user['password_hash'] === $password_hash) {
            // Set session for logged in user
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            // Check if user is admin
            if (isset($user['role']) && $user['role'] === 'admin') {
                header("Location: admin-dash.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            // Password incorrect
            $_SESSION['error'] = 'Incorrect password';
            header("Location: login.php");
            exit();
        }
    } else {
        // User not found
        $_SESSION['error'] = 'User not found';
        header("Location: login.php");
        exit();
    }
} else if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['signup-email']) && isset($_POST['signup-password'])) {
    $conn = new mysqli($host, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get and sanitize input
    $username = $conn->real_escape_string($_POST['signup-name']); // Changed variable name to $username
    $email = $conn->real_escape_string($_POST['signup-email']);
    $phone = $conn->real_escape_string($_POST['signup-phone']);
    $password = $_POST['signup-password'];

    // Validate input
    if (empty($username) || empty($email) || empty($password)) {
        $_SESSION['error'] = 'Please fill all required fields';
        header("Location: signup.php");
        exit();
    }

    // Check if email exists
    $sql = "SELECT id FROM users WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error'] = 'This email is already registered';
        header("Location: signup.php");
        $stmt->close();
        $conn->close();
        exit();
    }
    $stmt->close();

    // Insert new user with correct column names
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (username, email, phone, password_hash) VALUES (?, ?, ?, ?)"; // Changed 'name' to 'username'
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $email, $phone, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['user_name'] = $username;
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['error'] = 'Error registering user. Please try again.';
        header("Location: signup.php");
    }

    $stmt->close();
    $conn->close();
}
?>