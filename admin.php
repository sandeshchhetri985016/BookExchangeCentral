<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Include utility functions
include 'includes/functions.php';
include 'includes/header.php';
$conn = getDBConnection();

// Fetch users and listings from the database
$users = fetchAllUsers($conn); // Function to get all users
$listings = fetchAllListings($conn); // Function to get all listings
$totalUsers = countUsers($conn); // Function to count total users
$totalListings = countListings($conn); // Function to count total listings

// Handle form submission for saving user changes
if (isset($_POST['save_user_changes'])) {
    $edit_user_id = $_POST['edit_user_id'];
    $edit_user_name = $_POST['edit_user_name'];
    $edit_user_email = $_POST['edit_user_email'];
    $edit_user_role = $_POST['edit_user_role'];
    
    // Check if a new password was entered
    if (!empty($_POST['edit_user_password'])) {
        $edit_user_password = password_hash($_POST['edit_user_password'], PASSWORD_BCRYPT); // Hash the password

        // Update user with password
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ?, password = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $edit_user_name, $edit_user_email, $edit_user_role, $edit_user_password, $edit_user_id);
    } else {
        // Update user without password
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
        $stmt->bind_param("sssi", $edit_user_name, $edit_user_email, $edit_user_role, $edit_user_id);
    }

    if ($stmt->execute()) {
        echo "<p>User updated successfully!</p>";
    } else {
        echo "<p>Failed to update user.</p>";
    }

    // Reload users after updating
    $users = fetchAllUsers($conn);
}


