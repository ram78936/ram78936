<?php

session_start();

require "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare(
        "SELECT id, name, email, password
         FROM users
         WHERE email = ?"
    );

    $stmt->bind_param("s", $email);

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        if (
            password_verify(
                $password,
                $user["password"]
            )
        ) {

            session_regenerate_id(true);

            $_SESSION["user_id"] =
                $user["id"];

            $_SESSION["user_name"] =
                $user["name"];

            $_SESSION["user_email"] =
                $user["email"];

            header(
                "Location: dashboard.php"
            );

            exit;

        } else {

            $message =
                "Invalid email or password.";
        }

    } else {

        $message =
            "Invalid email or password.";
    }
}

?>

<!DOCTYPE html>

<html>

<head>

    <title>FoodGo Login</title>

    <link rel="stylesheet"
          href="style.css">

</head>

<body>

<div class="auth-container">

    <h2>FoodGo Login</h2>

    <?php

    if (isset($_GET["registered"])) {

        echo "<p class='success'>
                Registration successful.
                Please login.
              </p>";
    }

    ?>

    <?php if ($message): ?>

        <p class="error">
            <?= htmlspecialchars($message) ?>
        </p>

    <?php endif; ?>

    <form method="POST">

        <input
            type="email"
            name="email"
            placeholder="Email"
            required
        >

        <input
            type="password"
            name="password"
            placeholder="Password"
            required
        >

        <button type="submit">
            Login
        </button>

    </form>

    <p>
        Don't have an account?

        <a href="register.php">
            Register
        </a>
    </p>

</div>

</body>

</html>