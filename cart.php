<?php
session_start();
require_once "includes/connect_db.inc.php";

$user_id = $_SESSION['user_id'];  // Fixed: Removed extra single quotes

if (!isset($user_id)) {
    header('location:index.php');
    exit();
}

if (isset($_POST['update_cart'])) {
    $cart_id = $_POST['cart_id'];
    $cart_quantity = $_POST['cart_quantity'];

    $stmt = $pdo->prepare("UPDATE `cart` SET quantity = :quantity WHERE cart_id = :cart_id");
    $stmt->execute([':quantity' => $cart_quantity, ':cart_id' => $cart_id]);

    $message[] = 'Cart quantity updated!';
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    $stmt = $pdo->prepare("DELETE FROM `cart` WHERE cart_id = :delete_id");
    $stmt->execute([':delete_id' => $delete_id]);

    header('location:cart.php');
    exit();
}

if (isset($_GET['delete_all'])) {
    $stmt = $pdo->prepare("DELETE FROM `cart` WHERE user_session_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);

    header('location:cart.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="css/gen_style.css">
  
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
            <div class="icons">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="delete-btn">Logout</a>
                <?php else: ?>
                    <a href="index.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <section class="shopping-cart">
        <h2 class="cart">Your Cart</h2>

        <div class="cart-container">
            <?php
            $grand_total = 0;
            $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = :user_id");  // Fixed: used user_session_id for consistency
            $stmt->execute([':user_id' => $user_id]);
            $select_cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($select_cart) > 0) {
                foreach ($select_cart as $fetch_cart) {
                    ?>
                    <div class="cart-item">
                        <a href="cart.php?delete=<?php echo $fetch_cart['cart_id']; ?>" class="Delete" onclick="return confirm('Delete this from cart?');">Delete</a>
                        <img src="<?php echo htmlspecialchars($fetch_cart['image']); ?>" alt="">
                        <div class="name"><?php echo htmlspecialchars($fetch_cart['title']); ?></div>
                        <div class="price">₹<?php echo $fetch_cart['price']; ?>/-</div>
                        <form action="" method="post">
                            <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['cart_id']; ?>">
                            <input type="number" min="1" name="cart_quantity" value="<?php echo $fetch_cart['quantity']; ?>">
                            <input type="submit" name="update_cart" value="Update" class="option-btn">
                        </form>
                        <div class="sub-total"> Sub Total: ₹<?php echo $fetch_cart['quantity'] * $fetch_cart['price']; ?>/-</div>
                    </div>
                    <?php
                    $grand_total += ($fetch_cart['quantity'] * $fetch_cart['price']);
                }
            } else {
                echo '<p class="empty">Your cart is empty.</p>';
            }
            ?>

        </div>

        <div class="cart-total">
            <p>Grand Total: ₹<?php echo $grand_total; ?>/-</p>
            <div class="flex">
                <a href="bookstore.php" class="option-btn">Continue Shopping</a>
                <a href="checkout.php" class="btn <?php echo ($grand_total > 0) ? '' : 'disabled'; ?>">Proceed to Checkout</a>
            </div>
        </div> 
    </section>


    <footer>
<section class="box-container">
   <div class="box">
      <h3>Quick Links</h3>
      <a href="about.php">About</a>
      <a href="shop.php">E-Store</a>
      <a href="#">Reach Out to us</a>
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

