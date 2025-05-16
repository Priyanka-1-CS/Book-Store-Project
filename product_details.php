<?php
// Include database connection and start session
require_once "includes/connect_db.inc.php";

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redirect to login page if not logged in
    exit();
}

// Check if the book_id is provided in the URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $book_id = $_GET['id'];

    // Fetch product details from the database using PDO
    try {
        $stmt = $pdo->prepare("SELECT * FROM bookstore.books WHERE book_id = :book_id");
        $stmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            echo "Product not found.";
            exit();
        }
    } catch (PDOException $e) {
        echo "Error fetching product details: " . $e->getMessage();
        exit();
    }
} else {
    echo "Invalid product ID.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/stylehome.css">
    <title>BookStore - Product Details</title>
</head>

<body>
    <!-- Header Section -->
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
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="delete-btn">Logout</a>
                <?php else: ?>
                    <a href="index.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main Content Section -->
    <main>
        <section class="product-details">
            <div class="product-card">
                <div class="product-image">
                    <?php
                    $image_url = htmlspecialchars($product['image_url']);
                    echo "<!-- Image Path: /Book_Store/$image_url -->"; // Check the generated image URL
                    ?>
                    <img src="/Book_Store/<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">

                </div>
                <div class="product-info">
                    <h2><?php echo htmlspecialchars($product['title']); ?></h2>
                    <p><strong>Author:</strong> <?php echo htmlspecialchars($product['author']); ?></p>
                    <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    <p><strong>Price:</strong> ₹<?php echo number_format($product['price'], 2); ?></p>
                    
                    <!-- Add to Cart Form -->
                    <form action="cart.php" method="POST">
                        <input type="hidden" name="book_id" value="<?php echo $product['book_id']; ?>">
                        <input type="hidden" name="book_title" value="<?php echo htmlspecialchars($product['title']); ?>">
                        <input type="hidden" name="book_price" value="<?php echo $product['price']; ?>">
                        <button type="submit" class="btn">Add to Cart</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer Section -->
    <footer>
        <section class="box-container">
            <div class="box">
                <h3>Quick Links</h3>
                <a href="about.php">About</a>
                <a href="shop.php">E-Store</a>
                <a href="contact.php">Reach Out to Us</a>
            </div>

            <div class="box">
                <h3>Extra Links</h3>
                <a href="register.php">Register</a>
                <a href="cart.php">Cart</a>
                <a href="orders.php">Orders</a>
            </div>

            <div class="box">
                <h3>Contact Info</h3>
                <p>+91-33-36869091</p>
                <p>priyanka@mail.com</p>
                <p>Calcutta, India, 700019</p>
            </div>

            <div class="box">
                <h3>Follow Us</h3>
                <a href="#">Twitter</a>
                <a href="#">Instagram</a>
                <a href="#">LinkedIn</a>
            </div>
        </section>
    </footer>
    <section class="credit">
        <p>Copyright @ <?php echo date('Y'); ?> <span>Priyanka Mukherjee. All rights reserved</span></p>
    </section>
</body>

</html>
