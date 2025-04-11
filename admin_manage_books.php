<?php
session_start();
require_once "includes/connect_db.inc.php";

// Check if user is logged in and if they are an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["user_type"] !== "admin") {
    header("Location: index.php");
    exit();
}

// Handle deletion of books
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_book'])) {
    $book_id = $_POST['book_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM bookstore.books WHERE book_id = :book_id;");
        $stmt->bindParam(":book_id", $book_id, PDO::PARAM_INT);
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error deleting book: " . $e->getMessage();
    }
}

// Fetch all books for management
try {
    $stmt = $pdo->query("SELECT * FROM bookstore.books;");
} catch (PDOException $e) {
    echo "Error fetching books: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Adding a version query string to force refresh the CSS file -->
    <link rel="stylesheet" href="css/admindashboard.css?v=1.0">
    <title>Manage Books</title>
</head>
<body>
    <header>
        <div class="header">
        <a href="admin_home.php" class="logo">𝕿𝖆𝖙𝖙𝖑𝖊𝕿𝖆𝖑𝖊</a>
            <nav>
                <a href="admindashboard.php">Dashboard</a>
                <a href="admin_bookstore.php">Bookstore</a>
                <a href="admin_manage_orders.php">Manage Orders</a>
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
        <h2 style="text-transform: uppercase; text-align:center; color:black;">Manage Books</h2>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($book = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($book['title']); ?></td>
                        <td><?php echo htmlspecialchars($book['author']); ?></td>
                        <td>₹<?php echo htmlspecialchars($book['price']); ?></td>
                        <td><?php echo htmlspecialchars($book['description']); ?></td>
                        <td><img src="<?php echo htmlspecialchars($book['image_url']); ?>" alt="Book Image" width="100"></td>
                        <td>
                            <!-- Edit Button (Redirects to edit_book.php with book_id) -->
                                <a href="admin_edit_book.php?book_id=<?php echo $book['book_id']; ?>" class="edit-btn"><button>Edit</button></a>

                            <!-- Delete Book-->
                            <form action="admin_manage_books.php" method="POST" style="display:inline;">
                                <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                                <button type="submit" name="delete_book" onclick="return confirm('Are you sure you want to delete this book?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <a href="admin_add_book.php" class="add-book-btn">Add New Book</a>
    </main>

    <footer>
        <section class="credit">
            <p>Copyright @ <?php echo date('Y'); ?> <span>Priyanka Mukherjee. All rights reserved</span></p>
        </section>
    </footer>
</body>
</html>
