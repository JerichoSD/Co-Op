<?php
include 'dbconnect.php';
session_start();

if (!isset($_POST['transaction_id']) || empty($_POST['transaction_id'])) {
    echo "<script>alert('Invalid transaction ID.'); window.location.href = 'transactions.php';</script>";
    exit();
}

$transaction_id = $_POST['transaction_id'];
$userID = isset($_SESSION['UserID']) ? $_SESSION['UserID'] : '';

$sql_transaction = "SELECT * FROM Transactions WHERE Transaction_id = '$transaction_id' AND Status = 'Pending'";
$result_transaction = $conn->query($sql_transaction);

if ($result_transaction && $result_transaction->num_rows > 0) {
    $transaction = $result_transaction->fetch_assoc();

    $transaction_type = $transaction['Transaction_Type'];
    $amount = $transaction['Amount'];
    $member_name = $transaction['Member_Name'];

    $sql_member = "SELECT * FROM Member WHERE Name = '$member_name'";
    $result_member = $conn->query($sql_member);

    if ($result_member && $result_member->num_rows > 0) {
        $member = $result_member->fetch_assoc();
        $user_id = $member['User_id'];
        $balance = $member['Balance'];

        // Check balance before proceeding with transaction types
        if (($transaction_type === 'Withdraw' || $transaction_type === 'Loan Payment') && $balance < $amount) {
            echo "<script>alert('Insufficient balance for $transaction_type.'); window.location.href = 'transactions.php';</script>";
            exit();
        }

        if ($transaction_type === 'Deposit') {
            $new_balance = $balance + $amount;
        } elseif ($transaction_type === 'Withdraw') {
            $new_balance = $balance - $amount;
        } elseif ($transaction_type === 'Loan Payment') {
            $new_balance = $balance - $amount;

            // Proceed with loan-related updates
            $sql_loan = "SELECT * FROM Loans WHERE Member_id = '$user_id' AND Status = 'Active'";
            $result_loan = $conn->query($sql_loan);

            if ($result_loan && $result_loan->num_rows > 0) {
                $loan = $result_loan->fetch_assoc();
                $loan_amount = $loan['Amount'];
                $already_paid = $loan['AlreadyPaid'];

                $new_loan_amount = $loan_amount - $amount;
                $new_already_paid = $already_paid + $amount;

                $sql_update_loan = "UPDATE Loans SET Amount = '$new_loan_amount', AlreadyPaid = '$new_already_paid' WHERE Member_id = '$user_id'";

                if (!$conn->query($sql_update_loan)) {
                    echo "<script>alert('Failed to update loan details: " . $conn->error . "'); window.location.href = 'transactions.php';</script>";
                    exit();
                }
            } else {
                echo "<script>alert('Active loan details not found for the member.'); window.location.href = 'transactions.php';</script>";
                exit();
            }

            // Update invoice status after successful loan payment
            $sql_update_invoice = "UPDATE Invoice SET Status = 'Paid' WHERE Member_id = '$user_id' AND Status = 'Pending'";
            if (!$conn->query($sql_update_invoice)) {
                echo "<script>alert('Failed to update invoice status: " . $conn->error . "'); window.location.href = 'transactions.php';</script>";
                exit();
            }
        } else {
            echo "<script>alert('Invalid transaction type.'); window.location.href = 'transactions.php';</script>";
            exit();
        }

        // Update the member's balance
        $sql_update_member = "UPDATE Member SET Balance = '$new_balance' WHERE User_id = '$user_id'";
        if (!$conn->query($sql_update_member)) {
            echo "<script>alert('Failed to update member balance: " . $conn->error . "'); window.location.href = 'transactions.php';</script>";
            exit();
        }

        // Get the accountant details
        $sql_accountant = "SELECT Name FROM Accountant WHERE User_id = '$userID'";
        $result_accountant = $conn->query($sql_accountant);

        if ($result_accountant && $result_accountant->num_rows > 0) {
            $accountant = $result_accountant->fetch_assoc();
            $accountant_name = $accountant['Name'];
        } else {
            echo "<script>alert('Accountant details not found.'); window.location.href = 'transactions.php';</script>";
            exit();
        }

        // Record the transaction details
        $current_date = date("Y-m-d");
        $sql_update_transaction = "
            UPDATE Transactions 
            SET Accountant_Name = '$accountant_name', Date = '$current_date', Status = 'Completed' 
            WHERE Transaction_id = '$transaction_id'
        ";

        if (!$conn->query($sql_update_transaction)) {
            echo "<script>alert('Failed to update transaction: " . $conn->error . "'); window.location.href = 'transactions.php';</script>";
            exit();
        }

        echo "<script>alert('Transaction approved successfully.'); window.location.href = 'transactions.php';</script>";
        exit();
    } else {
        echo "<script>alert('Member details not found.'); window.location.href = 'transactions.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Transaction not found or already completed.'); window.location.href = 'transactions.php';</script>";
    exit();
}

$conn->close();
?>
