<?php
session_start();

// Checking if the user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php"); // Redirect to login page if not logged in
    exit();
}

try {
    require_once "includes/connect_db.inc.php";

    // Fetching products from the database
    $stmt = $pdo->prepare("SELECT book_id, title, author, price, description, image_url FROM books;");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "<p>Error fetching products: " . $e->getMessage() . "</p>";
    exit();
}



if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Checking if the required POST data is set
    if (isset($_POST['book_id'], $_POST['book_title'], $_POST['book_price'], $_POST['book_image'])) {
        $user_id = $_SESSION["user_id"];
        $book_id = $_POST["book_id"];
        $book_title = $_POST["book_title"];
        $book_price = $_POST["book_price"];
        $book_image = $_POST["book_image"];

       
        try {
            // Checking if the product is already in the cart for this user
            $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = :user_id AND book_id = :book_id;");
            $stmt->execute(['user_id' => $user_id, 'book_id' => $book_id]);
            $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($cart_item) {
                // Update quantity if book is already in the cart
                $new_quantity = $cart_item['quantity'] + 1;
                $stmt = $pdo->prepare("UPDATE cart SET quantity = :quantity WHERE cart_id = :cart_id;");
                $stmt->execute(['quantity' => $new_quantity, 'cart_id' => $cart_item['cart_id']]);
                echo "<p>Item quantity updated in the cart.</p>";
            } else {
                // Add new book to cart
                $stmt = $pdo->prepare("INSERT INTO cart (book_id, title, quantity, user_id, price, image) 
                    VALUES (:book_id, :book_title, 1, :user_id, :book_price, :book_image);");

               
                $stmt->execute([
                    'book_id' => $book_id,
                    'book_title' => $book_title,
                    'user_id' => $user_id,
                    'book_price' => $book_price,
                    'book_image' => $book_image
                ]);

                echo "<p>Item added to cart successfully.</p>";
            }

            // Redirect to the cart page after adding to cart
            header("Location: cart.php");
            exit();
        } catch (PDOException $e) {
            echo "<p>Error processing the cart: " . $e->getMessage() . "</p>";
            exit();
        }
    } else {
        echo "<p>Error: Required data missing from the form submission.</p>";
    }
}
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="css/stylehome.css"> -->
    <style>
        
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
    transition: all 0.3s ease;
}

body {
    color: #333;
    line-height: 1.6;
    font-size: 16px;
    background-color: #f8f8f8; 
    display: flex;
    flex-direction: column;
    min-height: 100vh; 
}

h1, h2, h3, h4, h5, h6 {
    font-weight: 600;
    letter-spacing: 0.5px;
}

a {
    text-decoration: none;
    color: inherit;
}


   header {
    background-color: #334a14; 
    color: white;
    padding: 20px 0;
    z-index: 1000; 
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    width: 100%;
    
}

header .header {
    width: 90%;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

header .logo {
    font-size: 28px;
    color: white;
    font-weight: bold;
    letter-spacing: 3px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    text-transform: uppercase;
    font-size: xx-large;
}

nav {
    display: flex;
    gap: 30px;
}

nav a {
    color: white;
    text-transform: uppercase;
    font-size: 16px;
    font-weight: 500;
    letter-spacing: 0.5px;
    padding: 5px 10px;
    transition: color 0.3s ease, background-color 0.3s ease;
    border-radius: 5px;
}


.header .icons a {
    color: #fff;
    padding: 10px 15px;
    background-color: #229822;
    text-decoration: none;
    border-radius: 5px;
}

nav a:hover {
    background-color: #333;
    color: #4CAF50;
}

header .icons a:hover {
    background-color: #c0392b;
}



main {
    padding: 40px 20px;
    margin-top: 30px; 
}


.product-list h1 {
    text-align: center;
    font-size: 36px;
    margin-bottom: 20px;
    margin-top: 40px;
    color: black;
}


.product-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
}


.product-card {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    width: 250px;
    padding: 20px;
    text-align: center;
    transition: transform 0.3s ease; 
}

.product-card:hover {
    transform: scale(1.05); 
}


.product-card img {
    width: 100%;
    border-radius: 8px;
    margin-bottom: 15px;
}


.product-card h3 {
    font-size: 22px;
    margin-bottom: 10px;
}


.product-card p {
    font-size: 16px;
    margin-bottom: 10px;
}

.product-card .btn {
    background-color: #27ae60;
    color: #fff;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
    transition: 0.3s ease;
}

.product-card .btn:hover {
    background-color: #2ecc71;
}


footer {
    background-color: #334a14;
    color: #fff;
    padding: 40px 20px;
}


footer {
    background-color: #334a14; 
    color: #fff;
    padding: 20px 10px;
    text-align: center;
}

footer .box-container {
    display: flex;
    justify-content: space-between;
    max-width: 1100px;
    margin: 0px auto;
    flex-wrap: wrap; 
}

footer .box {
    flex: 1;
    margin: 5px;
    background-color:#fafbfa00;
    min-width: 200px; 
}

footer h3 {
    font-size: 18px;
    color: #ffffff; 
    margin-bottom: 20px;
    font-weight: bold;
    text-transform: uppercase;
}

footer a {
    color: #ffffff;
    text-decoration: none;
    display: block;
    margin: 5px 0;
}

footer a:hover {
    color: #ef98fb; 
}

footer p {
    color: #ffffff;
}

section.credit {
    background-color: #000000;
    color: #fff;
    padding: 10px 0;
    text-align: center;
    font-size: 14px;
}

section.credit span {
    font-weight: bold;
}


 
@media (max-width: 768px) {


    header .header {
        flex-direction: column;
        align-items: center;
        padding: 10px 0;
    }

    .product-card {
        width: 100%;
        max-width: 320px;
        margin-bottom: 20px;
    }


    footer .box-container {
        flex-direction: column;
        align-items: center;
    }

   
    footer .box {
        width: 100%;
        max-width: 300px;
        text-align: center;
    }

    
    main {
        padding: 20px;
    }

   
    .product-list h1 {
        font-size: 28px;
    }
}


@media (max-width: 480px) {

  
    h1, h2, h3 {
        font-size: 18px;
    }

    .product-card h3 {
        font-size: 20px;
    }

    footer .box h3 {
        font-size: 18px;
    }

    .product-card .btn {
        font-size: 14px;
    }
}

    </style>
    <title>Bookstore</title>
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
        <section class="product-list">
            <h1 style="text-transform: uppercase; background-color:green; color:white; border-radius: 5px;">Our Products</h1>
            <?php if (count($products) > 0): ?>
                <div class="product-container">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <?php
                            $image_url = !empty($product['image_url']) ? $product['image_url'] : "default-image.jpg";
                            ?>
                            <img src="/Book_Store/<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
                            <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                            <p><strong>Author:</strong> <?php echo htmlspecialchars($product['author']); ?></p>
                            <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                            <p><strong>Price:</strong> ₹<?php echo number_format($product['price'], 2); ?></p>

                            <!-- Add to Cart Form -->
                            <form action="bookstore.php" method="POST">
                                <input type="hidden" name="book_id" value="<?php echo $product['book_id']; ?>">
                                <input type="hidden" name="book_title" value="<?php echo htmlspecialchars($product['title']); ?>">
                                <input type="hidden" name="book_price" value="<?php echo $product['price']; ?>">
                                <input type="hidden" name="book_image" value="<?php echo $image_url; ?>">
                                <button type="submit" class="btn">Add to Cart</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>No products available at the moment. Please check back later.</p>
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
