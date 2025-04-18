<?php


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $pwd = $_POST["pwd"]; // User-entered password

    try {
        session_start();
        require_once "includes/connect_db.inc.php"; 

        $errors = [];


        if (empty($email) || empty($pwd)) {
            $errors[] = "Email and password are required.";
        }

        if (empty($errors)) {
            try {
                // Prepare SQL to get the user based on the email
                $stmt = $pdo->prepare("SELECT * FROM bookstore.users WHERE email = :email;");
                $stmt->bindParam(":email", $email);
                $stmt->execute();
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // Check if user exists and verify password
                if ($user) {
                    // Verify the password using password_verify
                    // If password verification fails, rehash the password and update in the database
                        if (password_verify($pwd, $user['password']) === false) {
                            // If the entered password does not match the hash in the database,
                            // rehash the password and update the database.
                            $newHashedPassword = password_hash($pwd, PASSWORD_DEFAULT);
                            
                            // Update the password in the database with the new hash
                            $stmt = $pdo->prepare("UPDATE bookstore.users SET password = :password WHERE email = :email");
                            $stmt->bindParam(":password", $newHashedPassword);
                            $stmt->bindParam(":email", $email);
                            $stmt->execute();

                            // Now that the password is rehashed and updated, we can proceed with login
                        }

                        // Now verify the password after it might have been rehashed
                    if (password_verify($pwd, $user['password'])) {
                        // Password matches, set session variables
                        $_SESSION["user_id"] = $user["id"];
                        $_SESSION["user_name"] = $user["username"];
                        $_SESSION["user_type"] = $user["user_type"];

                        // // Redirect based on user type
                        if ($user["user_type"] === "admin") {
                            header("Location: admindashboard.php");
                            exit();
                        } else {
                            header("Location: home.php");
                            exit();
                        } 
                    } else {
                        // Invalid password
                        $errors[] = "Invalid email or password.";
                    }
                } else {
                    
                    $errors[] = "Invalid email or password.";
                }
            } catch (PDOException $e) {
                // Handle database errors
                $errors[] = "An error occurred: " . $e->getMessage();
            }
        }

        // Output errors if any
        if (!empty($errors)) {
            foreach ($errors as $error) {
                echo $error . "<br>";
            }
        }

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}



?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/stylel.css">
    <title>Login</title>
</head>
<body>
    <main>
        <form action="" method="post">
            <h3>Login Form</h3>

            <!-- Display errors if there are any -->
            <?php if (!empty($errors)): ?>
                <div class="error-messages">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            
            <input type="email" name="email" placeholder="Enter your email" class="box" required>
            <input type="password" name="pwd" placeholder="Enter your password" class="box" required><br>
            <input type="submit" name="login" value="Login" class="btn">
            <p>Don't have an account? <a href="register.php">Register Now</a></p>
        </form>
    </main>
</body>
</html>



