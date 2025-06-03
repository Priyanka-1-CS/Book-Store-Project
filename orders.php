<?php
session_start();
require_once "includes/connect_db.inc.php";

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "User not logged in!";
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch orders for the current user
function getUserOrders($pdo, $user_id) {
    $sql = "SELECT * FROM orders WHERE user_id = :user_id ORDER BY order_date DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch books in an order
function getOrderBooks($pdo, $order_id) {
    $sql = "SELECT b.title, b.author, b.price, oi.quantity
            FROM order_items oi
            JOIN books b ON oi.book_id = b.book_id
            WHERE oi.order_id = :order_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['order_id' => $order_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$orders = getUserOrders($pdo, $user_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders - Online Bookstore</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">

    <!-- Additional Table Styling -->
    <style>
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }

        .order-card {
            background-color: #fff;
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .order-card p {
            margin: 6px 0;
            font-size: 15px;
            color: #444;
        }

        .order-card h4 {
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 18px;
            color: #222;
        }

        .book-list {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }

        .book-list th,
        .book-list td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        .book-list th {
            background-color: #f4f4f4;
            font-weight: 600;
        }

        .book-list td {
            background-color: #fff;
        }

        .status {
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }

        .status.Paid {
            color: green;
        }

        .status.Pending {
            color: orange;
        }

        .status.Failed {
            color: red;
        }
    </style>
</head>
<body>

<!-- Header -->
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
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="logout.php" class="delete-btn">Logout</a>
            <?php else: ?>
                <a href="index.php">Login</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- Main -->
<main>
    <div class="container">
        <h1>My Orders</h1>

        <?php if (empty($orders)): ?>
            <p style="text-align:center;">No orders found.</p>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <p><strong>Order ID:</strong> <?= $order['order_id'] ?></p>
                    <p><strong>Name:</strong> <?= htmlspecialchars($order['name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($order['number']) ?></p>
                    <p><strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></p>
                    <p><strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
                    <p><strong>Payment Status:</strong> <span class="status <?= htmlspecialchars($order['payment_status']) ?>"><?= htmlspecialchars($order['payment_status']) ?></span></p>
                    <p><strong>Order Date:</strong> <?= $order['order_date'] ?></p>
                    <p><strong>Total Amount:</strong> ₹<?= number_format($order['total_price'], 2) ?></p>

                    <h4>Books in this order:</h4>
                    <?php
                        $books = getOrderBooks($pdo, $order['order_id']);
                        if (!empty($books)):
                    ?>
                    <table class="book-list">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Price (₹)</th>
                                <th>Quantity</th>
                                <th>Subtotal (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($books as $book): ?>
                                <tr>
                                    <td><?= htmlspecialchars($book['title']) ?></td>
                                    <td><?= htmlspecialchars($book['author']) ?></td>
                                    <td><?= number_format($book['price'], 2) ?></td>
                                    <td><?= $book['quantity'] ?></td>
                                    <td><?= number_format($book['price'] * $book['quantity'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                        <p>No books found in this order.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<!-- Footer -->
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

<section class="credit">
    <p>&copy; <?= date('Y') ?> <span>Priyanka Mukherjee. All rights reserved</span></p>
</section>

</body>
</html>

<?php $pdo = null; ?>
