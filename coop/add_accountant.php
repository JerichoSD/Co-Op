<?php
include 'dbconnect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["addAccountant"])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    $profilePic = $_FILES['profilePic'];

    if ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match.'); window.location.href='homepage.php';</script>";
        exit();
    }

    $checkEmailQuery = "SELECT * FROM Accountant WHERE Email = '$email'";
    $checkEmailResult = mysqli_query($conn, $checkEmailQuery);

    if (mysqli_num_rows($checkEmailResult) > 0) {
        echo "<script>alert('Email already exists. Please use a different email.'); window.location.href='homepage.php';</script>";
        exit();
    }

    $targetDir = "uploads/";
    $fileName = basename($profilePic["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    if (!in_array($fileType, ["jpg", "jpeg", "png", "gif"])) {
        echo "<script>alert('Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.'); window.location.href='homepage.php';</script>";
        exit();
    }

    if (!move_uploaded_file($profilePic["tmp_name"], $targetFilePath)) {
        echo "<script>alert('Failed to upload profile picture.'); window.location.href='homepage.php';</script>";
        exit();
    }


    $sql = "INSERT INTO Accountant (Name, Email, Password, ProfilePic) VALUES ('$name', '$email', '$password', '$targetFilePath')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Accountant added successfully!'); window.location.href='homepage.php';</script>";
    } else {
        echo "<script>alert('Error adding accountant: " . mysqli_error($conn) . "'); window.location.href='homepage.php';</script>";
    }
}

mysqli_close($conn);
?>
