<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$itemId = filter_input(
    INPUT_POST,
    "item_id",
    FILTER_VALIDATE_INT
);

$action = $_POST["action"] ?? "";

if (!$itemId || !isset($_SESSION["cart"][$itemId])) {
    header("Location: cart.php");
    exit;
}

if ($action === "increase") {

    // Add one quantity
    $_SESSION["cart"][$itemId]++;

} elseif ($action === "decrease") {

    // Remove one quantity
    $_SESSION["cart"][$itemId]--;

    // If quantity becomes zero, remove item completely
    if ($_SESSION["cart"][$itemId] <= 0) {
        unset($_SESSION["cart"][$itemId]);
    }

} elseif ($action === "remove") {

    // Remove the complete item
    unset($_SESSION["cart"][$itemId]);
}

header("Location: cart.php");
exit;

?>