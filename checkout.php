<?php
session_start();
require_once "includes/connect_db.inc.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = [];

// Fetch cart items
$stmt = $pdo->prepare("SELECT c.*, b.title, b.price FROM cart c JOIN books b ON c.book_id = b.book_id WHERE c.user_id = :user_id");
$stmt->execute([':user_id' => $user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cart_items)) {
    $message[] = "Your cart is empty!";
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($cart_items)) {
    $name = trim(htmlspecialchars($_POST['name']));
    $number = trim(htmlspecialchars($_POST['number']));
    $email = trim(htmlspecialchars($_POST['email']));
    $payment_method = htmlspecialchars($_POST['payment_method']);
    $address = trim(htmlspecialchars($_POST['address']));
    $order_date = date('Y-m-d H:i:s');
    $total_price = 0;

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message[] = "Invalid email address.";
    }

    if (!preg_match("/^[0-9]{10}$/", $number)) {
        $message[] = "Phone number must be 10 digits.";
    }

    if (empty($message)) {
        foreach ($cart_items as $item) {
            if ((int)$item['quantity'] > 0) {
                $total_price += $item['price'] * $item['quantity'];
            }
        }

        if ($total_price <= 0) {
            $message[] = "Invalid cart total. Please check quantities.";
        } else {
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

            $item_stmt = $pdo->prepare("INSERT INTO order_items (order_id, book_id, quantity) VALUES (:order_id, :book_id, :quantity)");
            foreach ($cart_items as $item) {
                if ((int)$item['quantity'] > 0) {
                    $item_stmt->execute([
                        ':order_id' => $order_id,
                        ':book_id' => $item['book_id'],
                        ':quantity' => $item['quantity']
                    ]);
                }
            }

            $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id")->execute([':user_id' => $user_id]);

            $_SESSION['order_success'] = "Order placed successfully!";
            header("Location: orders.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .checkout-form {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
        }

        .input-group {
            margin-bottom: 15px;
        }

        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .input-group input,
        .input-group textarea,
        .input-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .message {
            background-color: #ffe0e0;
            color: #a33;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            text-align: center;
        }

        .btn {
            background-color: #2c3e50;
            color: #fff;
            padding: 10px 25px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #1a252f;
        }

        .order-summary {
            margin-top: 25px;
        }

        .order-summary h3 {
            margin-bottom: 10px;
            color: #2c3e50;
            font-size: 20px;
        }

        .order-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #f9f9f9;
            box-shadow: 0 0 6px rgba(0, 0, 0, 0.05);
        }

        .order-table th,
        .order-table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: center;
            font-size: 15px;
        }

        .order-table th {
            background-color: #2c3e50;
            color: #fff;
        }

        .order-table .grand-total-row td {
            font-size: 16px;
            background-color: #e8f5e9;
            color: #2e7d32;
            font-weight: bold;
        }

        header .header {
            /* padding: 15px 20px; */
            background-color: #334a14;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
        }

        .header .logo {
            color: #fff;
            font-size: 24px;
            text-decoration: none;
            font-weight: bold;
        }

        nav a {
            color: #fff;
            margin-left: 15px;
            text-decoration: none;
        }

        footer {
            background-color: #334a14;;
            padding: 15px 0;
            margin-top: 40px;
            color: white;
        }

        .box-container {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            padding: 10px 30px;
        }

        .box {
            margin: 10px;
        }

        .box h3 {
            margin-bottom: 10px;
        }

        .box a,
        .box p {
            display: block;
            margin-bottom: 6px;
            color: #fff;
            text-decoration: none;
        }

        .credit {
            text-align: center;
            padding: 10px;
            background-color: black;
        }
    </style>
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
        <!-- Order Summary -->
        <div class="order-summary">
            <h3>Order Summary</h3>
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Price (₹)</th>
                        <th>Qty</th>
                        <th>Subtotal (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $grand_total = 0; ?>
                    <?php foreach ($cart_items as $item): ?>
                        <?php $subtotal = $item['price'] * $item['quantity']; ?>
                        <tr>
                            <td><?= htmlspecialchars($item['title']) ?></td>
                            <td><?= number_format($item['price'], 2) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td><?= number_format($subtotal, 2) ?></td>
                        </tr>
                        <?php $grand_total += $subtotal; ?>
                    <?php endforeach; ?>
                    <tr class="grand-total-row">
                        <td colspan="3" style="text-align: right;">Grand Total:</td>
                        <td>₹<?= number_format($grand_total, 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Checkout Form -->
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
