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