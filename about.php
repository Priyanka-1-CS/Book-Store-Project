<?php

require_once "includes/connect_db.inc.php";
session_start();
// Check if the user is logged in
if (!isset($_SESSION["user_id"])) {
   header("Location: index.php");  
   exit();  
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>about</title>

   <link rel="stylesheet" href="css/gen_style.css">
   <style>
   .homea {
    position: relative;
    text-align: center;
    margin-top: 120px;
}

.bg {
    width: 100%;
    height: 80vh;
    object-fit: cover;
    opacity: 0.44;
}

/* New container for text over image */
.homea .content {
    position: absolute;
    top: 10%;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1;
    width: 90%;
    max-width: 900px;
    color: rgb(0, 0, 0);
    text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.6);
    text-transform: uppercase;
    font-weight: bold;
}

.homea .content h2 {
    font-size: 48px;
    font-weight: bold;
    margin-bottom: 20px;
}

.homea .content ul {
    list-style-type: disc;
    text-align: left;
    margin: 0 auto;
    padding: 0 20px;
}

.homea .content ul li {
    font-size: 20px;
    /* margin-bottom: 10px; */
    text-transform: none;
}


   </style>

</head>
<body>
   
<header>
        <div class="header">
            <a href="home.php" class="logo">𝕿𝖆𝖙𝖙𝖑𝖊𝕿𝖆𝖑𝖊</a>
            <nav>
                <a href="home.php">Home</a>
                <a href="about.php">About</a>
                <a href="orders.php">Orders</a>
                <!-- <a href="contact.php">Contact</a> -->
                <a href="cart.php">Cart</a>
            </nav>
            <div class="icons">
                <?php if (isset($_SESSION["user_id"])): ?>
                    <a href="logout.php" class="delete-btn">Logout</a>
                <?php else: ?> 
                    <a href="index.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </header>


<main>
<section class="homea">
    <img src="images/woman reading-pana.png" alt="Reading woman" class="bg">

    <div class="content">
        <h2>Why choose us?</h2>
        <ul>
            <li>Facility to choose from a wide range of books on different subjects.</li>
            <li>Facility to choose from books written by both; novice as well as established authors.</li>
            <li>100% original books.</li>
            <li>Books available at affordable prices.</li>
            <li>Facility of comparing the book price with the market value.</li>
            <li>Cash on Delivery facility available.</li>
            <li>Free Home Delivery facility available.</li>
            <li>100% Secure and Safe Shopping.</li>
        </ul>
    </div>
</section>

</main>



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
    <!-- Date('Y'): always shows the current year  -->
   <p>Copyright @ <?php echo date('Y'); ?> <span>Priyanka Mukherjee. All rights reserved</span></p>
   </section>

<script src="js/script.js"></script>

</body>
</html>
