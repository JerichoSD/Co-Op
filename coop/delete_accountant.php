<?php
	include'dbconnect.php';
	
	$sql = "DELETE FROM Accountant WHERE User_id='" . $_GET["user_id"] . "'";
	
	if (mysqli_query($conn, $sql)) {
        header("Location: user.php?delete_success=1");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
	mysqli_close($conn);
?>