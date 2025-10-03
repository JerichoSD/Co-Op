<?php
include 'dbconnect.php';
session_start();

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
    } else {
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

        table {
            margin: 0 auto;
            background-color: antiquewhite;
        }

        td {
            font-size: 20px;
            text-align: center;
            padding: 10px;
        }

        th {
            font-size: 28px;
            text-align: center;
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

        table, th, tr, td {
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
        <label class="pagetitle">Member Accounts</label>
        <br>
        <?php
        if (isset($role) && $role === 'Administrator') { ?>
        <a href="user.php"><button style="text-decoration: none;" class="userbutton">To Accountants</button></a>
        <?php } ?>
        <?php
            $sql_member = "SELECT * FROM Member";
            $result_member = mysqli_query($conn, $sql_member);

            if (mysqli_num_rows($result_member) > 0) {
                ?>
                <h1>Members</h1>
                <table>
                    <th>User ID</th>
                    <th>Profile Pic</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th colspan="2">Actions</th>
                    <?php
                    while ($row = mysqli_fetch_array($result_member)) {
                        $userId = $row["User_id"];

                        $sql_loans = "SELECT * FROM Loans WHERE Member_id = '$userId' AND Status = 'Active'";
                        $result_loans = mysqli_query($conn, $sql_loans);

                        $showInvoiceButton = false;

                        if (mysqli_num_rows($result_loans) > 0) {
                            while ($loan = mysqli_fetch_array($result_loans)) {
                                $paymentStart = $loan['PaymentStart'];
                                $monthlyPay = $loan['MonthlyPay'];
                                $paymentDay = (new DateTime($paymentStart))->format('d');

                                $currentDay = date('d');
                                $currentMonth = date('Y-m');
                                $dueDate = "$currentMonth-$paymentDay";

                                $sql_check_invoice = "SELECT * FROM Invoice WHERE Member_id = '$userId' AND Due = '$dueDate'";
                                $result_check_invoice = mysqli_query($conn, $sql_check_invoice);

                                if (mysqli_num_rows($result_check_invoice) === 0) {
                                    $showInvoiceButton = true;

                                    if (isset($_POST['generateInvoice']) && $_POST['user_id'] == $userId) {
                                        $insertInvoice = "INSERT INTO Invoice (Member_id, Pay, Due) VALUES ('$userId', '$monthlyPay', '$dueDate')";
                                        if (mysqli_query($conn, $insertInvoice)) {
                                            echo "<script>alert('Invoice generated successfully!');</script>";
                                        } else {
                                            echo "<script>alert('Failed to generate invoice: " . mysqli_error($conn) . "');</script>";
                                        }
                                    }
                                    break;
                                }
                            }
                        }
                        ?>
                        <tr>
                            <td style="text-align: center;"><?php echo $userId; ?></td>
                            <td style="text-align: center;"><img src="<?php echo $row['ProfilePic']; ?>" alt="Pics" width="70" height="70"></td>
                            <td><?php echo $row["Name"]; ?></td>
                            <td><?php echo $row["Email"]; ?></td>
                            <td><button onclick="openEditMemberModal('<?php echo $userId; ?>', '<?php echo $row['Name']; ?>', '<?php echo $row['Email']; ?>', '<?php echo $row['ProfilePic']; ?>')">Edit</button></td>
                            <td><a href="delete_member.php?user_id=<?php echo $userId; ?>" onclick="return confirm('Are you sure you want to delete this user?');"><button>Delete</button></a></td>
                        </tr>
                <?php } ?>
                    <tr>
                        <td style="text-align: center;" colspan="6"><button onclick="openAddMemberModal()">Add New</button></td>
                    </tr>
                </table>
                <?php
            } else {
                echo "No members found.";
            }
            ?>
    </div>

    <div id="edit-member-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeEditMemberModal()">&times;</span>
            <h1>Edit Member</h1>
            <form class="edit-profile-form" method="POST" action="update_member.php" enctype="multipart/form-data">
                <input type="hidden" id="editMemberUserID" name="user_id" required>
                <input type="file" id="editMemberProfilePic" name="profilePic" accept="image/*">
                <input type="text" id="editMemberName" name="name" placeholder="Name" required>
                <input type="email" id="editMemberEmail" name="email" placeholder="Email" required>
                <input type="password" id="editMemberPassword" name="password" placeholder="Password">
                <input type="password" name="confirmPassword" placeholder="Confirm Password" required>
                <button type="submit" name="updateMember">Save Changes</button>
            </form>
        </div>
    </div>

    <div id="add-member-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeAddMemberModal()">&times;</span>
            <h1>Add New Member</h1>
            <form class="edit-profile-form" method="POST" action="add_member.php" enctype="multipart/form-data">
                <input type="file" name="profilePic" accept="image/*">
                <input type="text" name="name" placeholder="Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirmPassword" placeholder="Confirm Password" required>
                <button type="submit" name = "addMember">Add Member</button>
            </form>
        </div>
    </div>

    <script>
        function openEditMemberModal(userId, name, email, profilePic) {
            document.getElementById('editMemberUserID').value = userId;
            document.getElementById('editMemberName').value = name;
            document.getElementById('editMemberEmail').value = email;
            document.getElementById('edit-member-modal').style.display = 'flex';
        }

        function closeEditMemberModal() {
            document.getElementById('edit-member-modal').style.display = 'none';
        }

        function openAddMemberModal() {
            document.getElementById('add-member-modal').style.display = 'flex';
        }

        function closeAddMemberModal() {
            document.getElementById('add-member-modal').style.display = 'none';
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
            <input type="password" name="currentPassword" placeholder="Current Password">
            <input type="email" name="email" placeholder="Email" value="<?php echo $user['Email']; ?>" required>
            <input type="password" name="newPassword" placeholder="New Password">
            <input type="password" name="confirmPassword" placeholder="Confirm New Password">

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
