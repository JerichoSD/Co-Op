<?php
include 'dbconnect.php';
session_start();

$userID = isset($_SESSION['UserID']) ? $_SESSION['UserID'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loan_id'])) {
    $loanID = $_POST['loan_id'];

    $sql = "SELECT * FROM Loans WHERE Loan_id = '$loanID'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $loan = mysqli_fetch_assoc($result);

        $amount = $loan['Amount'];
        $months = $loan['Months'];
        $memberID = $loan['Member_id'];

        $amountAfterIncrease = $amount + ($amount * 0.1);
        $monthlyPay = $amountAfterIncrease / $months;

        $currentDate = date('Y-m-d');
        $paymentStart = date('Y-m-d', strtotime('+1 month', strtotime($currentDate)));
        $paymentEnd = date('Y-m-d', strtotime("+$months months", strtotime($currentDate)));

        $updateSql = "UPDATE Loans 
                      SET Accountant_id = '$userID', 
                          Amount = '$amountAfterIncrease',
                          MonthlyPay = '$monthlyPay',
                          PaymentStart = '$paymentStart',
                          PaymentEnd = '$paymentEnd',
                          Status = 'Active'
                      WHERE Loan_id = '$loanID'";

        if (mysqli_query($conn, $updateSql)) {
            $accountantSql = "SELECT Name FROM Accountant WHERE User_id = '$userID'";
            $accountantResult = mysqli_query($conn, $accountantSql);
            $accountantName = ($accountantResult && mysqli_num_rows($accountantResult) > 0) 
                                ? mysqli_fetch_assoc($accountantResult)['Name'] 
                                : 'Unknown Accountant';

            $memberSql = "SELECT Name FROM Member WHERE User_id = '$memberID'";
            $memberResult = mysqli_query($conn, $memberSql);
            $memberName = ($memberResult && mysqli_num_rows($memberResult) > 0) 
                                ? mysqli_fetch_assoc($memberResult)['Name'] 
                                : 'Unknown Member';

            $transactionSql = "INSERT INTO Transactions (Accountant_Name, Member_Name, Transaction_Type, Amount, Date, Status) 
                               VALUES ('$accountantName', '$memberName', 'Loan', '$amountAfterIncrease', '$currentDate', 'Active')";

            if (mysqli_query($conn, $transactionSql)) {
                echo "Loan approved successfully, and transaction recorded.";
            } else {
                echo "Error inserting transaction record: " . mysqli_error($conn);
            }

            header('Location: pendingloans.php');
            exit();
        } else {
            echo "Error updating loan record: " . mysqli_error($conn);
            header('Location: pendingloans.php');
            exit();
        }
    } else {
        echo "Loan record not found.";
        header('Location: pendingloans.php');
        exit();
    }
} else {
    echo "Invalid request.";
    header('Location: pendingloans.php');
    exit();
}

mysqli_close($conn);
?>
