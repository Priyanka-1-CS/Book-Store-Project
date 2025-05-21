<?php

session_start();
require_once "includes/connect_db.inc.php";

// Checking if user is logged in and if they are an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: index.php"); // Redirect to login page if not an admin
    exit();
}


// Fetch key metrics for the admin dashboard (e.g., total users, orders, and books)
try {
    $stmtUsers = $pdo->query("SELECT COUNT(*) FROM bookstore.users");
    $totalUsers = $stmtUsers->fetchColumn();

    $stmtOrders = $pdo->query("SELECT COUNT(*) FROM bookstore.orders");
    $totalOrders = $stmtOrders->fetchColumn();

    $stmtBooks = $pdo->query("SELECT COUNT(*) FROM bookstore.books");
    $totalBooks = $stmtBooks->fetchColumn();
} catch (PDOException $e) {
    $error = "Error fetching data: " . $e->getMessage();
}

// Handle payment status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_payment_status'])) {
    $orderId = $_POST['order_id'];
    $newStatus = $_POST['payment_status'];

    try {
        // Update payment status in the database
        $stmtUpdate = $pdo->prepare("UPDATE bookstore.orders SET payment_status = :payment_status WHERE id = :order_id");
        $stmtUpdate->execute([':payment_status' => $newStatus, ':order_id' => $orderId]);
        $successMessage = "Payment status updated successfully!";
    } catch (PDOException $e) {
        $errorMessage = "Error updating payment status: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Force the browser to reload the CSS with a version query string -->
    <link rel="stylesheet" href="css/admindashboard.css?v=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <header>
        <div class="header">
            <a href="admin_home.php" class="logo">𝕿𝖆𝖙𝖙𝖑𝖊𝕿𝖆𝖑𝖊</a>

            <nav>
                <a href="admin_bookstore.php">Bookstore</a>
                <a href="admin_manage_books.php">Manage Books</a>
                <a href="admin_manage_users.php">Manage Users</a>
                <a href="admin_manage_orders.php">Manage Orders</a>
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
        <section class="dashboard">
            <h2 style="text-transform: uppercase; background-color:green; color:white; border-radius: 5px;">Welcome to the Admin Dashboard</h2>

            <div class="overview">
                <div class="overview-box">
                    <h3>Total Users</h3>
                    <p><?php echo $totalUsers; ?></p>
                </div>
                <div class="overview-box">
                    <h3>Total Orders</h3>
                    <p><?php echo $totalOrders; ?></p>
                </div>
                <div class="overview-box">
                    <h3>Total Books</h3>
                    <p><?php echo $totalBooks; ?></p>
                </div>
            </div>

            <h3 style="text-align:center; text-transform: uppercase; color:black; border-radius: 5px;">Latest Activity</h3>
            <div class="latest-activity">
                <?php
                // Fetch the most recent 5 orders
                try {
                    $stmtLatestOrders = $pdo->query("SELECT * FROM bookstore.orders ORDER BY order_date DESC LIMIT 5");
                    
                    // Start the table
                    echo "<table border='1' cellpadding='10' cellspacing='0' style='width: 100%; border-collapse: collapse;'>";
                    echo "<thead>";
                    echo "<tr>";
                    echo "<th style='text-align: center;'>Order ID</th>";
                    echo "<th style='text-align: center;'>Name</th>";
                    echo "<th style='text-align: center;'>User ID</th>";
                    echo "<th style='text-align: center;'>Payment Status</th>";
                    echo "<th style='text-align: center;'>Order Date</th>";
                    echo "<th style='text-align: center;'>Action</th>"; // Adding Action column for updating payment status
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";
                    
                    // Fetch and display each order in a table row
                    while ($order = $stmtLatestOrders->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td style='text-align: center;'>" . $order['order_id'] . "</td>";
                        echo "<td style='text-align: center;'>" . $order["name"] . "</td>";
                        echo "<td style='text-align: center;'>" . $order['user_id'] . "</td>";
                        echo "<td style='text-align: center;'>" . $order['payment_status'] . "</td>";
                        echo "<td style='text-align: center;'>" . $order['order_date'] . "</td>";
                        
                        // Display a form for updating payment status
                        echo "<td style='text-align: center;'>
                                <form method='POST' action=''>
                                    <input type='hidden' name='order_id' value='" . $order['order_id'] . "'>
                                    <select name='payment_status' style='text-align: center;'>
                                        <option value='Paid' " . ($order['payment_status'] == 'Paid' ? 'selected' : '') . ">Paid</option>
                                        <option value='Pending' " . ($order['payment_status'] == 'Pending' ? 'selected' : '') . ">Pending</option>
                                        <option value='Failed' " . ($order['payment_status'] == 'Failed' ? 'selected' : '') . ">Failed</option>
                                    </select>
                                    <button type='submit' name='update_payment_status'>Update</button>
                                </form>
                            </td>";
                        echo "</tr>";
                    }
                    
                    // Close the table
                    echo "</tbody>";
                    echo "</table>";

                    // Display success or error message
                    if (isset($successMessage)) {
                        echo "<p style='color: green;'>$successMessage</p>";
                    } elseif (isset($errorMessage)) {
                        echo "<p style='color: red;'>$errorMessage</p>";
                    }
                } catch (PDOException $e) {
                    echo "Error fetching latest orders: " . $e->getMessage();
                }
                ?>
            </div>
        </section>
    </main>

    <footer>
        <section class="credit">
            <p>Copyright @ <?php echo date('Y'); ?> <span>Priyanka Mukherjee. All rights reserved</span></p>
        </section>
    </footer>

</body>
</html>
