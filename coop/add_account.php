<?php
include "dbconnect.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['submit'])) {	 
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $cpass = $_POST['confirm_password'];
    $utype = $_POST['utype'];

    $checkUserQuery = "SELECT * FROM UserAccounts WHERE Email = '$email'";
    $result = mysqli_query($conn, $checkUserQuery);

    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('Email already used. Please use another email address.'); window.location.href='add_account.php';</script>";
        exit();
    } else {
        if ($pass !== $cpass) {
            echo "<script>alert('Passwords do not match!'); window.location.href='add_account.php';</script>";
            exit(); 
        } else {
            $sql = "INSERT INTO UserAccounts (Name, User_type, Email, Password) VALUES ('$name', '$utype', '$email', '$pass')";
            if (mysqli_query($conn, $sql)) {
                echo "<script>alert('Account created successfully!'); window.location.href='user.php';</script>";
                exit();
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
            }
        }
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add New User</title>
    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-image: url('homepagebg.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .top-banner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #800000;
            color: white;
            padding: 10px 20px;
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            width: 300px;
            height: 60px;
            margin-right: 15px;
        }

        .menu-bar {
            display: flex;
            gap: 20px;
        }

        .menu-bar a {
            text-decoration: none;
            color: white;
            font-size: 16px;
            padding: 5px 10px;
            transition: background-color 0.3s;
        }

        .menu-bar a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
        }
    
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        .container {
            max-width: 500px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-size: 50px;
        }

        form label {
            font-size: 14px;
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        form input, form select, form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        form button {
            background-color: #800000;
            color: white;
            border: none;
            cursor: pointer;
        }

        form button:hover {
            background-color: #a00000;
        }

        .backbutton {
            text-align: center;
            margin-top: 10px;
        }

        .backbutton a {
            text-decoration: none;
            color: #800000;
        }
    </style>
</head>
<body>
    <div class="top-banner">
        <div class="logo">
            <a href="homepage.php"><img src="logo.png" alt="Logo"></a>
        </div>
        <div class="menu-bar">
            <a href="user.php">Users</a>
            <a href="#">Menu 2</a>
            <a href="#">Menu 3</a>
            <a href="#">Menu 4</a>
            <a href="#">Menu 5</a>
        </div>
    </div>
    <div class="container">
        <h1>Add New User<br>---------------------------</h1>
        <form method="post" action="add_account.php">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="role">User Type:</label>
            <select id="utype" name="utype" required>
                <option value="Administrator">Administrator</option>
                <option value="Member">Member</option>
                <option value="Accountant">Accountant</option>
            </select>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>

            <div style="text-align: center;">
                <input type="submit" name="submit" value="Create Account" style="padding: 10px 20px; font-size: 16px; cursor: pointer; background-color: #800000; color: white; border: none; border-radius: 5px;">
            </div>
        </form>
        <div class="backbutton">
            <a href="user.php" style = "text-decoration: none;">Back</a>
        </div>
    </div>
</body>
</html>
