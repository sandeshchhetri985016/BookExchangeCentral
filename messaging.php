<?php
include 'includes/header.php';
include 'includes/functions.php';
checkSession();

$conn = getDBConnection();

$listing_id = isset($_GET['listing_id']) ? (int)$_GET['listing_id'] : 0;

// Fetch listing details along with the seller's name
$listing_sql = "SELECT l.title, l.author, l.price, l.image, u.name as seller_name 
                FROM listings l 
                JOIN users u ON l.user_id = u.id 
                WHERE l.id = ?";
$listing_stmt = $conn->prepare($listing_sql);
$listing_stmt->bind_param("i", $listing_id);
$listing_stmt->execute();
$listing_stmt->bind_result($title, $author, $price, $image, $seller_name);
$listing_stmt->fetch();
$listing_stmt->close();

if (!$title) {
    echo "<p>Listing not found.</p>";
    include 'includes/footer.php';
    exit();
}

// Handle message submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if the form is for a new message or a delete action
    if (isset($_POST['new_message'])) {
        $new_message = isset($_POST['new_message']) ? trim($_POST['new_message']) : '';
        $receiver_id = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;

        // Insert the new message into the database
        if (!empty($new_message) && $receiver_id > 0) {
            $insert_message_sql = "INSERT INTO messages (listing_id, sender_id, receiver_id, message, timestamp) VALUES (?, ?, ?, ?, NOW())";
            $insert_message_stmt = $conn->prepare($insert_message_sql);
            $insert_message_stmt->bind_param("iiis", $listing_id, $_SESSION['user_id'], $receiver_id, $new_message);
            $insert_message_stmt->execute();
            $insert_message_stmt->close();

            // Redirect to the same page to show the updated messages
            header("Location: messaging.php?listing_id=" . $listing_id);
            exit();
        }
    } elseif (isset($_POST['delete_message'])) {
        $message_id = isset($_POST['message_id']) ? (int)$_POST['message_id'] : 0;

        // Delete the message from the database
        if ($message_id > 0) {
            $delete_message_sql = "DELETE FROM messages WHERE id = ?";
            $delete_message_stmt = $conn->prepare($delete_message_sql);
            $delete_message_stmt->bind_param("i", $message_id);
            $delete_message_stmt->execute();
            $delete_message_stmt->close();

            // Redirect to the same page to show the updated messages
            header("Location: messaging.php?listing_id=" . $listing_id);
            exit();
        }
    }
}

// Display listing details
echo "<div class='container'>";
echo "<div class='listing-details'>";
echo "<h2>Listing Details</h2>";
echo "<div class='listing-item'>";
echo "<div class='image-wrapper'>";
echo "<img src='$image' alt='$title' class='listing-image' style='max-width: 300px; height: auto;'>";
echo "</div>";
echo "<div class='text-wrapper'>";
echo "<h3>$title</h3>";
echo "<p>Author: $author</p>";
echo "<p>Price: $" . number_format($price, 2) . "</p>";
echo "<p>Listed by: <strong>$seller_name</strong></p>";

// Add the quantity input field
echo '<form method="POST" action="cart.php" class="quantity-form">'; // Pointing to cart.php
echo '<label for="quantity">Quantity:</label>';
echo '<input type="number" id="quantity" name="quantity" value="1" min="1" required>';
echo '<input type="hidden" name="listing_id" value="' . $listing_id . '">'; // Hidden field for listing ID
echo '<button type="submit">Add to Cart</button>'; // Button to add to cart
echo '</form>';

echo "</div>"; // Closing text-wrapper
echo "</div>"; // Closing listing-item
echo "</div>"; // Closing listing-details

// Fetch and display messages
$message_sql = "SELECT m.id, m.sender_id, m.receiver_id, m.message, m.timestamp, u.name 
                FROM messages m 
                JOIN users u ON m.sender_id = u.id 
                WHERE m.listing_id = ?";
$message_stmt = $conn->prepare($message_sql);
$message_stmt->bind_param("i", $listing_id);
$message_stmt->execute();
$message_stmt->bind_result($message_id, $sender_id, $receiver_id, $message, $timestamp, $sender_name);

echo "<div class='messages-container'>";
echo "<h3>Messages</h3>";

$messages_exist = false;
while ($message_stmt->fetch()) {
    $messages_exist = true;
    echo "<div class='listing-item'>";
    echo "<div class='text-wrapper'>";
    echo "<p><strong>$sender_name:</strong> $message <em>($timestamp)</em></p>";
    
    // Check if the current user is the sender or an admin
    if ($sender_id == $_SESSION['user_id'] || $_SESSION['user_role'] === 'admin') {
        // Display the delete form
        echo '<form method="POST" action="messaging.php?listing_id=' . $listing_id . '">';
        echo '<input type="hidden" name="message_id" value="' . $message_id . '">';
        echo '<button type="submit" name="delete_message" onclick="return confirm(\'Are you sure you want to delete this message?\');">Delete</button>';
        echo '</form>';
    }

    // Display the reply form with the sender's name
    echo '<form method="POST" action="messaging.php?listing_id=' . $listing_id . '">';
    echo '<label for="reply_message">Reply to <strong>' . htmlspecialchars($sender_name) . '</strong>:</label>';
    echo '<textarea id="reply_message" name="reply_message" required></textarea>';
    echo '<input type="hidden" name="receiver_id" value="' . $sender_id . '">';
    echo '<button type="submit">Send Reply</button>';
    echo '</form>';
    
    echo "</div>";
    echo "</div>"; // Closing listing-item
}

$message_stmt->close();

if (!$messages_exist) {
    echo "<p>No messages yet for this listing. Be the first to send a message!</p>";
}

// Form to send a new message
echo "<div class='listing-item'>";
echo "<div class='text-wrapper'>";
echo '<form method="POST" action="messaging.php?listing_id=' . $listing_id . '" class="message-form">';
echo '<label for="new_message">Send a Message to the Seller:</label>';
echo '<textarea id="new_message" name="new_message" required></textarea>';
echo '<input type="hidden" name="receiver_id" value="' . $_SESSION['user_id'] . '">'; // Ensure this is the receiver's ID
echo '<button type="submit">Send Message</button>';
echo '</form>';
echo "</div>";
echo "</div>"; // Closing listing-item

echo "</div>"; // Closing messages-container
echo "</div>"; // Closing container

include 'includes/footer.php';
?>
