<?php
include 'dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updateAccountant'])) {
    $userID = $_POST['user_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    $profilePic = '';

    if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] === UPLOAD_ERR_OK) {
        $profilePic = 'uploads/' . basename($_FILES['profilePic']['name']);
        move_uploaded_file($_FILES['profilePic']['tmp_name'], $profilePic);
    }

    if (empty($profilePic)) {
        $getProfilePicQuery = "SELECT ProfilePic FROM Accountant WHERE User_id = '$userID'";
        $result = $conn->query($getProfilePicQuery);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $profilePic = $row['ProfilePic'];
        }
    }

    if (!empty($newPassword) && $newPassword === $confirmPassword) {
        $updateQuery = "UPDATE Accountant SET Name = '$name', Email = '$email', ProfilePic = '$profilePic', Password = '$newPassword' WHERE User_id = '$userID'";
    } else {
        $updateQuery = "UPDATE Accountant SET Name = '$name', Email = '$email', ProfilePic = '$profilePic' WHERE User_id = '$userID'";
    }

    if ($conn->query($updateQuery) === TRUE) {
        echo "Accountant updated successfully.";
    } else {
        echo "Error updating accountant: " . $conn->error;
    }

    header("Location: user.php");
    exit;
}
?>
