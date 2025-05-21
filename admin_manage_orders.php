<?php
session_start();
require_once "includes/connect_db.inc.php";

// Checking if user is logged in and if they are an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: index.php");
    exit();
}

// Handle deletion of orders
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_order'])) {
    $order_id = $_POST['order_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM bookstore.orders WHERE id = :order_id");
        $stmt->bindParam(":order_id", $order_id);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error deleting order: " . $e->getMessage();
    }
}

// Fetch all orders for management
try {
    $stmt = $pdo->query("
            SELECT 
            o.order_id,
            o.name,
            o.email,
            o.order_date,
            o.total_price,
            o.payment_status,
            b.title AS book_title
            FROM orders o
            JOIN books b ON o.book_id = b.book_id
        ");
} catch (PDOException $e) {
    echo "Error fetching orders: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/admindashboard.css">
    <title>Manage Orders</title>
</head>
<body>
    <header>
        <div class="header">
        <a href="admin_home.php" class="logo">𝕿𝖆𝖙𝖙𝖑𝖊𝕿𝖆𝖑𝖊</a>
            <nav>
                <a href="admindashboard.php">Dashboard</a>
                <a href="admin_bookstore.php">Bookstore</a>
                <a href="admin_manage_books.php">Manage Books</a>
                <a href="admin_manage_users.php">Manage Users</a>                
            </nav>

            <div class="icons">
        <?php 
        if (isset($_SESSION['user_id'])):
        ?>
        <a href="logout.php" class="delete-btn">Logout</a>
        <?php else: ?>
            <a href="index.php">Login</a>
        <?php endif; ?>
      </div>
        </div>
    </header>

    <main>
        <h2 style="text-transform: uppercase; text-align:center; color:black;">Manage Orders</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Books Ordered</th>
                    <th>Order Date Time</th>
                    <th>Total Price</th>
                    <th>Payment Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($order = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order["order_id"]); ?></td>
                        <td><?php echo htmlspecialchars($order["name"]); ?></td>
                        <td><?php echo htmlspecialchars($order['email']); ?></td>
                        <td><?php echo htmlspecialchars($order['book_title']); ?></td>
                        <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                        <td>₹<?php echo htmlspecialchars($order['total_price']); ?></td>
                        <td><?php echo htmlspecialchars($order["payment_status"]); ?></td>
                        <td>
                        <a href="admin_view_order.php?order_id=<?php echo $order["order_id"]; ?>" class="view-btn"><button>View</button></a>
                            <form action="admin_manage_orders.php" method="POST" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?php echo $order["order_id"]; ?>">
                                <button type="submit" name="delete_order" onclick="return confirm('Are you sure you want to delete this order?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>

    <footer>
        <section class="credit">
            <p>Copyright @ <?php echo date('Y'); ?> <span>Priyanka Mukherjee. All rights reserved</span></p>
        </section>
    </footer>
</body>
</html>
