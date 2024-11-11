<?php
include 'includes/header.php';
include 'includes/functions.php';

$conn = getDBConnection();
$search_term = sanitizeInput($_GET['search'] ?? '');

$sql = "SELECT title, author, price FROM listings WHERE title LIKE ? OR author LIKE ?";
$search_term = '%' . $search_term . '%';
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $search_term, $search_term);
$stmt->execute();
$stmt->bind_result($title, $author, $price);
?>

<main>
    <h2>Search Results</h2>
    <form method="GET" action="search.php">
        <input type="text" name="search" placeholder="Search books..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
        <button type="submit">Search</button>
    </form>

    <ul>
        <?php
        while ($stmt->fetch()) {
            echo "<li>$title by $author - \$$price</li>";
        }
        ?>
    </ul>
</main>

<?php
$stmt->close();
include 'includes/footer.php';
?>

