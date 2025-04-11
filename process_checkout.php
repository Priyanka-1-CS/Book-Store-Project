<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php"); // Redirect to login page if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        require_once "includes/connect_db.inc.php";

        // Collect form data
        $user_id = $_SESSION["user_id"];
        $name = $_POST['name'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $postal_code = $_POST['postal_code'];
        $phone = $_POST['phone'];
        $payment_method = $_POST['payment_method'];

        // Calculate total price from the user's cart
        $stmt = $pdo->prepare("SELECT SUM(c.quantity * c.price) AS total FROM bookstore.cart c WHERE c.user_id = :user_id;");
        $stmt->execute(['user_id' => $user_id]);
        $total_amount = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Insert order into orders table
        $stmt = $pdo->prepare("INSERT INTO bookstore.orders (user_id, name, address, city, postal_code, phone, payment_method, total_amount) VALUES (:user_id, :name, :address, :city, :postal_code, :phone, :payment_method, :total_amount)");
        $stmt->execute([
            'user_id' => $user_id,
            'name' => $name,
            'address' => $address,
            'city' => $city,
            'postal_code' => $postal_code,
            'phone' => $phone,
            'payment_method' => $payment_method,
            'total_amount' => $total_amount
        ]);

        // Get the order ID of the inserted order
        $order_id = $pdo->lastInsertId();

        // Insert cart items into order items table
        $stmt = $pdo->prepare("SELECT book_id, quantity FROM bookstore.cart WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($cart_items as $item) {
            $stmt = $pdo->prepare("INSERT INTO bookstore.order_items (order_id, book_id, quantity) VALUES (:order_id, :book_id, :quantity)");
            $stmt->execute([
                'order_id' => $order_id,
                'book_id' => $item['book_id'],
                'quantity' => $item['quantity']
            ]);
        }

        // Clear the cart after successful order
        $stmt = $pdo->prepare("DELETE FROM bookstore.cart WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);

        // Redirect to order confirmation page
        header("Location: order_confirmation.php?order_id=" . $order_id);
        exit();

    } catch (PDOException $e) {
        echo "<p>Error processing the checkout: " . $e->getMessage() . "</p>";
        exit();
    }
}
?>
