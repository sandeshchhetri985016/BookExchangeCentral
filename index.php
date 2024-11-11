<?php
include 'includes/functions.php'; // Load utility functions
checkSession(); // Ensure user is logged in
$conn = getDBConnection(); // Get database connection

// Handle the search functionality
$search_query = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = sanitizeInput($_GET['search']); // Sanitize user input to prevent SQL injection
    // Modify the SQL query to filter based on the search input
    $sql = "SELECT id, title, author, price, image FROM listings WHERE title LIKE ? OR author LIKE ?";
    $stmt = $conn->prepare($sql);
    $like_search = '%' . $search_query . '%';
    $stmt->bind_param("ss", $like_search, $like_search); // Bind the sanitized input to the query
} else {
    // If no search query, show all listings
    $sql = "SELECT id, title, author, price, image FROM listings";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$listings_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookExchangeCentral - Listings</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    
        <?php include 'includes/header.php'; ?>
    

    <div class="container">
        <!-- Back buttons -->
        <button onclick="history.back()" class="btn">Go Back</button> <!-- JavaScript Back Button -->
        <h2>Welcome, <?php echo $_SESSION['user_name']; ?>!</h2>

        
        

        <!-- Display search results or all listings -->
        <?php if (!empty($search_query)): ?>
            <h2>Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h2>
        <?php else: ?>
            <h2>Available Listings</h2>
        <?php endif; ?>

        <div class="listings-container">
        <?php if ($listings_result->num_rows > 0): ?>
            <?php while ($listing = $listings_result->fetch_assoc()): ?>
                <div class="listing-item">
                    <a href="messaging.php?listing_id=<?php echo $listing['id']; ?>">
                        <div class="image-wrapper">
                            <img src="<?php echo $listing['image']; ?>" alt="<?php echo $listing['title']; ?>" class="listing-image">
                        </div>
                        <div class="text-wrapper">
                            <h3><?php echo $listing['title']; ?></h3>
                            <p>Author: <?php echo $listing['author']; ?></p>
                            <p>Price: $<?php echo number_format($listing['price'], 2); ?></p>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No listings found<?php echo !empty($search_query) ? ' for "' . htmlspecialchars($search_query) . '"' : ''; ?>.</p>
        <?php endif; ?>
        </div>
    </div>

    <footer>
        <?php include 'includes/footer.php'; ?>
    </footer>
</body>
</html>
