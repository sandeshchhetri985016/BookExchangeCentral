# BookExchangeCentral

BookExchangeCentral is a web-based platform for university students to buy and sell used textbooks.

## Features

User registration and login  
Book listings (create, edit, delete)  
Search for books  
User-to-user messaging  
Admin dashboard for managing users and listings  
Technologies Used  
HTML5, CSS3, JavaScript  
PHP for server-side processing  
MySQL for database  
JS for dynamic content loading  

## File Structure

/bookexchangecentral
├── index.php                       # Home page with book listings and introductory content
├── auth.php                        # User authentication (login and signup functionalities)
├── dashboard.php                   # User dashboard (profile, listings, messages, transaction history)
├── listings.php                    # Page for managing book listings (create, edit, view)
├── search.php                      # Search results page for book listings
├── messaging.php                   # Messaging center for communication between users
├── admin.php                       # Admin dashboard for managing users, listings, and transactions
├── about.php                       # About/help page for platform information and user assistance
├── cart.php                        # Cart page for managing book selections
├── /assets
| ├── css
| │   └── style.css                   # Main stylesheet for the website
| ├── js
| │   ├── script.js                   # JavaScript for interactive elements and form validation
| │   ├── search.js                   # JavaScript for search-related functionalities
| │   └── messaging.js                # JavaScript for messaging features
| └── /bookimg                        # contains images for books and logo
├── /includes
| ├── db_connect.php                  # Database connection setup file
| ├── footer.php                      # Footer included on all pages for consistent styling
| ├── functions.php                   # Utility functions used across the website
| └── header.php                      # Header included on all pages for consistent navigation
|
├──/data
| ├── book_data.sql                   # SQL file containing sample book data for database initialization
| └── bookexchangecentral.sql         # Main SQL file with the database schema for tables
|
└──/.vscode
  └── settings.json                   # VS Code configuration file for project-specific settings


## Installation

### Clone the repository:

git clone https://github.com/sandeshchhetri985016/BookExchangeCentral.git
Set up a local server (wampserver), and move the project to the www folder.

Create a MySQL database named book_exchange and import the bookexchangecentral.sql file using phpMyAdmin.

Start your local server and visit http://localhost/BookExchangeCentral/.

## License

This project is open source software.

