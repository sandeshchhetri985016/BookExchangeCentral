<?php
$loggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookExchangeCentral</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="logo.png" alt="BookExchangeCentral Logo">
            <span>BookExchangeCentral</span>
        </div>
        <form class="search-form" action="search.php" method="get">
            <input type="text" name="q" placeholder="Search">
            <button type="submit">🔍</button>
        </form>
        <nav>
            <?php if ($loggedIn): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="listings.php">Listings</a>
                <a href="messaging.php">Messaging Center</a>
                <?php if ($isAdmin): ?>
                    <a href="admin.php">Admin Dashboard</a>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
            <a href="about.php">About/Help</a>
        </nav>
    </header>
    <main>