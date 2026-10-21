<?php

session_start();

require "db.php";

// User must be logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION["user_id"];


// Get all orders belonging to this user
$stmt = $conn->prepare(
    "SELECT
        id,
        total,
        address,
        phone,
        payment_method,
        status,
        created_at
     FROM orders
     WHERE user_id = ?
     ORDER BY created_at DESC"
);

$stmt->bind_param(
    "i",
    $userId
);

$stmt->execute();

$orders = $stmt->get_result();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>My Orders - FoodGo</title>

    <link
        rel="stylesheet"
        href="style.css"
    >

    <style>

        body {
            background: #f5f5f5;
        }

        .orders-container {
            width: 90%;
            max-width: 1000px;
            margin: 50px auto;
        }

        .page-title {
            text-align: center;
            margin-bottom: 30px;
        }

        .back-btn {
            display: inline-block;
            background: #333;
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 6px;
            margin-bottom: 25px;
        }

        .back-btn:hover {
            background: #111;
        }

        .message {
            padding: 15px;
            border-radius: 7px;
            margin-bottom: 20px;
            text-align: center;
        }

        .success {
            background: #d4edda;
            color: #155724;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
        }

        .order-card {
            background: white;
            padding: 25px;
            margin-bottom: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.12);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .order-header h2 {
            margin: 0;
        }

        .status {
            padding: 8px 15px;
            border-radius: 20px;
            color: white;
            font-size: 14px;
            font-weight: bold;
        }

        .status-placed {
            background: #ff5722;
        }

        .status-cancelled {
            background: #d32f2f;
        }

        .status-delivered {
            background: #2e7d32;
        }

        .status-preparing {
            background: #1976d2;
        }

        .status-out {
            background: #7b1fa2;
        }

        .order-info {
            line-height: 1.8;
            margin-bottom: 20px;
        }

        .order-info strong {
            color: #222;
        }

        .items-title {
            margin-top: 20px;
            margin-bottom: 12px;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8f8f8;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
        }

        .item-name {
            font-weight: bold;
        }

        .item-details {
            color: #555;
            margin-top: 5px;
            font-size: 14px;
        }

        .item-price {
            font-weight: bold;
        }

        .order-total {
            text-align: right;
            border-top: 1px solid #ddd;
            margin-top: 20px;
            padding-top: 15px;
            font-size: 20px;
        }

        .order-actions {
            text-align: right;
            margin-top: 20px;
        }

        .cancel-btn {
            background: #e53935;
            color: white;
            border: none;
            padding: 11px 18px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
        }

        .cancel-btn:hover {
            background: #c62828;
        }

        .no-orders {
            background: white;
            text-align: center;
            padding: 40px;
            border-radius: 10px;
        }

        @media (max-width: 700px) {

            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .order-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

        }

    </style>

</head>


<body>


