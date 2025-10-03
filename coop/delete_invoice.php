<?php
	include'dbconnect.php';
	
	$sql = "DELETE FROM Invoice WHERE Invoice_id='" . $_GET["invoice_id"] . "'";
	
	if (mysqli_query($conn, $sql)) {
        header("Location: invoice.php?delete_success=1");
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
	mysqli_close($conn);
?>