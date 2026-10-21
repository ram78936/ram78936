<?php

session_start();

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit;
}

?>

<!DOCTYPE html>

<html>

<head>

    <title>FoodGo Dashboard</title>

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
            My Orders
        </a>

        <a href="logout.php">
            Logout
        </a>

    </nav>

</header>


<section class="section">

    <h1>
        Welcome,
        <?= htmlspecialchars($_SESSION["user_name"]) ?>!
    </h1>

    <p>
        You are successfully logged in.
    </p>

    <br>

    <a href="restaurants.php">

        <button>
            Browse Restaurants
        </button>

    </a>

</section>

</body>

</html>