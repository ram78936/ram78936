<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Get item ID from URL
$itemId = filter_input(
    INPUT_GET,
    "item_id",
    FILTER_VALIDATE_INT
);

// Remove item from cart
if ($itemId && isset($_SESSION["cart"][$itemId])) {
    unset($_SESSION["cart"][$itemId]);
}

// If cart is empty, remove the cart session
if (empty($_SESSION["cart"])) {
    unset($_SESSION["cart"]);
}

// Go back to cart
header("Location: cart.php?removed=1");
exit;

?>