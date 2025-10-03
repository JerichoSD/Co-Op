<?php
include 'dbconnect.php';
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

$userID = isset($_SESSION['UserID']) ? $_SESSION['UserID'] : '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['applyLoan'])) {
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    $months = isset($_POST['months']) ? intval($_POST['months']) : 0;

    if ($userID && $amount > 0 && in_array($months, [6, 12, 24, 36])) {
        $sql = "INSERT INTO Loans (Member_id, Amount, Months, Status) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $status = "Pending";

        $stmt->bind_param('sdss', $userID, $amount, $months, $status);
        if ($stmt->execute()) {
            echo "<script>alert('Loan application submitted successfully.'); window.location.href='balance.php';</script>";
        } else {
            echo "<script>alert('Failed to submit loan application. Please try again.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Invalid input. Please try again.'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Invalid request method.'); window.history.back();</script>";
}
?>