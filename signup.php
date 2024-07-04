<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST["name"];
        $gender = $_POST["gender"];
        $email = $_POST["email"];
        $mobile = $_POST["mobile"];
        $city = $_POST["city"];
        $pass = $_POST["password"];
        
        // Hash the password
        $hashed_password = password_hash($pass, PASSWORD_BCRYPT);
        
        // Database connection
        $con = new mysqli('localhost', 'shru', 'shru', 'Quizy');

        if ($con->connect_error) {
            die("Connection Failed: " . $con->connect_error);
        } else {
            // Prepare the SQL statement
            $stmt = $con->prepare("INSERT INTO user (name, gender, email, mobile, city, password) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt === false) {
                die("Prepare failed: " . $con->error);
            }

            // Bind parameters
            $stmt->bind_param("ssssss", $name, $gender, $email, $mobile, $city, $hashed_password);

            // Execute the statement
            if ($stmt->execute()) {
                echo "<script>alert('Registration successful');</script>";
            } else {
                echo "<script>alert('Registration failed , Try again later.');</script>";
            }

            $stmt->close();
            $con->close();

            echo "<script>setTimeout(function() { window.location.href = 'index.php'; }, 2000);</script>";
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Create Account</h2>
        <form action="" method="POST" onsubmit="return validateForm()">
            <div class="row">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" placeholder="Enter your name" required>
            </div>
            <div class="row">
                <label>Gender:</label>
                <input type="radio" id="female" name="gender" value="female" required> 
                <label for="female">Female</label>
                <input type="radio" id="male" name="gender" value="male" required> 
                <label for="male">Male</label>
                <input type="radio" id="others" name="gender" value="others" required> 
                <label for="others">Others</label>
            </div>
            <div class="row">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="row">
                <label for="mobile">Mobile no.:</label>
                <input type="tel" id="mobile" name="mobile" placeholder="Enter your mobile number" pattern="[0-9]{10}" required>
            </div>
            <div class="row">
                <label for="city">City:</label>
                <input type="text" id="city" name="city" placeholder="Enter your city" required>
            </div>
            <div class="row">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="row">
                <label for="cpassword">Confirm Password:</label>
                <input type="password" id="cpassword" name="cpassword" placeholder="Confirm your password" required>
            </div>
            <input type="submit" value="Create Account">
            <a href="login.html" class="back-link">Go back</a>
        </form>
    </div>

    <script>
        function validateForm() {
            var password = document.getElementById("password").value;
            var cpassword = document.getElementById("cpassword").value;

            if (password !== cpassword) {
                alert("Passwords do not match");
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
