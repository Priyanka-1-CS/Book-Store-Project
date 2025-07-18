<?php

session_start();

require_once "includes/connect_db.inc.php";

// Checking if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");  // Redirect to login page if user is not logged in
    exit();  // Always call exit() after a header redirect to stop further execution
}

$user_id = $_SESSION["user_id"];


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Home</title>
</head>
<body>
   
<header>
   <div class="header">
      <a href="home.php" class="logo">𝕿𝖆𝖙𝖙𝖑𝖊𝕿𝖆𝖑𝖊</a>

      <nav>
         <a href="about.php">About</a>
         <a href="bookstore.php">BookStore</a>
         <!-- <a href="contact.php">Contact</a> -->
         <a href="cart.php">Cart</a>
         <a href="orders.php">Orders</a>
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
<section class="home">
    <img src="images/bg3.jpg" alt="" class="background-cover">
    <div>
        <h3>Original Books to your doorsteps.
            <br>
        <a href="bookstore.php"><button>Discover More</button></a>
        </h3>
    </div>
</section>
</main>

<!-- Footer -->
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
