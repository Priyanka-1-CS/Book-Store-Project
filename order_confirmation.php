<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php"); // Redirect to login page if not logged in
    exit();
}

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
} else {
    echo "<p>No order ID found.</p>";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <h1>Order Confirmation</h1>
</header>

<main>
    <section>
        <h2>Your order has been successfully placed!</h2>
        <p>Your order ID is: <strong><?php echo htmlspecialchars($order_id); ?></strong></p>
        <p>Thank you for shopping with us!</p>
    </section>
</main>

</body>
</html>
