<?php
session_start();
require_once 'db_connect.php';

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function getAllUsers() {
    global $conn;
    
    if (!isAdmin()) {
        return "Access denied. Admin privileges required.";
    }
    
    $stmt = $conn->prepare("SELECT userId, username, email, userRole FROM users");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    
    return $users;
}

function changeUserRole($userId, $newRole) {
    global $conn;
    
    if (!isAdmin()) {
        return "Access denied. Admin privileges required.";
    }
    
    $stmt = $conn->prepare("UPDATE users SET userRole = ? WHERE userId = ?");
    $stmt->bind_param("si", $newRole, $userId);
    
    if ($stmt->execute()) {
        return "User role updated successfully.";
    } else {
        return "Error: " . $stmt->error;
    }
}

function deleteUser($userId) {
    global $conn;
    
    if (!isAdmin()) {
        return "Access denied. Admin privileges required.";
    }
    
    $stmt = $conn->prepare("DELETE FROM users WHERE userId = ?");
    $stmt->bind_param("i", $userId);
    
    if ($stmt->execute()) {
        return "User deleted successfully.";
    } else {
        return "Error: " . $stmt->error;
    }
}

function getSystemStats() {
    global $conn;
    
    if (!isAdmin()) {
        return "Access denied. Admin privileges required.";
    }
    
    $stats = [];
    
    // Total users
    $stmt = $conn->prepare("SELECT COUNT(*) as total_users FROM users");
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['total_users'] = $result->fetch_assoc()['total_users'];
    
    // Total books
    $stmt = $conn->prepare("SELECT COUNT(*) as total_books FROM books");
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['total_books'] = $result->fetch_assoc()['total_books'];
    
    // Total transactions
    $stmt = $conn->prepare("SELECT COUNT(*) as total_transactions FROM transactions");
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['total_transactions'] = $result->fetch_assoc()['total_transactions'];
    
    return $stats;
}

// Usage example
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['get_users'])) {
        $users = getAllUsers();
        echo json_encode($users);
    } elseif (isset($_GET['get_stats'])) {
        $stats = getSystemStats();
        echo json_encode($stats);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['change_role'])) {
        echo changeUserRole($_POST['user_id'], $_POST['new_role']);
    } elseif (isset($_POST['delete_user'])) {
        echo deleteUser($_POST['user_id']);
    }
}
include 'header.php'; ?>
<?php include 'footer.php'; ?>
