<?php
include 'dbconnect.php';
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = isset($_SESSION['UserID']) ? $_SESSION['UserID'] : '';
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : '';

    if (!$userID || !$role) {
        die("Unauthorized access.");
    }

    $name = isset($_POST['name']) ? $conn->real_escape_string(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? $conn->real_escape_string(trim($_POST['email'])) : '';
    $currentPassword = isset($_POST['currentPassword']) ? $_POST['currentPassword'] : '';
    $newPassword = isset($_POST['newPassword']) ? $_POST['newPassword'] : '';
    $confirmPassword = isset($_POST['confirmPassword']) ? $_POST['confirmPassword'] : '';
    
    if (empty($name) || empty($email)) {
        header("Location: homepage.php?update=failed&error=Name and email are required.");
        exit();
    }

    $table = '';
    if ($role === 'Administrator') {
        $table = 'Administrator';
    } 
    elseif ($role === 'Accountant') {
        $table = 'Accountant';
    } 
    elseif ($role === 'Member') {
        $table = 'Member';
    } 
    else {
        header("Location: homepage.php?update=failed&error=Invalid user role.");
        exit();
    }

    $profilePic = '';
    if (!empty($_FILES['profilePic']['name'])) {
        $uploadDir = 'uploads/';
        $profilePic = $uploadDir . basename($_FILES['profilePic']['name']);
        
        if (!move_uploaded_file($_FILES['profilePic']['tmp_name'], $profilePic)) {
            header("Location: homepage.php?update=failed&error=Error uploading profile picture.");
            exit();
        }
    }

    $passwordSQL = '';
    if (!empty($currentPassword) && !empty($newPassword) && $newPassword === $confirmPassword) {
        $sql = "SELECT Password FROM $table WHERE User_id = '$userID'";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if ($currentPassword === $user['Password']) {
                $passwordSQL = ", Password = '$newPassword'";
            } 
            else {
                header("Location: homepage.php?update=failed&error=Incorrect current password.");
                exit();
            }
        } 
        else {
            header("Location: homepage.php?update=failed&error=User not found.");
            exit();
        }
    } 
    elseif (!empty($newPassword) || !empty($confirmPassword)) {
        header("Location: homepage.php?update=failed&error=Passwords do not match.");
        exit();
    }

    $sql = "UPDATE $table SET 
                Name = '$name', 
                Email = '$email'
                " . ($profilePic ? ", ProfilePic = '$profilePic'" : "") . 
                $passwordSQL . "
            WHERE User_id = '$userID'";

    if ($conn->query($sql) === TRUE) {
        header("Location: homepage.php?update=success");
        exit();
    } 
    else {
        header("Location: homepage.php?update=failed&error=Error updating profile.");
        exit();
    }
} 
else {
    die("Invalid request method.");
}
?>
