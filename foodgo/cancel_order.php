<?php

session_start();

require "db.php";

// User must be logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION["user_id"];

$orderId = filter_input(
    INPUT_POST,
    "order_id",
    FILTER_VALIDATE_INT
);

if (!$orderId) {
    header("Location: orders.php");
    exit;
}


// Check that this order belongs to the logged-in user
$stmt = $conn->prepare(
    "SELECT id, status
     FROM orders
     WHERE id = ?
     AND user_id = ?"
);

$stmt->bind_param(
    "ii",
    $orderId,
    $userId
);

$stmt->execute();

$result = $stmt->get_result();


// Order does not belong to this user
if ($result->num_rows !== 1) {
    header("Location: orders.php");
    exit;
}

$order = $result->fetch_assoc();


// Do not allow cancellation of completed/cancelled orders
if (
    $order["status"] === "Delivered" ||
    $order["status"] === "Cancelled"
) {
    header("Location: orders.php?error=not_cancelable");
    exit;
}


// Cancel the order
$stmt = $conn->prepare(
    "UPDATE orders
     SET status = 'Cancelled'
     WHERE id = ?
     AND user_id = ?"
);

$stmt->bind_param(
    "ii",
    $orderId,
    $userId
);

$stmt->execute();


// Return to orders page
header("Location: orders.php?cancelled=1");
exit;

?>