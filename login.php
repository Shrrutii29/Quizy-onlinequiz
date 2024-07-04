<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Database connection
    $con = new mysqli('localhost', 'shru', 'shru', 'Quizy');

    if ($con->connect_error) {
        die("Connection Failed: " . $con->connect_error);
    } else {
        // Prepare and execute the SQL statement
        $stmt = $con->prepare("SELECT * FROM user WHERE email = ?");
        if ($stmt === false) {
            die("Prepare failed: " . $con->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            $hashed_password = $user['password'];

            // Debugging: Check the hashed password and input password
            error_log("Hashed password from DB: " . $hashed_password);
            error_log("Entered password: " . $password);

            // Verify the password
            if (password_verify($password, $hashed_password)) {
                // Set session variables
                $_SESSION['uid'] = $user['uid'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];

                // Redirect to a protected page
                header("Location: index.php");
                exit();
            } else {
                echo "<script>alert('Invalid email or password');</script>";
            }
        } else {
            echo "<script>alert('Invalid email or password');</script>";
        }

        $stmt->close();
        $con->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="post">
            <div class="row">
                <label for="email">Email: </label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="row">
                <label for="password">Password: </label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <input type="submit" value="Sign in">
            
            <div class="login-nav">
                <a href="forgotpass.php" class="back-link">Forgot Password ??</a>
                <a href="signup.php" class="back-link">Create Account</a>
                <a href="index.php" class="back-link">Go back</a>
            </div>
        </form>
    </div>
</body>
</html>