// Handle user deletion
if (isset($_POST['delete_user_id'])) {
    $delete_user_id = $_POST['delete_user_id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $delete_user_id);
    if ($stmt->execute()) {
        echo "<p>User deleted successfully!</p>";
    } else {
        echo "<p>Failed to delete user.</p>";
    }

    // Reload users after deletion
    $users = fetchAllUsers($conn);
}

// Handle listing updates
if (isset($_POST['save_listing_changes'])) {
    $edit_listing_id = $_POST['edit_listing_id'];
    $edit_listing_title = $_POST['edit_listing_title'];
    $edit_listing_author = $_POST['edit_listing_author'];
    $edit_listing_price = $_POST['edit_listing_price'];
    
    $stmt = $conn->prepare("UPDATE listings SET title = ?, author = ?, price = ? WHERE id = ?");
    $stmt->bind_param("ssdi", $edit_listing_title, $edit_listing_author, $edit_listing_price, $edit_listing_id);

    if ($stmt->execute()) {
        echo "<p>Listing updated successfully!</p>";
    } else {
        echo "<p>Failed to update listing.</p>";
    }

    // Reload listings after updating
    $listings = fetchAllListings($conn);
}

// Handle listing deletion
if (isset($_POST['delete_listing_id'])) {
    $delete_listing_id = $_POST['delete_listing_id'];
    $stmt = $conn->prepare("DELETE FROM listings WHERE id = ?");
    $stmt->bind_param("i", $delete_listing_id);
    if ($stmt->execute()) {
        echo "<p>Listing deleted successfully!</p>";
    } else {
        echo "<p>Failed to delete listing.</p>";
    }

    // Reload listings after deletion
    $listings = fetchAllListings($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Additional styles for layout */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
        }
        .statistics {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        .stat {
            padding: 20px;
            border: 1px solid #ccc;
            text-align: center;
            width: 30%;
        }
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            width: 50%;
        }
        .close {
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>
    

    <div class="container">
        
    <h1>Welcome Admin, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h1>
        <div class="statistics">
            <div class="stat">
                <h3>Total Users</h3>
                <p><?php echo $totalUsers; ?></p>
            </div>
            <div class="stat">
                <h3>Total Listings</h3>
                <p><?php echo $totalListings; ?></p>
            </div>
            <div class="stat">
                <h3>Active Listings</h3>
                <p><?php echo countActiveListings($conn); // Function to count active listings ?></p>
            </div>
        </div>

        <h2>User Management</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role']); ?></td>
                        <td>
                        <!-- <td> -->
                            <a href="#" class="edit-button" onclick="openEditModal('<?php echo $user['id']; ?>', '<?php echo htmlspecialchars($user['name']); ?>', '<?php echo htmlspecialchars($user['email']); ?>', '<?php echo $user['role']; ?>')">Edit</a>
                            <form method="POST" action="" style="display:inline;">
                                <input type="hidden" name="delete_user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this user?');" class="remove-button">Delete</button>
                            </form>
                        </td>

                        <!-- </td> -->
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Listing Management</h2>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listings as $listing): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($listing['title']); ?></td>
                        <td><?php echo htmlspecialchars($listing['author']); ?></td>
                        <td><?php echo htmlspecialchars($listing['price']); ?></td>
                        <td>
                            <a href="#" class="edit-button" onclick="openListingEditModal('<?php echo $listing['id']; ?>', '<?php echo htmlspecialchars($listing['title']); ?>', '<?php echo htmlspecialchars($listing['author']); ?>', '<?php echo $listing['price']; ?>')">Edit</a>
                            <form method="POST" action="" style="display:inline;">
                                <input type="hidden" name="delete_listing_id" value="<?php echo $listing['id']; ?>">
                                <button type="submit" onclick="return confirm('Are you sure you want to delete this listing?');" class="remove-button">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p>This is the admin dashboard. Here, you can manage users, review listings, and monitor platform activities.</p>
    </div>

    <!-- Modal for Editing User -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Edit User</h2>
            <form method="POST" action="">
                <input type="hidden" name="edit_user_id" id="edit_user_id">
                
                <label for="edit_user_name">Name:</label>
                <input type="text" name="edit_user_name" id="edit_user_name" required><br>

                <label for="edit_user_email">Email:</label>
                <input type="email" name="edit_user_email" id="edit_user_email" required><br>

                <label for="edit_user_role">Role:</label>
                <select name="edit_user_role" id="edit_user_role" required>
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select><br>

                <!-- Button to toggle password field -->
                <button type="button" id="togglePasswordField" onclick="togglePassword()">Change Password</button><br>

                <!-- Hidden password input field initially -->
                <div id="passwordField" style="display: none;">
                    <label for="edit_user_password">New Password:</label>
                    <input type="password" name="edit_user_password" id="edit_user_password"><br>
                </div>

                <button type="submit" name="save_user_changes">Save Changes</button>
            </form>
        </div>
    </div>

    <!-- Modal for Editing Listing -->
    <div id="editListingModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeListingModal()">&times;</span>
            <h2>Edit Listing</h2>
            <form method="POST" action="">
                <input type="hidden" name="edit_listing_id" id="edit_listing_id">

                <label for="edit_listing_title">Title:</label>
                <input type="text" name="edit_listing_title" id="edit_listing_title" required><br>

                <label for="edit_listing_author">Author:</label>
                <input type="text" name="edit_listing_author" id="edit_listing_author" required><br>

                <label for="edit_listing_price">Price:</label>
                <input type="number" step="0.01" name="edit_listing_price" id="edit_listing_price" required><br>

                <button type="submit" name="save_listing_changes">Save Changes</button>
            </form>
        </div>
    </div>


    <footer>
        <?php include 'includes/footer.php'; ?>
    </footer>

    <script>
    // Function to toggle the password field visibility
    function togglePassword() {
        var passwordField = document.getElementById('passwordField');
        if (passwordField.style.display === 'none') {
            passwordField.style.display = 'block';
        } else {
            passwordField.style.display = 'none';
        }
    }

    // Function to open the edit modal and fill the fields
    function openEditModal(id, name, email, role) {
        document.getElementById('edit_user_id').value = id;
        document.getElementById('edit_user_name').value = name;
        document.getElementById('edit_user_email').value = email;
        document.getElementById('edit_user_role').value = role;
        document.getElementById('editUserModal').style.display = 'block';
        document.getElementById('passwordField').style.display = 'none'; // Reset password field
    }

    // Function to close the modal
    function closeModal() {
        document.getElementById('editUserModal').style.display = 'none';
    }

    // Function to open the edit modal for listings and fill the fields
    function openListingEditModal(id, title, author, price) {
        document.getElementById('edit_listing_id').value = id;
        document.getElementById('edit_listing_title').value = title;
        document.getElementById('edit_listing_author').value = author;
        document.getElementById('edit_listing_price').value = price;
        document.getElementById('editListingModal').style.display = 'block';
    }

    // Function to close the listing modal
    function closeListingModal() {
        document.getElementById('editListingModal').style.display = 'none';
    }

    // Function to open the listing edit modal and fill the fields
    function openListingEditModal(id, title, author, price) {
        document.getElementById('edit_listing_id').value = id;
        document.getElementById('edit_listing_title').value = title;
        document.getElementById('edit_listing_author').value = author;
        document.getElementById('edit_listing_price').value = price;
        document.getElementById('editListingModal').style.display = 'block';
    }

    // Function to close the listing modal
    function closeListingModal() {
        document.getElementById('editListingModal').style.display = 'none';
    }

</script>
</body>
</html>
