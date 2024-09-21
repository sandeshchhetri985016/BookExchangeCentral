<?php
session_start();
require_once 'auth.php';
require_once 'book_management.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_book'])) {
        $result = addBook($_POST['title'], $_POST['author'], $_POST['condition'], $_POST['description'], $_POST['price'], $_POST['image_url']);
    } elseif (isset($_POST['update_book'])) {
        $result = updateBook($_POST['book_id'], $_POST['title'], $_POST['author'], $_POST['condition'], $_POST['description'], $_POST['price'], $_POST['image_url']);
    } elseif (isset($_POST['delete_book'])) {
        $result = deleteBook($_POST['book_id']);
    }
}

// Get user's books
$userBooks = getBooks(10, 0, $_SESSION['user_id']);
include 'header.php'; ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Your head content -->
</head>
<body>
    <header>
        <!-- Your header content -->
    </header>

    <main>
        <h2>My Listings</h2>
        <!-- Form to add a new book -->
        <form action="listings.php" method="post">
            <!-- Add form fields for book details -->
            <button type="submit" name="add_book">Add Book</button>
        </form>

        <!-- Display user's books -->
        <?php foreach ($userBooks as $book): ?>
            <div class="book-item">
                <!-- Display book details -->
                <form action="listings.php" method="post">
                    <input type="hidden" name="book_id" value="<?php echo $book['bookId']; ?>">
                    <!-- Add form fields for updating book details -->
                    <button type="submit" name="update_book">Update</button>
                </form>
                <form action="listings.php" method="post">
                    <input type="hidden" name="book_id" value="<?php echo $book['bookId']; ?>">
                    <button type="submit" name="delete_book">Delete</button>
                </form>
            </div>
        <?php endforeach; ?>
    </main>

    <footer>
        <!-- Your footer content -->
    </footer>
</body>
</html>
<?php include 'footer.php'; ?>
