<?php
session_start();
require_once "includes/connect_db.inc.php";

// Check if admin is logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: index.php");
    exit();
}

// Validate and get order ID
if (!isset($_GET['order_id']) || !ctype_digit($_GET['order_id'])) {
    echo "Invalid order ID!";
    exit();
}

$order_id = (int)$_GET['order_id'];

// Fetch order info
try {
    $stmtOrder = $pdo->prepare("SELECT * FROM orders WHERE order_id = :order_id");
    $stmtOrder->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmtOrder->execute();
    $order = $stmtOrder->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo "Order not found!";
        exit();
    }

    // Fetch books in the order
    $stmtBooks = $pdo->prepare("
        SELECT b.title, b.author, b.price, oi.quantity
        FROM order_items oi
        JOIN books b ON oi.book_id = b.book_id
        WHERE oi.order_id = :order_id
    ");
    $stmtBooks->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmtBooks->execute();
    $books = $stmtBooks->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// Handle payment status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_payment_status'])) {
    $allowed_statuses = ['Paid', 'Pending', 'Failed'];
    $newStatus = $_POST['payment_status'] ?? '';

    if (!in_array($newStatus, $allowed_statuses, true)) {
        echo "Invalid payment status!";
        exit();
    }

    try {
        $stmtUpdate = $pdo->prepare("UPDATE orders SET payment_status = :payment_status WHERE order_id = :order_id");
        $stmtUpdate->bindParam(':payment_status', $newStatus);
        $stmtUpdate->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmtUpdate->execute();

        header("Location: admin_view_order.php?order_id=$order_id");
        exit();
    } catch (PDOException $e) {
        echo "Error updating status: " . $e->getMessage();
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="css/admindashboard.css" />
    <title>View Order</title>
</head>
<body>
    <header>
        <div class="header">
            <a href="admin_home.php" class="logo">𝕿𝖆𝖙𝖙𝖑𝖊𝕿𝖆𝖑𝖊</a>
            <nav>
                <a href="admindashboard.php">Dashboard</a>
                <a href="admin_bookstore.php">Bookstore</a>
                <a href="admin_manage_orders.php">Manage Orders</a>
                <a href="admin_manage_books.php">Manage Books</a>
                <a href="admin_manage_users.php">Manage Users</a>
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

    <main>
        <h2 style="text-transform: uppercase; text-align:center; color:black;">Order Details</h2>

        <div class="order-details">
            <table>
                <tr><th>Order ID</th><td><?= htmlspecialchars($order['order_id']) ?></td></tr>
                <tr><th>Customer Name</th><td><?= htmlspecialchars($order['name']) ?></td></tr>
                <tr><th>Email</th><td><?= htmlspecialchars($order['email']) ?></td></tr>
                <tr><th>Contact</th><td><?= htmlspecialchars($order['number']) ?></td></tr>
                <tr>
                    <th>Books Ordered</th>
                    <td>
                        <ul style="list-style-type: disc; padding-left: 20px;">
                            <?php if (!empty($books)): ?>
                                <?php foreach ($books as $book): ?>
                                    <li>
                                        <?= htmlspecialchars($book['title']) ?> 
                                        by <?= htmlspecialchars($book['author']) ?> — 
                                        <?= $book['quantity'] ?> × ₹<?= number_format($book['price'], 2) ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li>No books found in this order.</li>
                            <?php endif; ?>
                        </ul>
                    </td>
                </tr>
                <tr><th>Order Date Time</th><td><?= htmlspecialchars($order['order_date']) ?></td></tr>
                <tr><th>Total Price</th><td>₹<?= number_format($order['total_price'], 2) ?></td></tr>
                <tr><th>Payment Mode</th><td><?= htmlspecialchars($order['payment_method']) ?></td></tr>
                <tr><th>Payment Status</th><td><?= htmlspecialchars($order['payment_status']) ?></td></tr>
            </table>

            <form style="text-align: center;" action="admin_view_order.php?order_id=<?= $order_id ?>" method="POST">
                <label for="payment_status"><b>Update Payment Status</b></label>
                <select name="payment_status" id="payment_status" style="border-radius: 5px; padding:5px;">
                    <option value="Paid" <?= $order['payment_status'] === 'Paid' ? 'selected' : '' ?>>Paid</option>
                    <option value="Pending" <?= $order['payment_status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Failed" <?= $order['payment_status'] === 'Failed' ? 'selected' : '' ?>>Failed</option>
                </select>
                <button type="submit" name="update_payment_status"><b>Update Status</b></button>
            </form>
        </div>
    </main>

    <footer>
        <section class="credit">
            <p>Copyright @ <?= date('Y') ?> <span>Priyanka Mukherjee. All rights reserved</span></p>
        </section>
    </footer>
</body>
</html>




<!-- CREATE TABLE view_order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    book_id INT,
    quantity INT,
    price DECIMAL(10, 2),
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books(book_id)
); -->