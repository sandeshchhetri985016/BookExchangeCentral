<?php include 'includes/functions.php'; // Load utility functions ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/style.css"> <!-- Ensure consistent loading of CSS -->
    <title>About - BookExchangeCentral</title>
</head>
<body>
    
        <?php include 'includes/header.php'; ?> <!-- Include header -->
    

    <main>
        
        <div class="container"> <!-- Add container for consistent layout -->
            <!-- Back buttons -->
            <button onclick="history.back()" class="btn">Go Back</button> <!-- JavaScript Back Button -->
            <h2>About BookExchangeCentral</h2>
            
            <p>BookExchangeCentral is a platform designed for students to easily buy and sell used textbooks. We aim to make the process of exchanging books between students seamless and efficient.</p>
            
            <h3>Our Mission</h3>
            <p>At BookExchangeCentral, our mission is to provide an affordable and convenient solution for students to access the textbooks they need without the burden of high costs. We believe that every student deserves access to quality educational resources.</p>

            <h3>Features</h3>
            <ul>
                <li><strong>User-Friendly Interface:</strong> Our platform is designed for ease of use, allowing users to quickly navigate and find listings.</li>
                <li><strong>Secure Transactions:</strong> We prioritize your safety and privacy by ensuring all transactions are secure.</li>
                <li><strong>Image Upload:</strong> Sellers can easily upload images of their textbooks, providing potential buyers with a clear view of the item.</li>
                <li><strong>Messaging System:</strong> Our built-in messaging system allows buyers and sellers to communicate directly.</li>
                <li><strong>Search Functionality:</strong> Quickly find textbooks by title or author with our robust search feature.</li>
            </ul>

            <h3>User Testimonials</h3>
            <blockquote>
                <p>"BookExchangeCentral made it so easy for me to find the textbooks I needed for my classes at a fraction of the price!" - <em>Emily R.</em></p>
            </blockquote>
            <blockquote>
                <p>"I sold my used textbooks quickly and effortlessly. The platform is user-friendly and efficient!" - <em>Michael T.</em></p>
            </blockquote>

            <h3>Frequently Asked Questions</h3>
            <h4>1. How do I create an account?</h4>
            <p>Simply click on the 'Sign Up' button on the homepage and fill in the required information. You’ll be ready to start buying and selling textbooks in no time!</p>
            
            <h4>2. Are there any fees for using BookExchangeCentral?</h4>
            <p>No, our platform is completely free to use for students!</p>

            <h4>3. What should I do if I encounter issues?</h4>
            <p>If you need help, contact our support team at <a href="mailto:support@bookexchangecentral.com">support@bookexchangecentral.com</a>. We're here to assist you!</p>
        </div>
    </main>

    <footer>
        <?php include 'includes/footer.php'; ?> <!-- Include footer -->
    </footer>
</body>
</html>
