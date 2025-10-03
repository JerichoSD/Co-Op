<?php
    include 'dbconnect.php';
    session_start();

    $userID = isset($_SESSION['UserID']) ? $_SESSION['UserID'] : '';
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;

    if ($userID && $amount > 0) {
        $query = "SELECT Name FROM Member WHERE User_id = '$userID'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $member = mysqli_fetch_assoc($result);
            $memberName = $member['Name'];

            $status = 'Pending';
            $transactionType = 'Deposit';
            $currentDate = date('Y-m-d H:i:s');

            $insertQuery = "INSERT INTO Transactions (Member_Name, Transaction_Type, Amount, Date, Status) 
                            VALUES ('$memberName', '$transactionType', $amount, '$currentDate', '$status')";
            
            if (mysqli_query($conn, $insertQuery)) {
                echo "Deposit request submitted successfully.";
                header("Location: balance.php");
                exit();
            } else {
                echo "Error saving deposit record: " . mysqli_error($conn);
            }
        } else {
            echo "Member record not found.";
        }
    } else {
        echo "Invalid request.";
    }

    mysqli_close($conn);
?>
