<?php
// Utility functions for common tasks

// Function to sanitize input data
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Function to validate session
function checkSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id'])) {
        header('Location: auth.php');
        exit();
    }
}

// Function to connect to the database
function getDBConnection() {
    include 'db_connect.php'; // Ensure this file sets up a PDO connection $conn
    return $conn;
}

// Function to log in a user
function loginUser($conn, $email, $password) {
    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
    
    // Bind the parameter to the statement
    $stmt->bind_param("s", $email);
    
    // Execute the statement
    $stmt->execute();
    
    // Store the result
    $stmt->store_result();
    
    // Check if a row was returned
    if ($stmt->num_rows > 0) {
        // Bind the result to variables
        $stmt->bind_result($id, $name, $hashed_password, $role);
        $stmt->fetch();
        
        // Verify the password
        if (password_verify($password, $hashed_password)) {
            // Start the session and set session variables
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_role'] = $role;
            return true; // Login successful
        }
    }
    return false; // Login failed
}


// Function to sign up a user
function signupUser($conn, $name, $email, $password, $role) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password before saving
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    // Bind the parameters to the prepared statement
    $stmt->bind_param("ssss", $name, $email, $password, $role);
    
    // Execute the statement
    return $stmt->execute(); // No arguments needed here
}

// Function to fetch all users
function fetchAllUsers($conn) {
    $stmt = $conn->prepare("SELECT * FROM users");
    $stmt->execute();
    $result = $stmt->get_result(); // Get the result set from the statement
    return $result->fetch_all(MYSQLI_ASSOC); // Fetch all rows as an associative array
}


// Function to fetch all listings
function fetchAllListings($conn) {
    $stmt = $conn->prepare("SELECT * FROM listings");
    $stmt->execute();
    $result = $stmt->get_result(); // Get the result set from the statement
    return $result->fetch_all(MYSQLI_ASSOC); // Fetch all rows as an associative array
}


// Function to fetch all messages
function fetchAllMessages($conn) {
    $stmt = $conn->prepare("SELECT * FROM messages");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to fetch all transactions
function fetchAllTransactions($conn) {
    $stmt = $conn->prepare("SELECT * FROM transactions");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Function to fetch all items in the cart
function fetchUserCart($conn, $user_id) {
    $stmt = $conn->prepare("SELECT c.*, l.title FROM cart c JOIN listings l ON c.listing_id = l.id WHERE c.user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Additional utility functions for counting entries
function countUsers($conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM users");
    $stmt->execute();
    $result = $stmt->get_result(); // Get the result set from the statement
    $row = $result->fetch_assoc(); // Fetch the row as an associative array
    return $row['total']; // Return the total count
}


function countListings($conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM listings");
    $stmt->execute();
    $result = $stmt->get_result(); // Get the result set from the statement
    $row = $result->fetch_assoc(); // Fetch the row as an associative array
    return $row['total']; // Return the total count
}


function countActiveListings($conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM listings");
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'];
}



?>
