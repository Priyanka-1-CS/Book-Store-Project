<?php

session_start();

require_once "includes/connect_db.inc.php";

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "User not logged in!";
    exit; // Stop execution if the user is not logged in
}

$user_id = $_SESSION['user_id'];

// Function to fetch all orders for the logged-in user
function getAllOrders($pdo, $user_id) {
    try {
        // Fetch orders for the current user
        $sql = "SELECT * FROM orders WHERE user_id = :user_id ORDER BY order_date DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching orders: " . $e->getMessage();
    }
}

// Fetch orders for the logged-in user
$orders = getAllOrders($pdo, $user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="css/style.css">

    <title>Orders - Online Bookstore</title>
</head>
<body>

<!-- Header Section -->
<header>
   <div class="header">
      <a href="home.php" class="logo">𝕿𝖆𝖙𝖙𝖑𝖊𝕿𝖆𝖑𝖊</a>
      <nav>
         <a href="home.php">Home</a>
         <a href="about.php">About</a>
         <a href="bookstore.php">BookStore</a>
         <a href="orders.php">Orders</a>
         <a href="cart.php">Cart</a>
      </nav>
      <div class="icons">
        <?php 
            if (isset($_SESSION['user_id'])): // If user is logged in
        ?>
        <a href="logout.php" class="delete-btn">Logout</a>
        <?php else: ?>
            <a href="index.php">Login</a>
        <?php endif; ?>
    </div>
</header>

<main>
    <!-- Orders Table -->
<div class="container">
    <h1>My Orders</h1>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Phone Number</th>
                <th>Email</th>
                <th>Payment Method</th>
                <th>Address</th>
                <th>Book ID</th>
                <th>Quantity</th>
                <th>Total Price</th>
                <th>Order Date</th>
                <th>Payment Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Loop through orders and display them in the table
            foreach ($orders as $order) {
                // Use htmlspecialchars to prevent XSS attacks
                $payment_status = htmlspecialchars(trim($order['payment_status']));
                echo "<tr>
                        <td>{$order['name']}</td>
                        <td>{$order['number']}</td>
                        <td>{$order['email']}</td>
                        <td>{$order['payment_method']}</td>
                        <td>{$order['address']}</td>
                        <td>{$order['book_id']}</td>
                        <td>{$order['quantity']}</td>
                        <td>₹{$order['total_price']}</td>
                        <td>{$order['order_date']}</td>
                        <td><span class='status {$payment_status}'>{$payment_status}</span></td>
                    </tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</main>

<!-- Footer Section -->
<footer>
    <div class="box-container">
        <div class="box">
            <h3>quick links</h3>
            <a href="about.php">About</a>
            <a href="shop.php">E-Store</a>
            <a href="#">Reach Out to us</a>
        </div>
        <div class="box">
            <h3>extra links</h3>
            <a href="register.php">Register</a>
            <a href="cart.php">Cart</a>
            <a href="orders.php">Orders</a>
        </div>
        <div class="box">
            <h3>contact info</h3>
            <p>+91-33-36869091</p>
            <p>priyanka@mail.com</p>
            <p>Calcutta, India, 700019</p>
        </div>
        <div class="box">
            <h3>follow us</h3>
            <a href="#">Twitter</a>
            <a href="#">Instagram</a>
            <a href="#">Linkedin</a>
        </div>
    </div>
</footer>

<!-- Credit Section -->
<section class="credit">
    <p>Copyright @ <?php echo date('Y'); ?> <span>Priyanka Mukherjee. All rights reserved</span></p>
</section>

</body>
</html>

<?php
// Close the PDO connection
$pdo = null;
?>
