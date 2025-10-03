<?php

require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function OverdueEmails($conn) {
    $sql_fetch_overdue = "
        SELECT Invoice.Invoice_id, Invoice.Member_id, Member.Email 
        FROM Invoice 
        INNER JOIN Member ON Invoice.Member_id = Member.User_id
        WHERE Invoice.Status = 'Overdue' AND Invoice.OverdueEmail = 'No'";

    $result_fetch_overdue = $conn->query($sql_fetch_overdue);

    if ($result_fetch_overdue && $result_fetch_overdue->num_rows > 0) {
        while ($row = $result_fetch_overdue->fetch_assoc()) {
            $invoiceID = $row['Invoice_id'];
            $memberID = $row['Member_id'];
            $email = $row['Email'];

            $mail = new PHPMailer();

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'dineros.jerichos.bsit@gmail.com';
            $mail->Password = 'wxjj srdg efbl ohmx';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('no-reply@yourdomain.com', 'Accountant');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Loan Overdue';
            $mail->Body = "
                Dear Member,<br><br>
                You have a loan payment that is overdue. Please contact an accountant to resolve this issue.<br><br>
                Thank you.";

            if ($mail->send()) {
                $sql_update_overdue_email = "UPDATE Invoice SET OverdueEmail = 'Yes' WHERE Invoice_id = '$invoiceID'";
                if (!$conn->query($sql_update_overdue_email)) {
                    echo "Error updating OverdueEmail status for Invoice ID $invoiceID: " . $conn->error . "<br>";
                }
            }
        }
    }
}
