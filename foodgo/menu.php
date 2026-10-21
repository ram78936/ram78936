<?php

session_start();

require "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$restaurantId = filter_input(
    INPUT_GET,
    "id",
    FILTER_VALIDATE_INT
);

if (!$restaurantId) {
    header("Location: restaurants.php");
    exit;
}


// Get restaurant
$stmt = $conn->prepare(
    "SELECT *
     FROM restaurants
     WHERE id = ?"
);

$stmt->bind_param(
    "i",
    $restaurantId
);

$stmt->execute();

$restaurantResult = $stmt->get_result();

if ($restaurantResult->num_rows !== 1) {
    header("Location: restaurants.php");
    exit;
}

$restaurant = $restaurantResult->fetch_assoc();


// Selected filter
$category = $_GET["category"] ?? "All";


// Validate filter
$allowedCategories = [
    "All",
    "Veg",
    "Non-Veg"
];

if (!in_array($category, $allowedCategories)) {
    $category = "All";
}


// Get menu items
if ($category === "All") {

    $stmt = $conn->prepare(
        "SELECT *
         FROM menu_items
         WHERE restaurant_id = ?
         ORDER BY id"
    );

    $stmt->bind_param(
        "i",
        $restaurantId
    );

} else {

    $stmt = $conn->prepare(
        "SELECT *
         FROM menu_items
         WHERE restaurant_id = ?
         AND category = ?
         ORDER BY id"
    );

    $stmt->bind_param(
        "is",
        $restaurantId,
        $category
    );
}

$stmt->execute();

$menuItems = $stmt->get_result();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?php echo htmlspecialchars($restaurant["name"]); ?>
        - FoodGo
    </title>

    <link
        rel="stylesheet"
        href="style.css"
    >

    <style>

        .menu-container {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
        }

        .restaurant-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .restaurant-header h1 {
            margin-bottom: 10px;
        }

        .restaurant-header p {
            color: #666;
        }


        /* FILTER BUTTONS */

        .filter-container {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 35px;
            flex-wrap: wrap;
        }

        .filter-btn {
            text-decoration: none;
            padding: 11px 22px;
            border-radius: 25px;
            border: 1px solid #ddd;
            background: white;
            color: #333;
            font-weight: bold;
            transition: 0.2s;
        }

        .filter-btn:hover {
            transform: translateY(-2px);
        }

        .filter-btn.active {
            background: #ff512f;
            color: white;
            border-color: #ff512f;
        }


        /* FOOD GRID */

        .food-grid {
            display: grid;
            grid-template-columns:
                repeat(auto-fit, minmax(260px, 1fr));

            gap: 25px;
        }


        /* FOOD CARD */

        .food-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.12);
            transition: 0.2s;
        }

        .food-card:hover {
            transform: translateY(-5px);
        }

        .food-card img {
            width: 100%;
            height: 190px;
            object-fit: cover;
        }

        .food-content {
            padding: 18px;
        }

        .food-content h3 {
            margin: 0 0 8px;
        }


        /* VEG / NON VEG BADGE */

        .food-type {
            display: inline-block;
            padding: 4px 9px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .veg {
            background: #dff5e1;
            color: #16852a;
        }

        .nonveg {
            background: #ffe0e0;
            color: #d62828;
        }


        .price {
            font-size: 18px;
            font-weight: bold;
            margin: 10px 0;
        }


        /* ADD BUTTON */

        .add-btn {
            width: 100%;
            padding: 11px;
            border: none;
            border-radius: 6px;
            background: #ff512f;
            color: white;
            font-weight: bold;
            cursor: pointer;
            font-size: 15px;
        }

        .add-btn:hover {
            background: #e64224;
        }


        .back-btn {
            display: inline-block;
            margin-bottom: 25px;
            padding: 10px 18px;
            background: #333;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }


        .no-items {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 10px;
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

        <a href="orders.php">📦 Orders</a>

        <a href="logout.php">Logout</a>

    </nav>

</header>


<div class="menu-container">


    <a
        href="restaurants.php"
        class="back-btn"
    >
        ← Back to Restaurants
    </a>


    <!-- RESTAURANT INFORMATION -->

    <div class="restaurant-header">

        <h1>
            <?php
            echo htmlspecialchars($restaurant["name"]);
            ?>
        </h1>

        <p>
            🍴
            <?php
            echo htmlspecialchars($restaurant["cuisine"]);
            ?>

            &nbsp;&nbsp;

            ⭐
            <?php
            echo htmlspecialchars($restaurant["rating"]);
            ?>
        </p>

    </div>


    <!-- FILTER -->

    <div class="filter-container">

        <a
            href="menu.php?id=<?php echo $restaurantId; ?>&category=All"
            class="filter-btn <?php echo $category === 'All' ? 'active' : ''; ?>"
        >
            🍽 All
        </a>


        <a
            href="menu.php?id=<?php echo $restaurantId; ?>&category=Veg"
            class="filter-btn <?php echo $category === 'Veg' ? 'active' : ''; ?>"
        >
            🟢 Veg
        </a>


        <a
            href="menu.php?id=<?php echo $restaurantId; ?>&category=Non-Veg"
            class="filter-btn <?php echo $category === 'Non-Veg' ? 'active' : ''; ?>"
        >
            🔴 Non-Veg
        </a>

    </div>


    <!-- MENU -->

    <?php if ($menuItems->num_rows === 0): ?>

        <div class="no-items">

            <h2>
                No
                <?php echo htmlspecialchars($category); ?>
                items available.
            </h2>

            <p>
                Try another category.
            </p>

        </div>

    <?php else: ?>


        <div class="food-grid">


            <?php while ($item = $menuItems->fetch_assoc()): ?>


                <?php

                $itemCategory = $item["category"];

                $badgeClass =
                    $itemCategory === "Veg"
                    ? "veg"
                    : "nonveg";

                ?>


                <div class="food-card">


                    <img
                        src="<?php
                        echo htmlspecialchars(
                            $item["image"]
                        );
                        ?>"
                        alt="<?php
                        echo htmlspecialchars(
                            $item["name"]
                        );
                        ?>"
                    >


                    <div class="food-content">


                        <span
                            class="food-type <?php
                            echo $badgeClass;
                            ?>"
                        >

                            <?php if ($itemCategory === "Veg"): ?>

                                🟢 VEG

                            <?php else: ?>

                                🔴 NON-VEG

                            <?php endif; ?>

                        </span>


                        <h3>

                            <?php
                            echo htmlspecialchars(
                                $item["name"]
                            );
                            ?>

                        </h3>


                        <div class="price">

                            ₹<?php
                            echo number_format(
                                $item["price"],
                                2
                            );
                            ?>

                        </div>


                        <form
                            action="add_to_cart.php"
                            method="POST"
                        >

                            <input
                                type="hidden"
                                name="item_id"
                                value="<?php
                                echo (int)$item["id"];
                                ?>"
                            >

                            <button
                                type="submit"
                                class="add-btn"
                            >
                                🛒 Add to Cart
                            </button>

                        </form>


                    </div>

                </div>


            <?php endwhile; ?>


        </div>


    <?php endif; ?>


</div>


</body>

</html>