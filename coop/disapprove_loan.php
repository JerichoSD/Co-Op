<?php
	include'dbconnect.php';
	
	$sql = "DELETE FROM Loans WHERE Loan_id='" . $_GET["loan_id"] . "'";
	
	if (mysqli_query($conn, $sql)) {
        header("Location: pendingloans.php?delete_success=1");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
	mysqli_close($conn);
?>