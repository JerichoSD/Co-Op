<?php
    session_start();
    include 'dbconnect.php';

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $uname = $_POST['username'];
        $pass = $_POST['password'];

        $sql_admin = "SELECT * FROM Administrator WHERE Email = '$uname' AND Password = '$pass'";
        $result_admin = $conn->query($sql_admin);
        
        if ($result_admin->num_rows > 0) {
            $user = $result_admin->fetch_assoc();
            $_SESSION['UserID'] = $user['User_id'];
            $_SESSION['role'] = 'Administrator';
            echo "<script>alert('Login successful as Administrator!'); window.location.href='homepage.php';</script>";
        } 
        else {
            $sql_accountant = "SELECT * FROM Accountant WHERE Email = '$uname' AND Password = '$pass'";
            $result_accountant = $conn->query($sql_accountant);

            if ($result_accountant->num_rows > 0) {
                $user = $result_accountant->fetch_assoc();
                $_SESSION['UserID'] = $user['User_id'];
                $_SESSION['role'] = 'Accountant';
                echo "<script>alert('Login successful as Accountant!'); window.location.href='homepage.php';</script>";
            }
            else {
                $sql_member = "SELECT * FROM Member WHERE Email = '$uname' AND Password = '$pass'";
                $result_member = $conn->query($sql_member);

                if ($result_member->num_rows > 0) {
                    $user = $result_member->fetch_assoc();
                    $_SESSION['UserID'] = $user['User_id'];
                    $_SESSION['role'] = 'Member';
                    $_SESSION['email'] = $uname;
                    $_SESSION['member_name'] = $user['Name'];
                    echo "<script>alert('Login successful as Member!'); window.location.href='homepage.php';</script>";
                } else {
                    echo "<script>alert('Invalid username or password.');</script>";
                }
            }
        }
    }

    $conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        .background {
            background-image: url(loginbg.jpg);
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .loginbox {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(20px);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
            width: 400px;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
            font-size:  35px;
        }

        .forms {
            width: 100%;
            margin-bottom: 15px;
        }

        label {
            display: block;
            font-size:  25px;
            color: #333;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size:  20px;
        }

        .btn {
            width: 100%;
            padding: 10px;
            background-color: blue;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size:  20px;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="background"></div>
    <div class="loginbox">
        <form action="index.php" method="post">
            <h2>Login</h2>
            <div class="forms">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="forms">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</body>
</html>
