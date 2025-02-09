<?php
session_start();
include 'dbConnect.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php'; // Make sure PHPMailer is installed

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password

    // Insert the new user into the database
    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashedPassword')";
    $query = mysqli_query($conn, $sql);

    if ($query) {
        // Generate OTP and set expiry time
        $otp = rand(100000, 999999);
        $otp_expiry = date("Y-m-d H:i:s", strtotime("+3 minute"));

        // Send OTP to the user's email
        $subject = "Your OTP for Registration";
        $message = "Your OTP is: $otp";

        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'leojoegem@gmail.com'; // Your email address
        $mail->Password = 'eofn inez hekr nfol'; // Your Gmail app password
        $mail->Port = 587;
        $mail->SMTPSecure = 'ssl';
        $mail->isHTML(true);
        $mail->setFrom('leojoegem@gmail.com', 'Online Examination System');
        $mail->addAddress($email); // Receiver's Email
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->send();

        // Update the OTP and expiry in the database
        $sql3 = "UPDATE users SET otp='$otp', otp_expiry='$otp_expiry' WHERE email='$email'";
        mysqli_query($conn, $sql3);

        // Store the temporary user data in the session
        $_SESSION['temp_user'] = ['email' => $email, 'otp' => $otp];

        // Redirect to OTP verification page
        ?>
        <script>
            alert("Registration successful. OTP sent to your email.");
            function navigateToPage() {
                window.location.href = 'otp_verification.php'; // Redirect to OTP verification page
            }
            window.onload = function() {
                navigateToPage();
            }
        </script>
        <?php
    } else {
        echo "<script> alert('Registration Failed. Try Again');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        #container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        input[type="submit"] {
            padding: 10px;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        a {
            text-decoration: none;
            color: #007BFF;
            font-size: 14px;
        }

        a:hover {
            text-decoration: underline;
        }

        .info {
            text-align: center;
            font-size: 14px;
        }

        .forgot-password {
            text-align: right;
            font-size: 14px;
        }

        .forgot-password a {
            color: #FF5722;
        }

        .forgot-password a:hover {
            color: #e64a19;
        }
    </style>
</head>
<body>
    <div id="container">
        <h2>Register</h2>
        <form method="post" action="registration.php">
            <label for="username">Username:</label>
            <input type="text" name="username" placeholder="Enter Username" required>

            <label for="email">Email:</label>
            <input type="text" name="email" placeholder="Enter Your Email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" placeholder="Enter Password" required>

            <input type="submit" name="register" value="Register">
        </form>

        <div class="info">
            Already have an account? <a href="index.php">Login</a>
        </div>

        <div class="forgot-password">
            <a href="forgot_password.php">Forgot Password?</a>
        </div>
    </div>
</body>
</html>
