<?php
session_start();
require_once "includes/connect_db.inc.php";

// Check if user is logged in and if they are an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: index.php");
    exit();
}

// Fetch the order_id from URL
if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    try {
        // Fetch the order details
        $stmtOrder = $pdo->prepare("SELECT * FROM bookstore.orders WHERE id = :order_id");
        $stmtOrder->bindParam(":order_id", $order_id, PDO::PARAM_INT);
        $stmtOrder->execute();

        $order = $stmtOrder->fetch(PDO::FETCH_ASSOC);

        // If the order doesn't exist
        if (!$order) {
            echo "Order not found!";
            exit();
        }

        // Fetch book details for the order
        // $stmtBooks = $pdo->prepare("SELECT * FROM bookstore.orders WHERE id = :order_id;");
        // $stmtBooks->bindParam(":order_id", $order_id, PDO::PARAM_INT);
        // $stmtBooks->execute();

        // $stmtBooks = $pdo->prepare("SELECT * FROM bookstore.books WHERE book_id = :order_id;");
        // $stmtBooks->bindParam(":order_id", $order_id, PDO::PARAM_INT);
        // $stmtBooks->execute();

    } catch (PDOException $e) {
        echo "Error fetching order details: " . $e->getMessage();
    }
} else {
    echo "Invalid order ID!";
    exit();
}




// Handle payment status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_payment_status'])) {
    $newStatus = $_POST['payment_status'];

    try {
        $stmtUpdate = $pdo->prepare("UPDATE bookstore.orders SET payment_status = :payment_status WHERE id = :order_id");
        $stmtUpdate->bindParam(':payment_status', $newStatus);
        $stmtUpdate->bindParam(':order_id', $order_id);
        $stmtUpdate->execute();

        // Redirect to the same page to reflect the change
        header("Location: admin_view_order.php?order_id=$order_id");
        exit();

    } catch (PDOException $e) {
        echo "Error updating payment status: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/admindashboard.css">
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

        <!-- Order Details Table -->
        <div class="order-details">
            <table>
                <tr>
                    <th>Order ID</th>
                    <td><?php echo htmlspecialchars($order['id']); ?></td>
                </tr>
                <tr>
                    <th>Customer Name</th>
                    <td><?php echo htmlspecialchars($order['name']); ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?php echo htmlspecialchars($order['email']); ?></td>
                </tr>
                <tr>
                    <th>Contact</th>
                    <td><?php echo htmlspecialchars($order["number"]); ?></td>
                </tr>
                <tr>
                    <th>Book Ordered</th>
                    <td><?php echo htmlspecialchars($order['book_name']); ?></td>
                </tr>
                <tr>
                    <th>Quantity</th>
                    <td><?php echo htmlspecialchars($order['quantities']); ?></td>
                </tr>
                <tr>
                    <th>Order Date Time</th>
                    <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                </tr>
                <tr>
                    <th>Total Price</th>
                    <td>₹<?php echo htmlspecialchars($order['total_price']); ?></td>
                </tr>
                <tr>
                    <th>Payment Mode</th>
                    <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                </tr>
                <tr>
                    <th>Payment Status</th>
                    <td><?php echo htmlspecialchars($order['payment_status']); ?></td>
                </tr>
            </table>

            <form style="text-align: center;" action="admin_view_order.php?order_id=<?php echo $order_id; ?>" method="POST">
                <label style="font-weight: bold;" for="payment_status">Update Payment Status </label>
                <select name="payment_status" id="payment_status" style="border-radius: 5px; padding:5px; text-align:center;">
                    <option value="Paid" <?php echo ($order['payment_status'] == 'Paid' ? 'selected' : ''); ?>>Paid</option>
                    <option value="Pending" <?php echo ($order['payment_status'] == 'Pending' ? 'selected' : ''); ?>>Pending</option>
                    <option value="Failed" <?php echo ($order['payment_status'] == 'Failed' ? 'selected' : ''); ?>>Failed</option>
                </select>
                <button style="font-weight: bold;" type="submit" name="update_payment_status">Update Status</button>
            </form>
        </div>

        <!-- Books in Order -->
        <!-- <h3>Books Ordered</h3>
        <table>
            <thead>
                <tr>
                    <th>Book Title</th>
                     <th>Author</th> -->
                    <!-- <th>Quantity</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // while ($book = $stmtBooks->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php 
                        // echo htmlspecialchars($book["title"]); ?></td>
                        <td><?php
                        //  echo htmlspecialchars($book['author']); 
                         ?></td> -->
                        
                        <!-- <td><?php 
                        // echo htmlspecialchars($book["quantities"]); 
                        ?></td> -->

                        <!-- <td>₹<?php
                        //  echo htmlspecialchars($book["price"]); 
                        ?></td> -->

                    <!-- </tr>
                <?php 
            // endwhile; 
            ?>
            </tbody>
        </table> -->

    </main>

    <footer>
        <section class="credit">
            <p>Copyright @ <?php echo date('Y'); ?> <span>Priyanka Mukherjee. All rights reserved</span></p>
        </section>
    </footer>

</body>
</html>
