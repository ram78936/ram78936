<?php

session_start();

require "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$cart = $_SESSION["cart"] ?? [];

$total = 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Cart - FoodGo</title>

    <link rel="stylesheet" href="style.css">

    <style>

        .cart-container {
            width: 90%;
            max-width: 1000px;
            margin: 50px auto;
        }

        .cart-item {
            background: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.12);
        }

        .cart-item-info {
            flex: 1;
        }

        .cart-item-info h3 {
            margin: 0 0 8px;
        }

        .cart-item-info p {
            margin: 5px 0;
        }

        /* Quantity controls */

        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 12px;
        }

        .quantity-controls button {
            width: 38px;
            height: 38px;
            border: none;
            border-radius: 6px;
            font-size: 22px;
            font-weight: bold;
            cursor: pointer;
        }

        .minus-btn {
            background: #eeeeee;
            color: #333;
        }

        .minus-btn:hover {
            background: #dddddd;
        }

        .plus-btn {
            background: #ff512f;
            color: white;
        }

        .plus-btn:hover {
            background: #e63e1f;
        }

        .quantity {
            min-width: 35px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
        }

        /* Remove complete item */

        .remove-btn {
            background: #e53935;
            color: white;
            padding: 10px 15px;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
        }

        .remove-btn:hover {
            background: #c62828;
        }

        /* Summary */

        .cart-summary {
            background: white;
            padding: 25px;
            margin-top: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.12);
            text-align: right;
        }

        .checkout-btn {
            background: #ff512f;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }

        .empty-cart {
            background: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px;
        }

        /* Mobile */

        @media (max-width: 700px) {

            .cart-item {
                flex-direction: column;
                align-items: flex-start;
            }

        }

    </style>

</head>


<body>

<header>

    <div class="logo">
        🍔 FoodGo
    </div>

    <nav>

        <a href="index.php">Home</a>

        <a href="restaurants.php">Restaurants</a>

        <a href="cart.php">🛒 Cart</a>

        <a href="orders.php">Orders</a>

        <a href="logout.php">Logout</a>

    </nav>

</header>


<div class="cart-container">

    <h2>Your Cart 🛒</h2>


    <?php if (empty($cart)): ?>

        <div class="empty-cart">

            <h3>Your cart is empty 🛒</h3>

            <p>Add some delicious food to your cart.</p>

            <a
                href="restaurants.php"
                class="checkout-btn"
            >
                Browse Restaurants
            </a>

        </div>

    <?php else: ?>


        <?php

        $ids = array_keys($cart);

        $placeholders = implode(
            ",",
            array_fill(0, count($ids), "?")
        );

        $types = str_repeat("i", count($ids));

        $stmt = $conn->prepare(
            "SELECT *
             FROM menu_items
             WHERE id IN ($placeholders)"
        );

        $stmt->bind_param(
            $types,
            ...$ids
        );

        $stmt->execute();

        $items = $stmt->get_result();

        ?>


        <?php while ($item = $items->fetch_assoc()): ?>

            <?php

            $itemId = (int)$item["id"];

            $quantity = (int)$cart[$itemId];

            $subtotal = $item["price"] * $quantity;

            $total += $subtotal;

            ?>


            <div class="cart-item">

                <div class="cart-item-info">

                    <h3>
                        <?php
                        echo htmlspecialchars($item["name"]);
                        ?>
                    </h3>

                    <p>
                        Price:
                        ₹<?php echo number_format($item["price"], 2); ?>
                    </p>


                    <!-- Quantity controls -->

                    <div class="quantity-controls">

                        <!-- MINUS -->

                        <form
                            action="update_cart.php"
                            method="POST"
                        >

                            <input
                                type="hidden"
                                name="item_id"
                                value="<?php echo $itemId; ?>"
                            >

                            <input
                                type="hidden"
                                name="action"
                                value="decrease"
                            >

                            <button
                                type="submit"
                                class="minus-btn"
                                title="Remove one"
                            >
                                −
                            </button>

                        </form>


                        <!-- CURRENT QUANTITY -->

                        <span class="quantity">
                            <?php echo $quantity; ?>
                        </span>


                        <!-- PLUS -->

                        <form
                            action="update_cart.php"
                            method="POST"
                        >

                            <input
                                type="hidden"
                                name="item_id"
                                value="<?php echo $itemId; ?>"
                            >

                            <input
                                type="hidden"
                                name="action"
                                value="increase"
                            >

                            <button
                                type="submit"
                                class="plus-btn"
                                title="Add one"
                            >
                                +
                            </button>

                        </form>

                    </div>


                    <p>
                        Subtotal:
                        <strong>
                            ₹<?php echo number_format($subtotal, 2); ?>
                        </strong>
                    </p>

                </div>


                <!-- REMOVE ENTIRE ITEM -->

                <form
                    action="update_cart.php"
                    method="POST"
                    onsubmit="return confirm('Remove this item completely from your cart?');"
                >

                    <input
                        type="hidden"
                        name="item_id"
                        value="<?php echo $itemId; ?>"
                    >

                    <input
                        type="hidden"
                        name="action"
                        value="remove"
                    >

                    <button
                        type="submit"
                        class="remove-btn"
                    >
                        🗑 Remove
                    </button>

                </form>

            </div>


        <?php endwhile; ?>


        <!-- TOTAL -->

        <div class="cart-summary">

            <h2>
                Total:
                ₹<?php echo number_format($total, 2); ?>
            </h2>

            <a
                href="checkout.php"
                class="checkout-btn"
            >
                Proceed to Checkout
            </a>

        </div>


    <?php endif; ?>

</div>

</body>

</html>