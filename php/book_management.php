<?php
session_start();
require_once 'db_connect.php';

function addBook($title, $author, $condition, $description, $price, $image_url) {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        return "User must be logged in to add a book.";
    }
    
    // Validate input
    if (empty($title) || empty($author) || empty($condition) || empty($price)) {
        return "Title, author, condition, and price are required.";
    }
    
    // Prepare and execute statement
    $stmt = $conn->prepare("INSERT INTO books (title, author, condition, description, price, image_url, userId) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssdsi", $title, $author, $condition, $description, $price, $image_url, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        return "Book added successfully.";
    } else {
        return "Error: " . $stmt->error;
    }
}

function updateBook($bookId, $title, $author, $condition, $description, $price, $image_url) {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        return "User must be logged in to update a book.";
    }
    
    // Validate input
    if (empty($bookId) || empty($title) || empty($author) || empty($condition) || empty($price)) {
        return "Book ID, title, author, condition, and price are required.";
    }
    
    // Prepare and execute statement
    $stmt = $conn->prepare("UPDATE books SET title = ?, author = ?, condition = ?, description = ?, price = ?, image_url = ? WHERE bookId = ? AND userId = ?");
    $stmt->bind_param("ssssdsii", $title, $author, $condition, $description, $price, $image_url, $bookId, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        return "Book updated successfully.";
    } else {
        return "Error: " . $stmt->error;
    }
}

function deleteBook($bookId) {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        return "User must be logged in to delete a book.";
    }
    
    // Prepare and execute statement
    $stmt = $conn->prepare("DELETE FROM books WHERE bookId = ? AND userId = ?");
    $stmt->bind_param("ii", $bookId, $_SESSION['user_id']);
    
    if ($stmt->execute()) {
        return "Book deleted successfully.";
    } else {
        return "Error: " . $stmt->error;
    }
}

function getBooks($limit = 10, $offset = 0) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM books ORDER BY postedDate DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $limit, $offset);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }
    
    return $books;
}

// Usage example
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_book'])) {
        echo addBook($_POST['title'], $_POST['author'], $_POST['condition'], $_POST['description'], $_POST['price'], $_POST['image_url']);
    } elseif (isset($_POST['update_book'])) {
        echo updateBook($_POST['book_id'], $_POST['title'], $_POST['author'], $_POST['condition'], $_POST['description'], $_POST['price'], $_POST['image_url']);
    } elseif (isset($_POST['delete_book'])) {
        echo deleteBook($_POST['book_id']);
    }
}
include 'header.php'; ?>
<?php include 'footer.php'; ?>
