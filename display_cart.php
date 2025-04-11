<?php
// cart.php
session_start();
require_once "includes/connect_db.inc.php";

$user_session_id = session_id();

// Get cart items for the current user
$sql = 'SELECT *, b.title, b.price FROM cart c JOIN books b ON c.book_id = b.book_id WHERE c.user_id = :user_session_id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['user_session_id' => $user_session_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total price
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Cart</title>
</head>
<body>
    <h1>Your Cart</h1>

    <?php if (empty($cart_items)): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Title</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
            <?php foreach ($cart_items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['title']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>$<?= number_format($item['price'], 2) ?></td>
                    <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <p>Total Price: $<?= number_format($total, 2) ?></p>
    <?php endif; ?>

</body>
</html>
