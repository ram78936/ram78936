<?php

session_start();

require "db.php";

// User must be logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

// Get food item ID
$itemId = intval($_POST["item_id"] ?? 0);

// Check whether the food item exists
$stmt = $conn->prepare(
    "SELECT id
     FROM menu_items
     WHERE id = ?"
);

$stmt->bind_param("i", $itemId);
$stmt->execute();

$result = $stmt->get_result();

// Invalid item
if ($result->num_rows !== 1) {
    die("Invalid food item.");
}

// Create cart if it doesn't exist
if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

// Add item or increase quantity
if (isset($_SESSION["cart"][$itemId])) {
    $_SESSION["cart"][$itemId]++;
} else {
    $_SESSION["cart"][$itemId] = 1;
}

// Go to cart
header("Location: cart.php?added=1");
exit;

?>