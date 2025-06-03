<?php
session_start();
require_once "includes/connect_db.inc.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = [];

// Fetch cart items for this user
$stmt = $pdo->prepare("SELECT c.*, b.title FROM cart c JOIN books b ON c.book_id = b.book_id WHERE c.user_id = :user_id");
$stmt->execute([':user_id' => $user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cart_items)) {
    $message[] = "Your cart is empty!";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($cart_items)) {
    $name = htmlspecialchars($_POST['name']);
    $number = htmlspecialchars($_POST['number']);
    $email = htmlspecialchars($_POST['email']);
    $payment_method = htmlspecialchars($_POST['payment_method']);
    $address = htmlspecialchars($_POST['address']);
    $order_date = date('Y-m-d H:i:s');
    $total_price = 0;

    foreach ($cart_items as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }

    // Insert into orders table
    $order_stmt = $pdo->prepare("INSERT INTO orders (user_id, name, number, email, payment_method, address, total_price, order_date, payment_status) VALUES (:user_id, :name, :number, :email, :payment_method, :address, :total_price, :order_date, 'pending')");
    $order_stmt->execute([
        ':user_id' => $user_id,
        ':name' => $name,
        ':number' => $number,
        ':email' => $email,
        ':payment_method' => $payment_method,
        ':address' => $address,
        ':total_price' => $total_price,
        ':order_date' => $order_date
    ]);

    $order_id = $pdo->lastInsertId();

    // Insert into order_items
    $item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, book_id, quantity) VALUES (:order_id, :book_id, :quantity)");
    foreach ($cart_items as $item) {
        $item_stmt->execute([
            ':order_id' => $order_id,
            ':book_id' => $item['book_id'],
            ':quantity' => $item['quantity']
        ]);
    }

    // Clear cart
    $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id")->execute([':user_id' => $user_id]);

    $message[] = "Order placed successfully!";
    header("Location: orders.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <div class="header">
        <a href="home.php" class="logo">𝕿𝖆𝖙𝖙𝖑𝖊𝕿𝖆𝖑𝖊</a>
        <nav>
            <a href="home.php">Home</a>
            <a href="about.php">About</a>
            <a href="bookstore.php">Bookstore</a>
            <a href="orders.php">Orders</a>
        </nav>
    </div>
</header>

<section class="checkout-form">
    <h2>Checkout</h2>

    <?php foreach ($message as $msg): ?>
        <p class="message"><?= $msg ?></p>
    <?php endforeach; ?>

    <?php if (!empty($cart_items)): ?>
        <form action="" method="POST">
            <div class="input-group">
                <label>Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="input-group">
                <label>Phone Number</label>
                <input type="text" name="number" required>
            </div>
            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="input-group">
                <label>Payment Method</label>
                <select name="payment_method" required>
                    <option value="COD">Cash on Delivery</option>
                    <option value="Card">Credit/Debit Card</option>
                    <option value="UPI">UPI</option>
                </select>
            </div>
            <div class="input-group">
                <label>Shipping Address</label>
                <textarea name="address" required></textarea>
            </div>
            <div class="total-price">
                <p>Grand Total: ₹<?= number_format($total_price, 2) ?>/-</p>
            </div>
            <input type="submit" value="Place Order" class="btn">
        </form>
    <?php endif; ?>
</section>

<footer>
    <section class="box-container">
        <div class="box">
            <h3>Quick Links</h3>
            <a href="about.php">About</a>
            <a href="bookstore.php">E-Store</a>
            <a href="#">Contact</a>
        </div>
        <div class="box">
            <h3>Extra Links</h3>
            <a href="register.php">Register</a>
            <a href="cart.php">Cart</a>
            <a href="orders.php">Orders</a>
        </div>
        <div class="box">
            <h3>Contact Info</h3>
            <p>+91 33 36869091</p>
            <p>priyanka@mail.com</p>
            <p>Calcutta, India, 700019</p>
        </div>
        <div class="box">
            <h3>Follow Us</h3>
            <a href="#">Twitter</a>
            <a href="#">Instagram</a>
            <a href="#">LinkedIn</a>
        </div>
    </section>
</footer>

<section class="credit">
    <p>&copy; <?= date("Y") ?> <span>Priyanka Mukherjee. All rights reserved</span></p>
</section>

</body>
</html>
