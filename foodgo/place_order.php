<?php

session_start();

require "db.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit;
}


$cart =
    $_SESSION["cart"] ?? [];


if (empty($cart)) {

    die("Cart is empty.");

}


$address =
    trim($_POST["address"] ?? "");

$phone =
    trim($_POST["phone"] ?? "");

$payment =
    $_POST["payment_method"] ?? "";


if (
    $address === "" ||
    !preg_match(
        "/^[0-9]{10}$/",
        $phone
    ) ||
    $payment === ""
) {

    die("Invalid checkout information.");

}


$conn->begin_transaction();


try {

    $total = 0;


    foreach (
        $cart as $itemId => $quantity
    ) {

        $stmt = $conn->prepare(
            "SELECT price
             FROM menu_items
             WHERE id = ?"
        );

        $stmt->bind_param(
            "i",
            $itemId
        );

        $stmt->execute();

        $result =
            $stmt->get_result();

        $item =
            $result->fetch_assoc();


        if (!$item) {

            throw new Exception(
                "Invalid menu item."
            );

        }


        $total +=
            $item["price"] *
            $quantity;
    }


    $stmt = $conn->prepare(
        "INSERT INTO orders
        (user_id, total, address, phone, payment_method)
        VALUES (?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "idsss",
        $_SESSION["user_id"],
        $total,
        $address,
        $phone,
        $payment
    );

    $stmt->execute();


    $orderId =
        $conn->insert_id;


    foreach (
        $cart as $itemId => $quantity
    ) {

        $stmt = $conn->prepare(
            "SELECT price
             FROM menu_items
             WHERE id = ?"
        );

        $stmt->bind_param(
            "i",
            $itemId
        );

        $stmt->execute();

        $item =
            $stmt
            ->get_result()
            ->fetch_assoc();


        $price =
            $item["price"];


        $stmt = $conn->prepare(
            "INSERT INTO order_items
            (order_id, menu_item_id, quantity, price)
            VALUES (?, ?, ?, ?)"
        );

        $stmt->bind_param(
            "iiid",
            $orderId,
            $itemId,
            $quantity,
            $price
        );

        $stmt->execute();

    }


    $conn->commit();


    $_SESSION["cart"] = [];


    header(
        "Location: orders.php?success=1"
    );

    exit;


} catch (Exception $e) {

    $conn->rollback();

    die(
        "Order failed: " .
        htmlspecialchars(
            $e->getMessage()
        )
    );
}

?>