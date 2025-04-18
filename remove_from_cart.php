<?php

session_start();
include 'db.php';

if (isset($_GET['cart_id'])) {
    $cart_id = (int) $_GET['cart_id'];
    $user_session_id = session_id();

    // Remove the item from the cart
    $sql = 'DELETE FROM cart WHERE id = :cart_id AND user_session_id = :user_session_id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['cart_id' => $cart_id, 'user_session_id' => $user_session_id]);

    header('Location: cart.php');
}
?>
