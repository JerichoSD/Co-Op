<?php
	include'dbconnect.php';
	
	$sql = "DELETE FROM Transactions WHERE Transaction_id='" . $_GET["transaction_id"] . "'";
	
	if (mysqli_query($conn, $sql)) {
        header("Location: transactions.php?delete_success=1");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
	mysqli_close($conn);
?>