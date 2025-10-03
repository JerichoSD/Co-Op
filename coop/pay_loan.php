<?php
    include 'dbconnect.php';
    session_start();

    $userID = isset($_SESSION['UserID']) ? $_SESSION['UserID'] : '';
    
    if ($userID) {
        $memberQuery = "SELECT Name FROM Member WHERE User_id = '$userID'";
        $loanQuery = "SELECT MonthlyPay FROM Loans WHERE Member_id = '$userID' AND Status = 'Active'";

        $memberResult = mysqli_query($conn, $memberQuery);
        $loanResult = mysqli_query($conn, $loanQuery);

        if ($memberResult && mysqli_num_rows($memberResult) > 0 && $loanResult && mysqli_num_rows($loanResult) > 0) {
            $member = mysqli_fetch_assoc($memberResult);
            $loan = mysqli_fetch_assoc($loanResult);

            $memberName = $member['Name'];
            $monthlyPayment = floatval($loan['MonthlyPay']);

            $status = 'Pending';
            $transactionType = 'Loan Payment';
            $currentDate = date('Y-m-d H:i:s');

            $insertTransactionQuery = "INSERT INTO Transactions (Member_Name, Transaction_Type, Amount, Date, Status)
                                       VALUES ('$memberName', '$transactionType', $monthlyPayment, '$currentDate', '$status')";

            if (mysqli_query($conn, $insertTransactionQuery)) {
                echo "Loan payment request submitted successfully.";
                header("Location: balance.php");
                exit();
            } else {
                echo "Error processing loan payment request: " . mysqli_error($conn);
            }
        } else {
            echo "Member or loan record not found.";
        }
    } else {
        echo "Invalid request.";
    }

    mysqli_close($conn);
?>
