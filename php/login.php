<?php
session_start();
require_once 'auth.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $result = loginUser($_POST['email'], $_POST['password']);
    if ($result === "Login successful.") {
        header("Location: dashboard.php");
        exit();
    }
}
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
        <h2>Login</h2>
        <form action="login.php" method="post">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Login</button>
        </form>
        <?php
        if (isset($result)) {
            echo "<p>$result</p>";
        }
        ?>
    </main>

    <footer>
        <!-- Your footer content -->
    </footer>
</body>
</html>
<?php include 'footer.php'; ?>