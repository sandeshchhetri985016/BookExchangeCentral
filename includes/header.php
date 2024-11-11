<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookExchangeCentral</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<header>
    <nav>
        <div class="logo">
            <a href="index.php"><img src="assets/bookimg/logo.png" alt="BookExchangeCentral Logo"></a>
        </div>
        <div class="menu-toggle" id="mobile-menu">
            <i class="fas fa-bars"></i>
        </div>
        <ul class="nav-links" id="nav-links">
            <li><a href="index.php">Home</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="listings.php">Listings</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="about.php">About</a></li>
                <li><a href="cart.php" class="cart-icon"><i class="fas fa-shopping-cart"></i></a></li>
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                    <li><a href="admin.php" style = "color: orange;">Admin</a></li>
                <?php endif; ?>
                <li><a href="auth.php?logout=1" style="color:red;">Logout</a></li>
            <?php else: ?>
                <li><a href="auth.php">Login/Signup</a></li>
                <li><a href="about.php">About</a></li>
            <?php endif; ?>
        </ul>
        <form action="index.php" method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search..." aria-label="Search">
            <button type="submit" class="search-button"><i class="fas fa-search"></i></button>
        </form>
    </nav>
</header>

<script>
    const mobileMenu = document.getElementById('mobile-menu');
    const navLinks = document.getElementById('nav-links');

    mobileMenu.addEventListener('click', () => {
        navLinks.classList.toggle('active');
    });
</script>
