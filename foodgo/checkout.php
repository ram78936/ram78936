<?php

session_start();

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit;
}

if (empty($_SESSION["cart"])) {

    header("Location: cart.php");

    exit;
}

?>

<!DOCTYPE html>

<html>

<head>

    <title>Checkout - FoodGo</title>

    <link rel="stylesheet"
          href="style.css">

</head>

<body>

<div class="auth-container">

    <h2>
        Checkout
    </h2>

    <form
        method="POST"
        action="place_order.php"
    >

        <input
            type="text"
            name="address"
            placeholder="Delivery Address"
            required
        >

        <input
            type="tel"
            name="phone"
            placeholder="10-digit Phone Number"
            pattern="[0-9]{10}"
            required
        >

        <select
            name="payment_method"
            required
        >

            <option value="">
                Select Payment
            </option>

            <option value="Cash">
                Cash on Delivery
            </option>

            <option value="UPI">
                UPI
            </option>

            <option value="Card">
                Card
            </option>

        </select>

        <button type="submit">
            Place Order
        </button>

    </form>

</div>

</body>

</html>