# Bookstore E-commerce Web App

This project is a **PHP-based Bookstore E-commerce Web Application** designed to manage book sales and inventory with two types of users: **Admin** and **Customer**. It runs on the XAMPP server using PHPMyAdmin with MySQL (default port: 3306).

---

## Features

### Admin
1. **Manage Books**:
   - Add, Update, Delete, and View all books.
2. **Manage Categories**:
   - Full CRUD (Create, Read, Update, Delete) operations on book categories.
3. **Order Management**:
   - View customer orders and order details.

### Customer
1. **Shopping Cart**:
   - Add books to the cart.
2. **Order Placement**:
   - Place orders for the selected books.
3. **Order Tracking**:
   - View current and past orders.

---

## Installation and Setup

### Prerequisites
- [XAMPP](https://www.apachefriends.org/index.html) installed (PHP, MySQL, Apache).
- Basic knowledge of PHP and MySQL.

### Steps to Run the Project
1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/bookstore-ecommerce.git
   cd bookstore-ecommerce

2. Start the XAMPP server:

    - Open the XAMPP control panel.
    - Start Apache and MySQL services.

3. Set up the database:

    - Open PHPMyAdmin (default URL: http://localhost/phpmyadmin).
    - Create a new database named bookstore.
    - Import the SQL dump file db.sql included in the repository.

4. Update the db_connect.php file (if necessary):

```bash
<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'bookstore';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

5. Access the application:

   - Place the project folder in xampp/htdocs.
   - Navigate to the application in your browser: http://localhost/bookstore

