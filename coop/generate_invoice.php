<?php
include 'dbconnect.php';

if (isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];

    $sql_loans = "SELECT * FROM Loans WHERE Member_id = '$userId' AND Status = 'Active'";
    $result_loans = mysqli_query($conn, $sql_loans);

    if (mysqli_num_rows($result_loans) > 0) {
        while ($loan = mysqli_fetch_array($result_loans)) {
            $paymentStart = $loan['PaymentStart'];
            $monthlyPay = $loan['MonthlyPay'];
            $paymentDay = (new DateTime($paymentStart))->format('d');
            $currentMonth = date('Y-m');
            $dueDate = "$currentMonth-$paymentDay";

            $sql_check_pending_invoice = "SELECT * FROM Invoice WHERE Member_id = '$userId' AND Status = 'Pending'";
            $result_check_pending_invoice = mysqli_query($conn, $sql_check_pending_invoice);

            if (mysqli_num_rows($result_check_pending_invoice) > 0) {
                echo "<script>alert('There is already a pending invoice for this member.');</script>";
            } else {
                $sql_check_invoice = "SELECT * FROM Invoice WHERE Member_id = '$userId' AND Due = '$dueDate'";
                $result_check_invoice = mysqli_query($conn, $sql_check_invoice);

                if (mysqli_num_rows($result_check_invoice) === 0) {
                    $insertInvoice = "INSERT INTO Invoice (Member_id, Pay, Due, Status, OverdueEmail) VALUES ('$userId', '$monthlyPay', '$dueDate', 'Pending', 'No')";
                    if (mysqli_query($conn, $insertInvoice)) {
                        echo "<script>alert('Invoice generated successfully!');</script>";
                    } else {
                        echo "<script>alert('Failed to generate invoice: " . mysqli_error($conn) . "');</script>";
                    }
                } else {
                    echo "<script>alert('Invoice already exists for this due date.');</script>";
                }
            }
        }
    } else {
        echo "<script>alert('No active loans found for this user.');</script>";
    }
} else {
    echo "<script>alert('No user ID specified.');</script>";
}
echo "<script>window.location.href='activeloans.php';</script>";
?>
