<?php

session_start();

require "db.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit;
}

$result = $conn->query(
    "SELECT * FROM restaurants"
);

?>

<!DOCTYPE html>

<html>

<head>

    <title>Restaurants - FoodGo</title>

    <link rel="stylesheet"
          href="style.css">

</head>

<body>

<header>

    <div class="logo">
        🍔 FoodGo
    </div>

    <nav>

        <a href="dashboard.php">
            Home
        </a>

        <a href="restaurants.php">
            Restaurants
        </a>

        <a href="orders.php">
            Orders
        </a>

        <a href="logout.php">
            Logout
        </a>

    </nav>

</header>


<section class="section">

    <h2>
        Restaurants
    </h2>

    <div class="restaurant-grid">

        <?php while (
            $restaurant =
            $result->fetch_assoc()
        ): ?>

            <div class="restaurant-card">

                <img
                    src="<?= htmlspecialchars(
                        $restaurant["image"]
                    ) ?>"
                >

                <div class="restaurant-info">

                    <h3>
                        <?= htmlspecialchars(
                            $restaurant["name"]
                        ) ?>
                    </h3>

                    <p>
                        <?= htmlspecialchars(
                            $restaurant["cuisine"]
                        ) ?>
                    </p>

                    <p>
                        ⭐
                        <?= $restaurant["rating"] ?>
                    </p>

                    <a href="menu.php?id=
                        <?= $restaurant["id"] ?>">

                        <button>
                            View Menu
                        </button>

                    </a>

                </div>

            </div>

        <?php endwhile; ?>

    </div>

</section>

</body>

</html>