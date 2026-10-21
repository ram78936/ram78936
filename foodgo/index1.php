<?php

session_start();
require_once "db.php";

$restaurants = [];

$result = $conn->query(
    "SELECT id, name, cuisine, rating, image
     FROM restaurants
     ORDER BY rating DESC"
);

if ($result) {

    while ($row = $result->fetch_assoc()) {
        $restaurants[] = $row;
    }

}

$cartCount = 0;

if (isset($_SESSION["cart"]) && is_array($_SESSION["cart"])) {

    foreach ($_SESSION["cart"] as $quantity) {
        $cartCount += (int)$quantity;
    }

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>FoodGo - Food Delivery Platform</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>


<!-- NAVBAR -->

<header>

    <div class="logo">
        🍔 FoodGo
    </div>

    <nav>

        <a href="index.php">
            Home
        </a>

        <a href="restaurants.php">
            Restaurants
        </a>

        <?php if (isset($_SESSION["user_id"])): ?>

            <a href="cart.php">
                🛒 Cart
                <?php if ($cartCount > 0): ?>
                    (<?php echo $cartCount; ?>)
                <?php endif; ?>
            </a>

            <a href="orders.php">
                📦 Orders
            </a>

            <a href="dashboard.php">
                Dashboard
            </a>

            <a href="logout.php">
                Logout
            </a>

        <?php else: ?>

            <a href="login.php">
                Login
            </a>

            <a href="register.php">
                Register
            </a>

        <?php endif; ?>

    </nav>

</header>


<!-- HERO -->

<section class="hero">

    <div class="hero-content">

        <h1>
            Delicious Food, Delivered Fast 🍕
        </h1>

        <p>
            Order your favourite meals
            from your favourite restaurants.
        </p>

        <?php if (isset($_SESSION["user_id"])): ?>

            <a href="restaurants.php">
                🍽 Order Now
            </a>

        <?php else: ?>

            <a href="login.php">
                🔐 Login to Order
            </a>

            <a href="register.php">
                👤 Create Account
            </a>

        <?php endif; ?>

    </div>

</section>


<!-- RESTAURANTS -->

<section class="section">

    <h2>
        Popular Restaurants
    </h2>

    <div class="restaurant-grid">

        <?php if (count($restaurants) > 0): ?>

            <?php foreach ($restaurants as $restaurant): ?>

                <div class="restaurant-card">

                    <img
                        src="<?php
                            echo htmlspecialchars(
                                $restaurant["image"]
                            );
                        ?>"
                        alt="<?php
                            echo htmlspecialchars(
                                $restaurant["name"]
                            );
                        ?>"
                    >

                    <div class="restaurant-content">

                        <h3>
                            <?php
                            echo htmlspecialchars(
                                $restaurant["name"]
                            );
                            ?>
                        </h3>

                        <p>
                            🍴
                            <?php
                            echo htmlspecialchars(
                                $restaurant["cuisine"]
                            );
                            ?>
                        </p>

                        <p>
                            ⭐
                            <?php
                            echo htmlspecialchars(
                                $restaurant["rating"]
                            );
                            ?>
                        </p>

                        <?php if (isset($_SESSION["user_id"])): ?>

                            <a href="menu.php?id=<?php
                                echo (int)$restaurant["id"];
                            ?>">
                                View Menu →
                            </a>

                        <?php else: ?>

                            <a href="login.php">
                                Login to Order
                            </a>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <p>
                No restaurants available.
            </p>

        <?php endif; ?>

    </div>

</section>


<!-- FEATURES -->

<section class="section">

    <h2>
        Why Choose FoodGo?
    </h2>

    <div class="restaurant-grid">

        <div class="restaurant-card">

            <div class="restaurant-content">

                <h3>
                    🍕 Variety of Food
                </h3>

                <p>
                    Choose from different cuisines
                    and restaurants.
                </p>

            </div>

        </div>


        <div class="restaurant-card">

            <div class="restaurant-content">

                <h3>
                    🛒 Easy Ordering
                </h3>

                <p>
                    Add your favourite food
                    to your cart and checkout easily.
                </p>

            </div>

        </div>


        <div class="restaurant-card">

            <div class="restaurant-content">

                <h3>
                    📦 Order Tracking
                </h3>

                <p>
                    View your previous orders
                    and their status.
                </p>

            </div>

        </div>

    </div>

</section>


<!-- FOOTER -->

<footer>

    <p>
        © 2026 FoodGo | SEP Micro Project
    </p>

    <p>
        Integrated Black-Box and White-Box Testing Framework
    </p>

</footer>


</body>

</html>