<?php
include 'includes/functions.php'; // Load utility functions
checkSession(); // Ensure user is logged in

$conn = getDBConnection(); // Get database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = sanitizeInput($_POST['title']);
    $author = sanitizeInput($_POST['author']);
    $price = sanitizeInput($_POST['price']);
    
    $user_id = $_SESSION['user_id'];

    // Handle the file upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = 'assets/bookimg/';
        $target_file = $target_dir . basename($_FILES['image']['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the file is an actual image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check !== false) {
            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = $target_file; // Store the file path for database insertion
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            echo "File is not an image.";
        }
    }

    // Insert into the database
    $sql = "INSERT INTO listings (title, author, price, image, user_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsi", $title, $author, $price, $image_path, $user_id);
    $stmt->execute();
    $stmt->close();

    // Redirect to the same page to prevent form resubmission
    header('Location: listings.php');
    exit(); // Always use exit after a header redirect
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css"> <!-- Ensure consistent loading of CSS -->
    <title>Create Listing - BookExchangeCentral</title>
</head>
<body>
    
    <?php include 'includes/header.php'; ?> <!-- Wrap header in <header> tag for consistency -->

    <main>
        <div class="container">
            <!-- Back buttons -->
            <button onclick="history.back()" class="btn">Go Back</button> <!-- JavaScript Back Button -->
            
            <!-- Your Current Listings Section -->
            <h2>Your Current Listings</h2>
            <div class="listings-container">
                <?php
                $user_id = $_SESSION['user_id'];
                $sql = "SELECT id, title, author, price, image FROM listings WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $stmt->bind_result($listing_id, $title, $author, $price, $image);

                while ($stmt->fetch()): ?>
                    <div class="listing-item">
                        <a href="messaging.php?listing_id=<?php echo $listing_id; ?>">
                            <div class="image-wrapper">
                                <?php if ($image): ?>
                                    <img src="<?php echo $image; ?>" alt="<?php echo $title; ?>" class="listing-image">
                                <?php endif; ?>
                            </div>
                            <div class="text-wrapper">
                                <h3><?php echo $title; ?></h3>
                                <p>by <?php echo $author; ?></p>
                                <p>Price: $<?php echo number_format($price, 2); ?></p>
                            </div>
                        </a>
                    </div>
                <?php endwhile;
                $stmt->close();
                ?>
            </div>

            <!-- Create a New Book Listing Form -->
            <h2>Create a New Book Listing</h2>
            <form method="POST" action="listings.php" enctype="multipart/form-data">
                <label for="title">Book Title:</label>
                <input type="text" id="title" name="title" required>

                <label for="author">Author:</label>
                <input type="text" id="author" name="author" required>

                <label for="price">Price ($):</label>
                <input type="number" id="price" name="price" required>

                <label for="image">Upload Book Image:</label>
                <input type="file" id="image" name="image" accept="image/*" required>

                <button type="submit">Create Listing</button>
            </form>
        </div>
    </main>

    <footer>
        <?php include 'includes/footer.php'; ?> <!-- Ensure consistent footer inclusion -->
    </footer>
</body>
</html>
