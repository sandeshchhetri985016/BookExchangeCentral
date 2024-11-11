<?php
session_start();
include 'includes/db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];

// Fetch user details
$user_query = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_query->bind_result($name, $email);
$user_query->fetch();
$user_query->close();

// Fetch user listings
$listings_query = $conn->prepare("SELECT id, title, author, price FROM listings WHERE user_id = ?");
$listings_query->bind_param("i", $user_id);
$listings_query->execute();
$listings_result = $listings_query->get_result();

// Fetch user messages (inbox and outbox)
$inbox_query = $conn->prepare("
    SELECT m.sender_id, m.message, l.title AS listing_title, u.name AS sender_name 
    FROM messages m 
    JOIN listings l ON m.listing_id = l.id
    JOIN users u ON m.sender_id = u.id 
    WHERE m.receiver_id = ?
");
$inbox_query->bind_param("i", $user_id);
$inbox_query->execute();
$inbox_result = $inbox_query->get_result();

$outbox_query = $conn->prepare("
    SELECT m.receiver_id, m.message, l.title AS listing_title, u.name AS receiver_name 
    FROM messages m 
    JOIN listings l ON m.listing_id = l.id
    JOIN users u ON m.receiver_id = u.id 
    WHERE m.sender_id = ?
");
$outbox_query->bind_param("i", $user_id);
$outbox_query->execute();
$outbox_result = $outbox_query->get_result();

// Fetch transaction history
$transactions_query = $conn->prepare("
    SELECT l.title AS book_title, t.transaction_date, t.amount 
    FROM transactions t 
    JOIN listings l ON t.book_id = l.id 
    WHERE t.buyer_id = ? OR t.seller_id = ?
");
$transactions_query->bind_param("ii", $user_id, $user_id);
$transactions_query->execute();
$transactions_result = $transactions_query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    
        <?php include 'includes/header.php'; ?>
    

    <div class="container">
        <!-- Back buttons -->
        <button onclick="history.back()" class="btn">Go Back</button> <!-- JavaScript Back Button -->
        <h2>Welcome, <?php echo htmlspecialchars($name); ?>!</h2>
        

        <!-- User Profile -->
        <section class="profile-section">
            <h2>Your Profile</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
        </section>

        <!-- User Listings -->
        <section class="listings-section">
            <h2>Your Listings</h2>
            <?php if ($listings_result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($listing = $listings_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($listing['title']); ?></td>
                                <td><?php echo htmlspecialchars($listing['author']); ?></td>
                                <td>$<?php echo htmlspecialchars(number_format($listing['price'], 2)); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>You have no listings.</p>
            <?php endif; ?>
        </section>

        <!-- Messages -->
        <section class="messages-section">
            <h2>Your Inbox</h2>
            <?php if ($inbox_result->num_rows > 0): ?>
                <ul>
                    <?php while ($message = $inbox_result->fetch_assoc()): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($message['sender_name']); ?>:</strong>
                            <?php echo htmlspecialchars($message['message']); ?> 
                            <em>(Regarding: <?php echo htmlspecialchars($message['listing_title']); ?>)</em>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>You have no messages in your inbox.</p>
            <?php endif; ?>

            <h2>Your Outbox</h2>
            <?php if ($outbox_result->num_rows > 0): ?>
                <ul>
                    <?php while ($message = $outbox_result->fetch_assoc()): ?>
                        <li>
                            <strong>To <?php echo htmlspecialchars($message['receiver_name']); ?>:</strong>
                            <?php echo htmlspecialchars($message['message']); ?> 
                            <em>(Regarding: <?php echo htmlspecialchars($message['listing_title']); ?>)</em>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>You have not sent any messages.</p>
            <?php endif; ?>
        </section>

        <!-- Transaction History -->
        <section class="transactions-section">
            <h2>Transaction History</h2>
            <?php if ($transactions_result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Book Title</th>
                            <th>Transaction Date</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($transaction = $transactions_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($transaction['book_title']); ?></td>
                                <td><?php echo htmlspecialchars($transaction['transaction_date']); ?></td>
                                <td>$<?php echo htmlspecialchars(number_format($transaction['amount'], 2)); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No transaction history available.</p>
            <?php endif; ?>
        </section>
    </div>

    <footer>
        <?php include 'includes/footer.php'; ?>
    </footer>
</body>
</html>

<?php
$listings_query->close();
$inbox_query->close();
$outbox_query->close();
$transactions_query->close();
$conn->close();
?>
