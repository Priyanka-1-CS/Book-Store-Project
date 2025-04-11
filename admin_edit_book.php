<?php
session_start();
require_once "includes/connect_db.inc.php";

// Check if user is logged in and if they are an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: index.php");
    exit();
}

// Get the book ID from the URL
if (isset($_GET["book_id"])) {
    $book_id = $_GET["book_id"];
    // echo "Book ID from URL: " . $book_id; 
} else {
    header("Location: admin_manage_books.php");
    exit();
}

// Fetch the book details from the database
try {
    $stmt = $pdo->prepare("SELECT * FROM bookstore.books WHERE book_id = :book_id");
    $stmt->bindParam(":book_id", $book_id, PDO::PARAM_INT);
    $stmt->execute();
    $book = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Check if the book exists
    if (!$book) {
        echo "No book found with book_id: " . $book_id; // Debugging line
        // header("Location: admin_manage_books.php");
        var_dump($book);
        exit();
    }
} catch (PDOException $e) {
    echo "Error fetching book details: " . $e->getMessage();
    exit();
}


// Handle the book update
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image_url = $_POST['image_url'];

    try {
        $stmt = $pdo->prepare("UPDATE bookstore.books SET title = :title, author = :author, price = :price, description = :description, image_url = :image_url WHERE book_id = :book_id;");
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":author", $author);
        $stmt->bindParam(":price", $price);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":image_url", $image_url);
        $stmt->bindParam(":book_id", $book_id, PDO::PARAM_INT);
        $stmt->execute();
        
        // Redirect to manage_books.php after updating
        header("Location: admin_manage_books.php");
        exit();
    } catch (PDOException $e) {
        echo "Error updating book: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/admindashboard.css">
    <title>Edit Book</title>
</head>
<body>
    <header>
        <div class="header">
            <a href="admin_home.php" class="logo">𝕿𝖆𝖙𝖙𝖑𝖊𝕿𝖆𝖑𝖊</a>
            <nav>
                <a href="admindashboard.php">Dashboard</a>
                <a href="admin_bookstore.php">Bookstore</a>
                <a href="admin_manage_orders.php">Manage Orders</a>
                <a href="admin_manage_books.php">Manage Books</a>
                <a href="admin_manage_users.php">Manage Users</a>                
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
        <h2 style="text-transform: uppercase; text-align:center; color:black;">Edit Book</h2>
        <form action="admin_edit_book.php?book_id=<?php echo $book['book_id']; ?>" method="POST">
            <table>
                <tr>
                    <td><label for="title">Title:</label></td>
                    <td><input type="text" name="title" id="title" value="<?php echo htmlspecialchars($book['title']); ?>" required></td>
                </tr>
                <tr>
                    <td><label for="author">Author:</label></td>
                    <td><input type="text" name="author" id="author" value="<?php echo htmlspecialchars($book['author']); ?>" required></td>
                </tr>
                <tr>
                    <td><label for="price">Price:</label></td>
                    <td><input type="text" name="price" id="price" value="<?php echo htmlspecialchars($book['price']); ?>" required></td>
                </tr>
                <tr>
                    <td><label for="description">Description:</label></td>
                    <td><textarea name="description" id="description" required><?php echo htmlspecialchars($book['description']); ?></textarea></td>
                </tr>
                <tr>
                    <td><label for="image_url">Image URL:</label></td>
                    <td><input type="text" name="image_url" id="image_url" value="<?php echo htmlspecialchars($book['image_url']); ?>" required></td>
                </tr>
                <tr>
                    <td><label>Current Image:</label></td>
                    <td><img src="<?php echo htmlspecialchars($book['image_url']); ?>" alt="Book Image" width="100"></td>
                </tr>
            </table>
            
            <button type="submit" name="update_book">Update Book</button>
        </form>
    </main>

    <footer>
        <section class="credit">
            <p>Copyright @ <?php echo date('Y'); ?> <span>Priyanka Mukherjee. All rights reserved</span></p>
        </section>
    </footer>
</body>
</html>