<div class="orders-container">

    <a
        href="dashboard.php"
        class="back-btn"
    >
        ← Back to Dashboard
    </a>


    <h1 class="page-title">
        📦 My Orders
    </h1>


    <?php if (isset($_GET["success"])): ?>

        <div class="message success">
            ✅ Your order has been placed successfully!
        </div>

    <?php endif; ?>


    <?php if (isset($_GET["cancelled"])): ?>

        <div class="message success">
            ✅ Your order has been cancelled successfully.
        </div>

    <?php endif; ?>


    <?php if (isset($_GET["error"]) && $_GET["error"] === "not_cancelable"): ?>

        <div class="message error">
            ❌ This order cannot be cancelled because it has already been
            delivered or cancelled.
        </div>

    <?php endif; ?>


    <?php if ($orders->num_rows === 0): ?>

        <div class="no-orders">

            <h2>No Orders Yet</h2>

            <p>
                You have not placed any orders.
            </p>

            <a
                href="restaurants.php"
                class="back-btn"
            >
                Browse Restaurants
            </a>

        </div>


    <?php else: ?>


        <?php while ($order = $orders->fetch_assoc()): ?>


            <?php

            $orderId = (int)$order["id"];

            $status = $order["status"];

            // CSS class for status
            $statusClass = "status-placed";

            if ($status === "Cancelled") {
                $statusClass = "status-cancelled";
            }
            elseif ($status === "Delivered") {
                $statusClass = "status-delivered";
            }
            elseif ($status === "Preparing") {
                $statusClass = "status-preparing";
            }
            elseif ($status === "Out for Delivery") {
                $statusClass = "status-out";
            }

            ?>


            <div class="order-card">


                <!-- ORDER HEADER -->

                <div class="order-header">

                    <h2>
                        Order #<?php echo $orderId; ?>
                    </h2>

                    <span class="status <?php echo $statusClass; ?>">
                        <?php echo htmlspecialchars($status); ?>
                    </span>

                </div>


                <!-- ORDER INFORMATION -->

                <div class="order-info">

                    <div>
                        <strong>Order Date:</strong>
                        <?php
                        echo date(
                            "d M Y, h:i A",
                            strtotime($order["created_at"])
                        );
                        ?>
                    </div>

                    <div>
                        <strong>Delivery Address:</strong>
                        <?php
                        echo htmlspecialchars($order["address"]);
                        ?>
                    </div>

                    <div>
                        <strong>Phone:</strong>
                        <?php
                        echo htmlspecialchars($order["phone"]);
                        ?>
                    </div>

                    <div>
                        <strong>Payment Method:</strong>
                        <?php
                        echo htmlspecialchars($order["payment_method"]);
                        ?>
                    </div>

                </div>


                <!-- ORDERED ITEMS -->

                <h3 class="items-title">
                    🍽 Ordered Items
                </h3>


                <?php

                $itemStmt = $conn->prepare(
                    "SELECT
                        oi.quantity,
                        oi.price,
                        mi.name
                     FROM order_items oi
                     INNER JOIN menu_items mi
                         ON oi.menu_item_id = mi.id
                     WHERE oi.order_id = ?
                     ORDER BY oi.id ASC"
                );

                $itemStmt->bind_param(
                    "i",
                    $orderId
                );

                $itemStmt->execute();

                $orderItems = $itemStmt->get_result();

                ?>


                <?php while ($item = $orderItems->fetch_assoc()): ?>


                    <?php

                    $quantity = (int)$item["quantity"];

                    $price = (float)$item["price"];

                    $itemSubtotal = $quantity * $price;

                    ?>


                    <div class="order-item">

                        <div>

                            <div class="item-name">

                                <?php
                                echo htmlspecialchars($item["name"]);
                                ?>

                            </div>

                            <div class="item-details">

                                ₹<?php echo number_format($price, 2); ?>

                                ×

                                <?php echo $quantity; ?>

                            </div>

                        </div>


                        <div class="item-price">

                            ₹<?php
                            echo number_format(
                                $itemSubtotal,
                                2
                            );
                            ?>

                        </div>

                    </div>


                <?php endwhile; ?>


                <!-- TOTAL -->

                <div class="order-total">

                    <strong>
                        Total:
                        ₹<?php
                        echo number_format(
                            $order["total"],
                            2
                        );
                        ?>
                    </strong>

                </div>


                <!-- CANCEL BUTTON -->

                <?php

                $canCancel =
                    $status !== "Delivered" &&
                    $status !== "Cancelled";

                ?>


                <?php if ($canCancel): ?>

                    <div class="order-actions">

                        <form
                            action="cancel_order.php"
                            method="POST"
                            onsubmit="return confirm('Are you sure you want to cancel this order?');"
                        >

                            <input
                                type="hidden"
                                name="order_id"
                                value="<?php echo $orderId; ?>"
                            >

                            <button
                                type="submit"
                                class="cancel-btn"
                            >
                                ❌ Cancel Order
                            </button>

                        </form>

                    </div>

                <?php endif; ?>


            </div>


        <?php endwhile; ?>


    <?php endif; ?>


</div>


</body>

</html>