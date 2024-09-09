CREATE DATABASE bookexchange_db;

USE bookexchange_db;

CREATE TABLE users (
    userId INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    contact VARCHAR(100),
    userRole ENUM('user', 'admin') DEFAULT 'user'
);

CREATE TABLE books (
    bookId INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    condition ENUM('new', 'like new', 'good', 'fair', 'poor') NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255),
    postedDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('available', 'sold') DEFAULT 'available',
    userId INT,
    FOREIGN KEY (userId) REFERENCES users(userId)
);

CREATE TABLE transactions (
    transactionId INT AUTO_INCREMENT PRIMARY KEY,
    bookId INT,
    buyerId INT,
    sellerId INT,
    transactionDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    FOREIGN KEY (bookId) REFERENCES books(bookId),
    FOREIGN KEY (buyerId) REFERENCES users(userId),
    FOREIGN KEY (sellerId) REFERENCES users(userId)
);

CREATE TABLE messages (
    messageId INT AUTO_INCREMENT PRIMARY KEY,
    senderId INT,
    recipientId INT,
    content TEXT,
    sentDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    isProposal BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (senderId) REFERENCES users(userId),
    FOREIGN KEY (recipientId) REFERENCES users(userId)
);