<?php
include 'dbconnect.php';
session_start();

$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
$userID = isset($_SESSION['UserID']) ? $_SESSION['UserID'] : '';
$email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$memname = isset($_SESSION['member_name']) ? $_SESSION['member_name'] : '';
$profileImagePath = 'uploads/admin.jpg';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($role === 'Member') {
    $sql_invoice_check = "SELECT Due FROM Invoice WHERE Member_id = '$userID' AND Status = 'Pending'";
    $result_invoice_check = $conn->query($sql_invoice_check);

    if ($result_invoice_check && $result_invoice_check->num_rows > 0) {
        $invoice = $result_invoice_check->fetch_assoc();
        $notification = "You have a loan payment due this " . $invoice['Due'] . ".";
    }
}

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
            margin: 40px auto;
            text-align: center;
        }

        .content h2 {
            color: #333;
            margin-bottom: 15px;
        }

        .content p {
            color: #555;
            font-size: 16px;
            line-height: 1.5;
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

        .modal-content h2 {
            margin-bottom: 20px;
            color: #333;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 20px;
            cursor: pointer;
            color: #333;
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

        .notification {
            background-color: red;
            color: white;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            font-size: 20px;
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
                if (isset($role) && $role === 'Member') {
            ?>
                <a href="memtransac_history.php">Transactions</a>
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
        <?php if (!empty($notification)) { ?>
            <div class="notification">
                <label><?php echo $notification; ?></label>
            </div>
        <?php } ?>

        <h2 style = "font-size: 40px;">Welcome to the Homepage</h2>
        <p style = "font-size: 20px;">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
        </p>
    </div>

    <div id="profile-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeProfileModal()">&times;</span>
            <h2>Profile Details</h2>
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
            <h2>Edit Profile</h2>
            <form class="edit-profile-form" method="POST" action="update_profile.php" enctype="multipart/form-data">
                <input type="text" name="name" placeholder="Name" value="<?php echo $user['Name']; ?>" required>
                <input type="email" name="email" placeholder="Email" value="<?php echo $user['Email']; ?>" required>
                <input type="file" name="profilePic" accept="image/*">
                <input type="password" name="currentPassword" placeholder="Current Password" >
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
