<?php
include 'dbconnect.php';
session_start();
include 'overdue_email.php';
OverdueEmails($conn);
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$userID = isset($_SESSION['UserID']) ? $_SESSION['UserID'] : '';
$profileImagePath = 'uploads/admin.jpg';

ini_set('display_errors', 1);
error_reporting(E_ALL);


if ($userID && $role) {
    if ($role === 'Administrator') {
        $sql = "SELECT * FROM Administrator WHERE User_id = '$userID'";
    } elseif ($role === 'Accountant') {
        $sql = "SELECT * FROM Accountant WHERE User_id = '$userID'";
    } elseif ($role === 'Member') {
        $sql = "SELECT * FROM Member WHERE User_id = '$userID'";
    } else {
        echo "Invalid role or table.";
    }

    if (!empty($sql)) {
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $profileImagePath = isset($user['ProfilePic']) && !empty($user['ProfilePic']) ? $user['ProfilePic'] : 'uploads/admin.jpg';
        }
    }
    else {
        echo "Invalid role or table.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Homepage</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .content {
            margin-top: 20px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
            text-align: center;
        }

        .pagetitle {
            color: #333;
            margin-bottom: 30px;
            font-size: 60px;
        }

        .content p {
            color: #555;
            font-size: 16px;
            line-height: 1.5;
        }
        table{
            margin: 0 auto;
            background-color: antiquewhite;
        }
        td{
            font-size: 20px;
            text-allign: center;
            padding: 10px;
        }
        th{
            font-size: 28px;
            text-allign: center;
            padding: 10px;
            background-color: rgb(210, 4, 45);
        }
        td button {
            background-color: rgb(210, 4, 45);
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            font-size: 20px;
        }
        table, th, tr, td{
            border: solid 5px;
            border-collapse: collapse;
        }
        .userbutton {
            margin-top: 20px;
            margin-bottom: 20px;
            background-color: rgb(210, 4, 45);
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 18px;
            cursor: pointer;
            border-radius: 5px;
            margin-right: 20px;
            transition: background-color 0.3s;
        }

        .userbutton:hover {
            background-color: #990000;
        }

        .content h1 {
            color: #333;
            margin-bottom: 15px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            margin: auto;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .modal-content h1 {
            margin-bottom: 20px;
            color: #333;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 50px;
            cursor: pointer;
            color: #800000;
        }

        .edit-profile-form {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .edit-profile-form input,
        .edit-profile-form select {
            width: 80%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .edit-profile-form button {
            padding: 10px 20px;
            background-color: #800000;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .edit-profile-form button:hover {
            background-color: #7a0000;
        }
    </style>
</head>
<body>
    <div class="top-banner">
        <div class="logo">
            <a href="homepage.php"><img src="logo.png" alt="Logo"></a>
        </div>
        <div class="menu-bar">
            <?php 
                if (isset($role) && ($role === 'Administrator' || $role === 'Accountant')) {
                    $href = ($role === 'Accountant') ? 'user2.php' : 'user.php';
            ?>
                    <a href="<?php echo $href; ?>">Users</a>
            <?php } ?>
            <?php 
                if (isset($role) && $role === 'Member') {
                    echo '<a href="balance.php">Balance</a>';
                } else {
                    echo '<a href="activeloans.php">Loans</a>';
                }
            ?>
            <?php 
                if (isset($role) && ($role === 'Administrator' || $role === 'Accountant')) {
            ?>
                <a href="transactions.php">Transactions</a>
            <?php } ?>
            <?php 
                if (isset($role) && ($role === 'Administrator' || $role === 'Accountant')) {
            ?>
                <a href="invoice.php">Invoices</a>
            <?php } ?>
            <?php 
                if (isset($role) && ($role === 'Administrator' || $role === 'Accountant')) {
            ?>
                <a href="report.php">Reports</a>
            <?php } ?>
        </div>
        <div class="profile">
            <button class="profile-btn" onclick="toggleMenu()">
                <img src="<?php echo $profileImagePath; ?>" alt="Profile Picture">
            </button>
            <div id="dropdown-menu" class="dropdown-menu">
                <a href="javascript:void(0)" onclick="openProfileModal()">View Profile</a>
                <a href="javascript:void(0)" onclick="openEditProfileModal()">Edit Account</a>
                <a href="index.php">Log Out</a>
            </div>
        </div>
    </div>

    <div class="content">
        <label class='pagetitle';>Invoices</label>
        <br>
        <?php
            $sql_accountant = "SELECT * FROM Invoice";
            $result_invoice = mysqli_query($conn, $sql_accountant);

            if (mysqli_num_rows($result_invoice) > 0) {
            ?>
                <label><br><br></label>
                <table>
                    <th><label style="color: white;">Invoice ID</label></th>
                    <th><label style="color: white;">Member ID</label></th>
                    <th><label style="color: white;">Pay</label></th>
                    <th><label style="color: white;">Due</label></th>
                    <th><label style="color: white;">Status</label></th>
                    <th colspan="3"><label style="color: white;">Actions</label></th>
                    <?php
                        while ($row = mysqli_fetch_array($result_invoice)) {
                    ?>
                    <tr>
                        <td style="text-align: center;"><?php echo $row["Invoice_id"]; ?></td>
                        <td style="text-align: center;"><?php echo $row["Member_id"]; ?></td>
                        <td><?php echo $row["Pay"]; ?></td>
                        <td><?php echo $row["Due"]; ?></td>
                        <td><?php echo $row["Status"]; ?></td>
                        <td><button onclick="openEditInvoiceModal('<?php echo $row['Invoice_id']; ?>', '<?php echo $row['Status']; ?>')">Edit</button></td>
                        <td><a href="delete_invoice.php?invoice_id=<?php echo $row['Invoice_id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');"><button>Delete</button></a></td>
                    </tr>
                    <?php
                    }
                    ?>
                </table>
                <?php
                    }
                    else{
                        echo "No result found";
                    }
                ?>
                
    </div>

    <div id="edit-invoice-modal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center;">
        <div class="modal-content" style="background-color: #fff; padding: 30px 40px; border-radius: 10px; width: 400px; box-shadow: 0px 8px 15px rgba(0, 0, 0, 0.2); text-align: center; position: relative;">
            <span class="close-btn" onclick="closeEditInvoiceModal()" style="position: absolute; top: 10px; right: 20px; font-size: 24px; font-weight: bold; cursor: pointer; color: #d9534f;">&times;</span>
            <h1 style="font-size: 24px; margin-bottom: 20px; color: #333;">Edit Invoice Status</h1>
            <form method="POST" action="update_invoicestatus.php" style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
                <input type="hidden" id="edit-invoice-id" name="invoice_id" value="">
                <label for="status" style="font-size: 16px; color: #555; margin-bottom: 5px;">Change Status:</label>
                <select id="edit-invoice-status" name="status" required style="width: 90%; padding: 10px; font-size: 16px; border: 1px solid #ccc; border-radius: 5px; outline: none; transition: border-color 0.3s;">
                    <option value="Pending">Pending</option>
                    <option value="Paid">Paid</option>
                    <option value="Overdue">Overdue</option>
                </select>
                <button type="submit" style="padding: 10px 20px; font-size: 16px; color: white; background-color: #007bff; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.3s;">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        let editInvoiceModal = document.getElementById('edit-invoice-modal');

        function openEditInvoiceModal(invoiceId, currentStatus) {
            document.getElementById('edit-invoice-id').value = invoiceId;
            document.getElementById('edit-invoice-status').value = currentStatus;
            editInvoiceModal.style.display = 'flex';
        }

        function closeEditInvoiceModal() {
            editInvoiceModal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == editInvoiceModal) {
                closeEditInvoiceModal();
            }
        }
    </script>

    <div id="profile-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeProfileModal()">&times;</span>
            <h1>Profile Details</h1>
            <table class="profile-table">
                <tr><td colspan="2"><img class="profile-pic-large" src="<?php echo $profileImagePath; ?>" alt="Profile Picture"></td></tr>
                <tr><th>User ID</th><td><?php echo $user['User_id']; ?></td></tr>
                <tr><th>Name</th><td><?php echo $user['Name']; ?></td></tr>
                <tr><th>Email</th><td><?php echo $user['Email']; ?></td></tr>
                <tr><th>User Type</th><td><?php echo $role; ?></td></tr>
            </table>
        </div>
    </div>

    <div id="edit-profile-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeEditProfileModal()">&times;</span>
            <h1>Edit Profile</h1>
            <form class="edit-profile-form" method="POST" action="update_profile.php" enctype="multipart/form-data">
                <input type="file" name="profilePic" accept="image/*">
                <input type="text" name="name" placeholder="Name" value="<?php echo $user['Name']; ?>" required> 
                <input type="password" name="currentPassword" placeholder="Current Password" >
                <input type="email" name="email" placeholder="Email" value="<?php echo $user['Email']; ?>" required>
                <input type="password" name="newPassword" placeholder="New Password" >
                <input type="password" name="confirmPassword" placeholder="Confirm New Password" >

                <button type="submit" name="updateProfile">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        let dropdownMenu = document.getElementById('dropdown-menu');
        let profileModal = document.getElementById('profile-modal');
        let editProfileModal = document.getElementById('edit-profile-modal');

        function toggleMenu() {
            dropdownMenu.classList.toggle('show');
        }

        function openProfileModal() {
            profileModal.style.display = 'flex';
        }

        function closeProfileModal() {
            profileModal.style.display = 'none';
        }

        function openEditProfileModal() {
            editProfileModal.style.display = 'flex';
        }

        function closeEditProfileModal() {
            editProfileModal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == profileModal) {
                closeProfileModal();
            }
            if (event.target == editProfileModal) {
                closeEditProfileModal();
            }
        }
    </script>
</body>
</html>
