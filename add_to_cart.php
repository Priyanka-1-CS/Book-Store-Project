<?php
session_start();
require_once "includes/connect_db.inc.php";

// Checking if the book_id is provided
if (isset($_GET['book_id'])) {
    $book_id = (int) $_GET['book_id'];
    $user_session_id = session_id();

    // Check if the book is already in the cart
    $sql = 'SELECT * FROM cart WHERE book_id = :book_id AND user_session_id = :user_session_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['book_id' => $book_id, 'user_session_id' => $user_session_id]);

    if ($stmt->rowCount() > 0) {
        // If the book is already in the cart, increase the quantity
        $sql = 'UPDATE cart SET quantity = quantity + 1 WHERE book_id = :book_id AND user_session_id = :user_session_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['book_id' => $book_id, 'user_session_id' => $user_session_id]);
    } else {
        // If the book is not in the cart, add it
        $sql = 'INSERT INTO cart (book_id, quantity, user_session_id) VALUES (:book_id, 1, :user_session_id)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['book_id' => $book_id, 'user_session_id' => $user_session_id]);
    }

    header('Location: book.php');
}
?>
