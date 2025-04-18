<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitizing and validate form data
    $uname = $_POST["uname"];
    $email = $_POST["email"];
    $pwd = $_POST["pwd"];
    $user_type = $_POST["user_type"];

    try {
        require_once "includes/connect_db.inc.php";

        // Error handling
        $errors = [];

        // uname validation
        if (empty($uname)) {
            $errors[] = "Name Required";
        }

        // Validate email
        if (empty($email)) {
            $errors[] = "Email required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Invalid email format.";
        }

        // Validate password
        if (empty($pwd)) {
            $errors[] = "Password required.";
        }

        // If no errors, proceed to insert data into the database
        if (empty($errors)) {
            try {
                // Hash the password
                $hashed_pwd = password_hash($pwd, PASSWORD_DEFAULT);

                // Prepare SQL statement to check if the email already exists
                $stmt = $pdo->prepare("SELECT * FROM bookstore.users WHERE email = :email;");
                $stmt->bindParam(":email", $email);
                $stmt->execute();

                // Checking if the email already exists
                if ($stmt->rowCount() > 0) {
                    $errors[] = "This email is already registered.";
                } else {
                    // If the email does not exist, insert the new user into the database
                    $insert_stmt = $pdo->prepare("INSERT INTO bookstore.users (username, password, email, user_type) 
                                                  VALUES (:uname, :pwd, :email, :user_type);");
                    $insert_stmt->bindParam(":uname", $uname);
                    $insert_stmt->bindParam(":pwd", $hashed_pwd);
                    $insert_stmt->bindParam(":email", $email);
                    $insert_stmt->bindParam(":user_type", $user_type);

                    if ($insert_stmt->execute()) {
                        echo "<script>alert('Registration successful! Please log in.'); window.location.href = 'index.php';</script>";
                    } else {
                        $errors[] = "An error occurred while registration. Please try again.";
                    }
                }
            } catch (PDOException $e) {
                $errors[] = "Query Failed: " . $e->getMessage();
            }
        }
    } catch (PDOException $e) {
        die("Error!: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styler.css">
    <title>Register</title>
</head>
<body>
    <main>
        <form action="" method="post">
            <h3>Register Now</h3>

            <?php
            // Display errors if any
            if (!empty($errors)) {
                echo '<div class="error-message">';
                foreach ($errors as $error) {
                    echo "<p>$error</p>";
                }
                echo '</div>';
            }
            ?>

            <input type="text" name="uname" placeholder="Enter your name" class="box" value="<?= isset($uname) ? $uname : '' ?>">
            <input type="email" name="email" placeholder="Enter your email" class="box" value="<?= isset($email) ? $email : '' ?>">
            <input type="password" name="pwd" placeholder="Enter your password" class="box">
            <select name="user_type" class="box">
                <option value="user" <?= isset($user_type) && $user_type == "user" ? "selected" : "" ?>>Customer</option>
                <option value="admin" <?= isset($user_type) && $user_type == "admin" ? "selected" : "" ?>>Admin</option>
            </select>
            <input type="submit" name="submit" value="Register" class="btn">
            <p>Already have an account? <a href="index.php">Login</a></p>
        </form>
    </main>
</body>
</html>




<!-- ALTER TABLE `users` CHANGE `password` `password` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL; -->