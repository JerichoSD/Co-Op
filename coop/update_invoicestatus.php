<?php
include 'dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $invoiceId = $_POST['invoice_id'];
    $status = $_POST['status'];

    $sql = "UPDATE Invoice SET Status = '$status' WHERE Invoice_id = '$invoiceId'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Invoice status updated successfully!'); window.location.href='invoice.php';</script>";
    } else {
        echo "<script>alert('Failed to update invoice status.'); window.location.href='invoice.php';</script>";
    }

    mysqli_close($conn);
}
?>