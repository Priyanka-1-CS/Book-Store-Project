<?php
session_start();
require_once "includes/connect_db.inc.php";

// Check if user is logged in and if they are an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: index.php");
    exit();
}

// Handle book addition
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    
    // Handle the image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $imageType = $_FILES['image']['type'];
        $imageSize = $_FILES['image']['size'];
        
        // Define the allowed file types (image types)
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        
        if (in_array($imageType, $allowedTypes)) {
            // Define the directory where the image will be stored
            $uploadDir = 'uploads/images/';
            
            // Ensure the upload directory exists
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Define the target path for the image file
            $imagePath = $uploadDir . basename($imageName);
            
            // Move the uploaded image to the target directory
            if (move_uploaded_file($imageTmpName, $imagePath)) {
                // Insert book data into the database
                try {
                    $stmt = $pdo->prepare("INSERT INTO bookstore.books (title, author, price, description, image_url) 
                                           VALUES (:title, :author, :price, :description, :image_url)");
                    $stmt->bindParam(":title", $title);
                    $stmt->bindParam(":author", $author);
                    $stmt->bindParam(":price", $price);
                    $stmt->bindParam(":description", $description);
                    $stmt->bindParam(":image_url", $imagePath);
                    $stmt->execute();
                    
                    // Redirect to manage books page after successful addition
                    header("Location: admin_manage_books.php");
                    exit();
                } catch (PDOException $e) {
                    echo "Error inserting book: " . $e->getMessage();
                }
            } else {
                echo "Error uploading image.";
            }
        } else {
            echo "Invalid image type. Only JPEG, PNG, and GIF are allowed.";
        }
    } else {
        echo "Please upload an image.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/admindashboard.css">
    <title>Add Book</title>
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

    <!-- <main>
        <h2>Add New Book</h2>
        <form action="admin_add_book.php" method="POST" enctype="multipart/form-data">
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" required>

            <label for="author">Author:</label>
            <input type="text" name="author" id="author" required>

            <label for="price">Price:</label>
            <input type="text" name="price" id="price" required>

            <label for="description">Description:</label>
            <textarea name="description" id="description" required></textarea>

            <label for="image">Book Image:</label>
            <input type="file" name="image" id="image" accept="image/jpeg, image/png, image/gif" required>

            <button type="submit" name="add_book">Add Book</button>
        </form>
    </main> -->

    <main>
    <h2>Add New Book</h2>
    <form action="admin_add_book.php" method="POST" enctype="multipart/form-data">
        <!-- Table Layout for Form -->
        <table class="form-table">
            <tr>
                <td><label for="title">Title:</label></td>
                <td><input type="text" name="title" id="title" required></td>
            </tr>
            <tr>
                <td><label for="author">Author:</label></td>
                <td><input type="text" name="author" id="author" required></td>
            </tr>
            <tr>
                <td><label for="price">Price:</label></td>
                <td><input type="text" name="price" id="price" required></td>
            </tr>
            <tr>
                <td><label for="description">Description:</label></td>
                <td><textarea name="description" id="description" required></textarea></td>
            </tr>
            <tr>
                <td><label for="image">Book Image:</label></td>
                <td><input type="file" name="image" id="image" accept="image/jpeg, image/png, image/gif" required></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <button type="submit" name="add_book">Add Book</button>
                </td>
            </tr>
        </table>
    </form>
</main>



    <footer>
        <section class="credit">
            <p>Copyright @ <?php echo date('Y'); ?> <span>Priyanka Mukherjee. All rights reserved</span></p>
        </section>
    </footer>
</body>
</html>
