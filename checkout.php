<?php
try {
    require_once "includes/connect_db.inc.php";
    session_start();

    // Checking if the user is logged in
    if (!isset($_SESSION["user_id"])) {
        header("Location: index.php"); // Redirect to login page if not logged in
        exit();
    }

    $user_id = $_SESSION["user_id"];

    // Fetch cart items for the logged-in user
    $stmt = $pdo->prepare("SELECT cart_id, book_id, title, quantity, price, image FROM bookstore.cart WHERE user_id = :user_id;");
    $stmt->execute(['user_id' => $user_id]);
    $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate total price
    $total_price = 0;
    $book_ids = []; // Array to store book ids
    $quantities = []; // Array to store quantities

    foreach ($cart_items as $item) {
        $total_price += $item['price'] * $item['quantity'];
        // Store book_id and quantity in arrays
        $book_ids[] = $item['book_id'];
        $quantities[] = $item['quantity'];
    }

    // If no items in the cart, notify user and stop execution
    if (count($cart_items) === 0) {
        header("Location: cart.php");
        // echo "<p>Your cart is empty. Please add items to your cart before proceeding.</p>";
        exit();
    }


    // Handle form submission for placing the order
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Sanitize and validate form data
        $name = htmlspecialchars($_POST['name']);
        $number = htmlspecialchars($_POST['number']);
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) ? $_POST['email'] : null;
        $address = htmlspecialchars($_POST['address']);
        $payment_method = htmlspecialchars($_POST['payment_method']);

        // Validate form inputs
        if (!$email) {
            echo "<p>Please enter a valid email address.</p>";
            exit();
        }

        // Start transaction for inserting order
        $pdo->beginTransaction();

        try {
            // Convert book_ids and quantities arrays to comma-separated strings
            $book_ids_str = implode(",", $book_ids);
            $quantities_str = implode(",", $quantities);

            // Insert order into orders table (with book_ids and quantities as comma-separated values)
            $stmt = $pdo->prepare("INSERT INTO bookstore.orders (user_id, name, number, email, payment_method, address, total_price, order_date, payment_status, book_ids, quantities) 
                                   VALUES (:user_id, :name, :number, :email, :payment_method, :address, :total_price, NOW(), 'Pending', :book_ids, :quantities)");

            // Execute order insert
            $stmt->execute([
                'user_id' => $user_id,
                'name' => $name,
                'number' => $number,
                'email' => $email,
                'payment_method' => $payment_method,
                'address' => $address,
                'total_price' => $total_price,
                'book_ids' => $book_ids_str,
                'quantities' => $quantities_str
            ]);

            // Get the order ID of the inserted order
            $order_id = $pdo->lastInsertId();

            // Clear the cart after successful order
            $stmt = $pdo->prepare("DELETE FROM bookstore.cart WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $user_id]);

            // Commit the transaction
            $pdo->commit();

            // Display the confirmation message
            echo "<div class='order-confirmation'>
                    <h2>Order Confirmation</h2>
                    <p>Your order has been successfully placed! Order ID: <strong>$order_id</strong></p>
                    <p>Total Price: ₹" . number_format($total_price, 2) . "</p>
                    <p>Payment Method: " . htmlspecialchars($payment_method) . "</p>
                    <p>Thank you for shopping with us!</p>
                  </div>";
        } catch (PDOException $e) {
            // Rollback the transaction in case of an error
            $pdo->rollBack();
            echo "<p>Error processing the order: " . $e->getMessage() . "</p>";
        }
    }
} catch (PDOException $e) {
    echo "<p>Error fetching cart items: " . $e->getMessage() . "</p>";
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Bookstore</title>
    <link rel="stylesheet" href="css/checkout.css">
</head>
<body>

<header>
<div class="header">
      <a href="home.php" class="logo">𝕿𝖆𝖙𝖙𝖑𝖊𝕿𝖆𝖑𝖊</a>

      <nav>
         <a href="about.php">About</a>
         <a href="bookstore.php">BookStore</a>
         <a href="orders.php">Orders</a>
         <a href="contact.php">Contact</a>
         <a href="cart.php">Cart</a>
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
    <section class="cart-items">
        <!-- <h2>Your Cart</h2> -->

        <!-- Cart Table -->
        <table class="cart-table">
            <caption><h1>Your Cart</h1></caption>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($cart_items) > 0): ?>
                    <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td><img src="/Book_Store/<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>"></td>
                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>₹<?php echo number_format($item['price'], 2); ?></td>
                            <td>₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Your cart is empty.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Total Amount -->
        <!-- <div class="total-amount">
            <strong>Total Amount: ₹<?php 
            // echo number_format($total_price, 2); 
            ?></strong>
        </div> -->

        <!-- Checkout Form -->
        <?php if (!isset($order_id)): ?>
            <form class="checkout-form" method="POST">
                <h2>Shipping Information</h2>
                <input type="text" name="name" placeholder="Enter Your Name" required>
                <input type="text" name="number" placeholder="Enter Your Phone Number" required>
                <input type="email" name="email" placeholder="Enter Your Email" required>
                <input name="address" placeholder="Enter Your Shipping Address" required>
                <select name="payment_method" required>
                    <option value="">Select Payment Method</option>
                    <option value="Credit Card">Credit Card</option>
                    <option value="PayPal">PayPal</option>
                    <option value="COD">Cash on Delivery</option>
                </select>
                <button type="submit">Place Order</button>
            </form>
        <?php endif; ?>
    </section>
</main>

<footer>
<section class="box-container">
   <div class="box">
      <h3>Quick Links</h3>
      <a href="about.php">About</a>
      <a href="shop.php">E-Store</a>
      <a href="contact.php">Reach Out to us</a>
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
      <a href="#">Linkedin</a>
   </div>
</section>
</footer>

   <section class="credit">
   <p>Copyright @ <?php echo date('Y'); ?> <span>Priyanka Mukherjee. All rights reserved</span></p>
   </section>

</body>
</html>
