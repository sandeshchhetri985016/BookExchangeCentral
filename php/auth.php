<?php
session_start();
require_once 'db_connect.php';

function registerUser($username, $email, $password) {
    global $conn;
    
    // Validate input
    if (empty($username) || empty($email) || empty($password)) {
        return "All fields are required.";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email format.";
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Prepare and execute statement
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashedPassword);
    
    if ($stmt->execute()) {
        return "Registration successful.";
    } else {
        return "Error: " . $stmt->error;
    }
}

function loginUser($email, $password) {
    global $conn;
    
    // Validate input
    if (empty($email) || empty($password)) {
        return "Email and password are required.";
    }
    
    // Prepare and execute statement
    $stmt = $conn->prepare("SELECT userId, username, password, userRole FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['userId'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['userRole'];
            return "Login successful.";
        } else {
            return "Invalid password.";
        }
    } else {
        return "User not found.";
    }
}

function logoutUser() {
    session_unset();
    session_destroy();
    return "Logout successful.";
}

// Usage example
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['register'])) {
        echo registerUser($_POST['username'], $_POST['email'], $_POST['password']);
    } elseif (isset($_POST['login'])) {
        echo loginUser($_POST['email'], $_POST['password']);
    } elseif (isset($_POST['logout'])) {
        echo logoutUser();
    }
}
include 'header.php'; ?>
<?php include 'footer.php'; ?>
