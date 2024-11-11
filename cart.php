<?php
session_start();
include 'includes/header.php';
include 'includes/functions.php';
checkSession();

$conn = getDBConnection();

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$totalAmount = 0; // Initialize totalAmount for current cart

// Handle adding items to the cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['listing_id']) && !isset($_POST['remove'])) {
    $listing_id = (int)$_POST['listing_id'];
    $quantity = (int)$_POST['quantity'] ?? 1;

    if ($listing_id > 0) {
        // Add or update cart item
        $_SESSION['cart'][$listing_id] = ($_SESSION['cart'][$listing_id] ?? 0) + $quantity;
        header("Location: cart.php");
        exit();
    }
}

// Handle removing items from the cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['remove']) && isset($_POST['listing_id'])) {
    $listing_id = (int)$_POST['listing_id'];

    // Remove item from cart
    if (isset($_SESSION['cart'][$listing_id])) {
        unset($_SESSION['cart'][$listing_id]);
    }
    header("Location: cart.php");
    exit();
}

// Display cart contents
echo "<div class='container'>";
echo "<button onclick='history.back()' class='btn'>Go Back</button>"; // Back button
echo "<h2>Your Cart</h2>";

if (empty($_SESSION['cart'])) {
    echo "<p>Your cart is empty.</p>";
} else {
    echo "<table class='cart-table'>";
    echo "<thead><tr><th>Image</th><th>Title</th><th>Quantity</th><th>Price</th><th>Total</th><th style='width: 80px;'>Actions</th></tr></thead>"; // Set the width for the Actions column
    echo "<tbody>";

    foreach ($_SESSION['cart'] as $id => $quantity) {
        // Fetch listing details
        $listing_sql = "SELECT title, price, image, user_id FROM listings WHERE id = ?";
        $listing_stmt = $conn->prepare($listing_sql);
        $listing_stmt->bind_param("i", $id);
        $listing_stmt->execute();
        $listing_stmt->bind_result($title, $price, $image, $seller_id);
        $listing_stmt->fetch();
        $listing_stmt->close();

        $total_price = $price * $quantity;
        $totalAmount += $total_price;  // Add to total amount for current cart

        echo "<tr>";
        echo "<td><img src='$image' alt='$title' class='cart-image' style='max-width: 100px; height: auto;'></td>";
        echo "<td>$title</td>";
        echo "<td>$quantity</td>";
        echo "<td>$" . number_format($price, 2) . "</td>";
        echo "<td>$" . number_format($total_price, 2) . "</td>";
        echo "<td style='text-align: center;'>"; // Center the button in the Actions column
        echo "<form method='POST' action=''>";
        echo "<input type='hidden' name='listing_id' value='$id'>";
        echo "<input type='submit' name='remove' value='Remove' class='remove-button'>"; // Add class for styling
        echo "</form>";
        echo "</td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";

    // Show total amount for current cart
    echo "<h3>Total: $" . number_format($totalAmount, 2) . "</h3>";
    
    // Show fake transaction form
    echo "<h4>Complete Your Purchase</h4>";
    echo "<form method='POST' action=''>";
    echo "<label for='address'>Shipping Address:</label><br>";
    echo "<textarea id='address' name='address' required></textarea><br>";
    echo "<input type='submit' name='checkout' value='Confirm Order'>";
    echo "</form>";
}

echo "</div>"; // Close container

// Handle the fake transaction and store in the transactions table
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['checkout'])) {
    $user_id = $_SESSION['user_id']; // Assuming session stores logged-in user ID
    $address = $_POST['address'];

    // Process each item in the cart
    foreach ($_SESSION['cart'] as $listing_id => $quantity) {
        // Get listing and seller info again
        $listing_sql = "SELECT price, user_id FROM listings WHERE id = ?";
        $listing_stmt = $conn->prepare($listing_sql);
        $listing_stmt->bind_param("i", $listing_id);
        $listing_stmt->execute();
        $listing_stmt->bind_result($price, $seller_id);
        $listing_stmt->fetch();
        $listing_stmt->close();

        $amount = $price * $quantity;

        // Insert transaction
        $transaction_sql = "INSERT INTO transactions (book_id, buyer_id, seller_id, amount, status) VALUES (?, ?, ?, ?, 'completed')";
        $transaction_stmt = $conn->prepare($transaction_sql);
        $transaction_stmt->bind_param("iiid", $listing_id, $user_id, $seller_id, $amount);
        $transaction_stmt->execute();
        $transaction_stmt->close();
    }

    // Clear the cart after transaction
    $_SESSION['cart'] = [];

    // Display confirmation
    echo "<h4>Thank you for your purchase!</h4>";
    echo "<p>Your order has been processed. Shipping to: " . htmlspecialchars($address) . "</p>";
    echo "<p>Total Paid: $" . number_format($totalAmount, 2) . "</p>";
}

// Show Order History (for logged-in user)
echo "<div class='container'>";
echo "<h2>Your Order History</h2>";

// Query to get order history and calculate total paid from previous transactions
$order_sql = "SELECT t.id, l.title, t.amount, t.transaction_date FROM transactions t
              JOIN listings l ON t.book_id = l.id WHERE t.buyer_id = ?";
$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("i", $_SESSION['user_id']);  // Assuming user_id is in session
$order_stmt->execute();
$order_stmt->bind_result($transaction_id, $book_title, $transaction_amount, $transaction_date);

// Initialize variable for total paid from order history
$totalPaidFromHistory = 0;

if ($order_stmt->fetch()) {
    echo "<table class='order-table'>";
    echo "<thead><tr><th>Transaction ID</th><th>Book Title</th><th>Amount</th><th>Transaction Date</th></tr></thead>";
    echo "<tbody>";

    do {
        $totalPaidFromHistory += $transaction_amount;  // Sum the amounts from the order history
        echo "<tr>";
        echo "<td>$transaction_id</td>";
        echo "<td>$book_title</td>";
        echo "<td>$" . number_format($transaction_amount, 2) . "</td>";
        echo "<td>$transaction_date</td>";
        echo "</tr>";
    } while ($order_stmt->fetch());

    echo "</tbody>";
    echo "</table>";

    // Show total paid from history
    echo "<h3>Total Paid (from order history): $" . number_format($totalPaidFromHistory, 2) . "</h3>";
} else {
    echo "<p>No past transactions found.</p>";
}

$order_stmt->close();
echo "</div>";

include 'includes/footer.php';
?>
