<?php

require_once "includes/connect_db.inc.php";

// session_start();

// $user_id = $_SESSION['user_id'];

// if(!isset($user_id)){
   // header('location:index.php');
// }

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>about</title>

   <!-- font awesome cdn link  -->
   <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"> -->

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/gen_style.css">

</head>
<body>
   
<header>
        <div class="header">
            <a href="home.php" class="logo">𝕿𝖆𝖙𝖙𝖑𝖊𝕿𝖆𝖑𝖊</a>
            <nav>
                <a href="about.php">About</a>
                <a href="orders.php">Orders</a>
                <a href="contact.php">Contact</a>
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


   <main>
<section class="homea">
    <img src="images/woman reading-pana.png" alt="" class="bg">
    <div>
        <h3>Original Books to your doorsteps.
            <br>
            
         <h2>why choose us?</h2>
         
         <li>Facility to choose from a wide range of books on different subjects.</li>
         <li>Facility to choose from books written by both; novice as well as established authors.</li>
         <li>100% original books.</li>
         <li>Books available at affordable prices.</li>
         <li>Attractive discounts on different books.</li>
         <li>Facility of comparing the book price with the market value.</li>
         <li>Cash on Delivery facility available.</li>
         <li>Free Home Delivery facility available.</li>
         <li>100% Secure and Safe Shopping.</li>
        <a href="bookstore.php"><button>Discover More</button></a>
        </h3>
    </div>
</section>
<!-- </main>
<div class="homea">
<img class="bg" src="images/woman reading-pana.png" alt="" style=" height: 80mvh; width: 100%">
   <h3>about us</h3>

         <h2>why choose us?</h2>
         
         <li>Facility to choose from a wide range of books on different subjects.</li>
         <li>Facility to choose from books written by both; novice as well as established authors.</li>
         <li>100% original books.</li>
         <li>Books available at affordable prices.</li>
         <li>Attractive discounts on different books.</li>
         <li>Facility of comparing the book price with the market value.</li>
         <li>Cash on Delivery facility available.</li>
         <li>Free Home Delivery facility available.</li>
         <li>100% Secure and Safe Shopping.</li>
         
         <a href="contact.php" class="btn">contact us</a> 

      </div>
      </main> -->
<!-- <section class="reviews">

   <h1 class="title">client's Reviews</h1>

   <div class="box-container">

      <div class="box">
         <img src="images/karan_malhot.jpg" alt="">
         <p>Amazing Collection of books. Must Try. Frpm Classics to friction, everything is there.</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3>Karan Malhotra</h3>
      </div>

      <div class="box">
         <img src="images/Mekhola.jpg" alt="">
         <p>The Owner is a friend of mine so I can vouch for this website. Quickest Delivery</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3>Mekhola Bose</h3>
      </div>

      <div class="box">
         <img src="images/Allen Peter.jpg" alt="">
         <p>Awesome Service. Great Collection of books. Amazing Service. I always buy my books from here.</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3>Allen Peter</h3>
      </div>

      <div class="box">
         <img src="images/chattopadhyay_abhijnan.jpg" alt="">
         <p>On Time Delivery. Hassle Free and quickService. classic reads. </p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3>Abhijnan Chatterjee</h3>
      </div>

      <div class="box">
         <img src="images/sarthak.jpg" alt="">
         <p>Amazing Reads and that too at affordable prices. Great Experience.</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3>Sarthak Tuteja</h3>
      </div>

      <div class="box">
         <img src="images/RahulB.jpg" alt="">
         <p>Great Service!! Good Collection of Books. Going to order more books soon.</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3>Rahul Birjaniya</h3>
      </div>

   </div>

</section> -->

<!-- </div> -->

<!-- <section class="authors">

   <h1 class="title">Popular Authors</h1>

   <div class="box-container">

      <div class="box">
         <img src="images/JKR.jpg" alt="">
         <div class="share">
            <a href="#" class="fab fa-facebook-f"></a>
            <a href="#" class="fab fa-twitter"></a>
            <a href="#" class="fab fa-instagram"></a>
            <a href="#" class="fab fa-linkedin"></a>
         </div>
         <h3>J K Rowling</h3>
      </div>

      <div class="box">
         <img src="images/LeoTol.jpg" alt="">
         <div class="share">
            <a href="#" class="fab fa-facebook-f"></a>
            <a href="#" class="fab fa-twitter"></a>
            <a href="#" class="fab fa-instagram"></a>
            <a href="#" class="fab fa-linkedin"></a>
         </div>
         <h3>Leo Tolstoy</h3>
      </div>

      <div class="box">
         <img src="images/JaneAusten.jpg" alt="">
         <div class="share">
            <a href="#" class="fab fa-facebook-f"></a>
            <a href="#" class="fab fa-twitter"></a>
            <a href="#" class="fab fa-instagram"></a>
            <a href="#" class="fab fa-linkedin"></a>
         </div>
         <h3>Jane Austen</h3>
      </div>

      <div class="box">
         <img src="images/MarkTwain.jpg" alt="">
         <div class="share">
            <a href="#" class="fab fa-facebook-f"></a>
            <a href="#" class="fab fa-twitter"></a>
            <a href="#" class="fab fa-instagram"></a>
            <a href="#" class="fab fa-linkedin"></a>
         </div>
         <h3>Mark Twain</h3>
      </div>

      <div class="box">
         <img src="images/Shakespere.jpg" alt="">
         <div class="share">
            <a href="#" class="fab fa-facebook-f"></a>
            <a href="#" class="fab fa-twitter"></a>
            <a href="#" class="fab fa-instagram"></a>
            <a href="#" class="fab fa-linkedin"></a>
         </div>
         <h3>William Shakespeare</h3>
      </div>

      <div class="box">
         <img src="images/RKN.jpg" alt="">
         <div class="share">
            <a href="#" class="fab fa-facebook-f"></a>
            <a href="#" class="fab fa-twitter"></a>
            <a href="#" class="fab fa-instagram"></a>
            <a href="#" class="fab fa-linkedin"></a>
         </div>
         <h3>R K Narayan</h3>
      </div>

   </div>

</section> -->






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

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>