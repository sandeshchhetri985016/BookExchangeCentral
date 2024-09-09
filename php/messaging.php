<?php
session_start();
require_once 'db_connect.php';

function sendMessage($recipientId, $content, $isProposal = false) {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        return "User must be logged in to send a message.";
    }
    
    // Validate input
    if (empty($recipientId) || empty($content)) {
        return "Recipient and message content are required.";
    }
    
    // Prepare and execute statement
    $stmt = $conn->prepare("INSERT INTO messages (senderId, recipientId, content, isProposal) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iisi", $_SESSION['user_id'], $recipientId, $content, $isProposal);
    
    if ($stmt->execute()) {
        return "Message sent successfully.";
    } else {
        return "Error: " . $stmt->error;
    }
}

function getMessages($userId) {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        return "User must be logged in to view messages.";
    }
    
    $stmt = $conn->prepare("SELECT m.*, u.username as senderName FROM messages m JOIN users u ON m.senderId = u.userId WHERE m.recipientId = ? ORDER BY m.sentDate DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    
    return $messages;
}

// Usage example
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['send_message'])) {
        echo sendMessage($_POST['recipient_id'], $_POST['content'], isset($_POST['is_proposal']));
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['get_messages'])) {
        $messages = getMessages($_SESSION['user_id']);
        echo json_encode($messages);
    }
}
include 'header.php'; ?>
<?php include 'footer.php'; ?>
