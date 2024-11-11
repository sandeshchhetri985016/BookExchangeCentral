<?php
session_start();
include 'includes/functions.php'; // Load utility functions

$conn = getDBConnection(); // Get database connection

$error = '';
$success = '';

// Handle logout (if `logout=1` is in the query string)
if (isset($_GET['logout'])) {
    session_destroy(); // End the session
    header('Location: auth.php'); // Redirect to auth page after logging out
    exit();
}

// Handle login
if (isset($_POST['login'])) {
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);

    // Authenticate the user
    if (loginUser($conn, $email, $password)) {
        header('Location: index.php'); // Redirect to index after successful login
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}

// Handle user signup
if (isset($_POST['signup'])) {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $password = password_hash(sanitizeInput($_POST['password']), PASSWORD_DEFAULT);

    // Register the user as a regular user
    if (signupUser($conn, $name, $email, $password, 'user')) {
        $success = "Account created successfully. You can now log in.";
    } else {
        $error = "Error creating account. Please try again.";
    }
}

// Handle admin signup
if (isset($_POST['admin_signup'])) {
    $name = sanitizeInput($_POST['admin_name']);
    $email = sanitizeInput($_POST['admin_email']);
    $password = password_hash(sanitizeInput($_POST['admin_password']), PASSWORD_DEFAULT);

    // Register the user as an admin
    if (signupUser($conn, $name, $email, $password, 'admin')) {
        $success = "Admin account created successfully. You can now log in.";
    } else {
        $error = "Error creating admin account. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Signup - BookExchangeCentral</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Add styles to position and hide the admin signup form */
        #admin-signup-form {
            display: none;
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 20px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .show-admin-link {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            font-size: 14px;
            color: white;
            text-decoration: underline;
        }

        .container {
            position: relative; /* So that the admin form can be positioned absolutely */
        }

        /* Close button styles */
        .close-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: #f44336;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            font-size: 12px;
        }

        .close-btn:hover {
            background-color: #d32f2f;
        }
    </style>
    <script>
        // JavaScript to toggle the visibility of the admin signup form
        function toggleAdminSignup() {
            var adminForm = document.getElementById('admin-signup-form');
            if (adminForm.style.display === 'none') {
                adminForm.style.display = 'block';
            } else {
                adminForm.style.display = 'none';
            }
        }

        // JavaScript to close the admin signup form
        function closeAdminSignup() {
            document.getElementById('admin-signup-form').style.display = 'none';
        }
    </script>
</head>
<body>

        <?php include 'includes/header.php'; ?>
 

    <?php if ($error): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:green;"><?php echo $success; ?></p>
    <?php endif; ?>

    <div class="container">
        <!-- Show login and signup forms only when the user is not logged in -->
        <?php if (!isset($_SESSION['user_id'])): ?>
            <h2>Login</h2>
            <form action="auth.php" method="POST">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                
                <button type="submit" name="login">Login</button>
            </form>

            <h2>Signup</h2>
            <form action="auth.php" method="POST">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
                
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                
                <button type="submit" name="signup">Signup</button>
            </form>

            <!-- Admin signup link and form -->
            <span class="show-admin-link" onclick="toggleAdminSignup()">Admin Signup</span>
            <div id="admin-signup-form">
                <!-- Close button -->
                <button class="close-btn" onclick="closeAdminSignup()">Close</button>
                
                <h3>Admin Signup</h3>
                <form action="auth.php" method="POST">
                    <label for="admin_name">Name:</label>
                    <input type="text" id="admin_name" name="admin_name" required>

                    <label for="admin_email">Email:</label>
                    <input type="email" id="admin_email" name="admin_email" required>

                    <label for="admin_password">Password:</label>
                    <input type="password" id="admin_password" name="admin_password" required>

                    <button type="submit" name="admin_signup">Signup as Admin</button>
                </form>
            </div>
        <?php else: ?>
            <!-- Redirect if already logged in -->
            <?php header("Location: index.php"); ?>
        <?php endif; ?>
    </div>

    <footer>
        <?php include 'includes/footer.php'; ?>
    </footer>
</body>
</html>
