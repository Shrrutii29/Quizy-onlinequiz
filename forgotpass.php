<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];
    $cpassword = $_POST["cpassword"];
    
    if ($password !== $cpassword){
        echo "<script>alert('Passwords do not match');</script>";
    }
    else
    {
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
                $stmt->close();
            
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $con->prepare("UPDATE user SET password = ? WHERE email = ?");
                if ($stmt === false) {
                    die("Prepare failed: " . $con->error);
                }
            
                $stmt->bind_param("ss", $hashed_password, $email);
            
                if ($stmt->execute()) {
                    echo "<script>alert('Password reset successful');</script>";

                } else {
                    echo "<script>alert('Password reset failed, try again later.');</script>";
                }

                $stmt->close();
            } else {
                echo "<script>alert('Email not found');</script>";
            }
            $con->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Forgot Password</h2>
        <form method="post">
            <div class="row">
                <label for="email">Email: </label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="row">
                <label for="password">New Password: </label>
                <input type="password" id="password" name="password" placeholder="Enter your new password" required>
            </div>
            <div class="row">
                <label for="cpassword">Confirm New Password: </label>
                <input type="password" id="cpassword" name="cpassword" placeholder="Confirm your new password" required>
            </div>
            <input type="submit" value="Reset Password">
            
            <div class="login-nav">
                <a href="login.php" class="back-link">Login</a>
                <a href="signup.php" class="back-link">Create New Account</a>
                <a href="index.php" class="back-link">Back to Home</a>
            </div>
        </form>
    </div>
</body>
</html>
