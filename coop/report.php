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
    }
    else {
        echo "Invalid role or table.";
    }

    $outstandingReceivablesQuery = "SELECT Member_Name, SUM(Amount) AS OutstandingAmount FROM Transactions WHERE Transaction_Type IN ('Loan Payment') AND Status IN ('Completed') GROUP BY Member_Name";
    $outstandingReceivablesResult = $conn->query($outstandingReceivablesQuery);

    $totalOutstandingQuery = "SELECT SUM(Amount) AS TotalOutstanding FROM Transactions WHERE Transaction_Type IN ('Loan Payment') AND Status IN ('Completed')";
    $totalOutstandingResult = $conn->query($totalOutstandingQuery);
    $totalOutstanding = ($totalOutstandingResult->num_rows > 0) ? $totalOutstandingResult->fetch_assoc()['TotalOutstanding'] : 0;
}

if ($userID && $role) {
    $summaries = [
        'monthly' => [],
        'quarterly' => [],
        'annual' => []
    ];

    $monthlyQuery = "SELECT DATE_FORMAT(Date, '%Y-%m') AS Month, Transaction_Type, SUM(Amount) AS TotalAmount
                     FROM Transactions
                     GROUP BY Month, Transaction_Type
                     ORDER BY Month DESC";
    $monthlyResult = $conn->query($monthlyQuery);
    while ($row = $monthlyResult->fetch_assoc()) {
        $summaries['monthly'][] = $row;
    }

    $quarterlyQuery = "SELECT CONCAT(YEAR(Date), '-Q', QUARTER(Date)) AS Quarter, Transaction_Type, SUM(Amount) AS TotalAmount
                        FROM Transactions
                        GROUP BY Quarter, Transaction_Type
                        ORDER BY Quarter DESC";
    $quarterlyResult = $conn->query($quarterlyQuery);
    while ($row = $quarterlyResult->fetch_assoc()) {
        $summaries['quarterly'][] = $row;
    }

    $annualQuery = "SELECT YEAR(Date) AS Year, Transaction_Type, SUM(Amount) AS TotalAmount
                     FROM Transactions
                     GROUP BY Year, Transaction_Type
                     ORDER BY Year DESC";
    $annualResult = $conn->query($annualQuery);
    while ($row = $annualResult->fetch_assoc()) {
        $summaries['annual'][] = $row;
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
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            text-align: left;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }

        th {
            background-color: #333;
            color: white;
        }

        td {
            font-size: 16px;
        }

        .summary-section {
            margin: 20px auto;
            padding: 20px;
            width: 90%;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .summary-section h2 {
            text-align: center;
            color: #333;
        }

        .report-section {
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 90%;
        }

        .report-title, .totalreport {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
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

    <div class="report-section">
        <h1 class="report-title">Outstanding Receivables by Member</h1>
        <table>
            <tr>
                <th>Member Name</th>
                <th>Amount</th>
            </tr>
            <?php while ($row = $outstandingReceivablesResult->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['Member_Name']; ?></td>
                    <td><?php echo number_format($row['OutstandingAmount'], 2); ?></td>
                </tr>
            <?php } ?>
        </table>
        <h2 class="totalreport" style = "font-size: 35px;"><br>Total Receivables: <?php echo number_format($totalOutstanding, 2); ?></h2>
    </div>

    <div class="summary-section">
        <h2>Monthly Financial Summary</h2>
        <table class="summary-table">
            <tr>
                <th>Month</th>
                <th>Transaction Type</th>
                <th>Total Amount</th>
            </tr>
            <?php foreach ($summaries['monthly'] as $row) { ?>
                <tr>
                    <td><?php echo $row['Month']; ?></td>
                    <td><?php echo $row['Transaction_Type']; ?></td>
                    <td><?php echo number_format($row['TotalAmount'], 2); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <div class="summary-section">
        <h2>Quarterly Financial Summary</h2>
        <table class="summary-table">
            <tr>
                <th>Quarter</th>
                <th>Transaction Type</th>
                <th>Total Amount</th>
            </tr>
            <?php foreach ($summaries['quarterly'] as $row) { ?>
                <tr>
                    <td><?php echo $row['Quarter']; ?></td>
                    <td><?php echo $row['Transaction_Type']; ?></td>
                    <td><?php echo number_format($row['TotalAmount'], 2); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <div class="summary-section">
        <h2>Annual Financial Summary</h2>
        <table class="summary-table">
            <tr>
                <th>Year</th>
                <th>Transaction Type</th>
                <th>Total Amount</th>
            </tr>
            <?php foreach ($summaries['annual'] as $row) { ?>
                <tr>
                    <td><?php echo $row['Year']; ?></td>
                    <td><?php echo $row['Transaction_Type']; ?></td>
                    <td><?php echo number_format($row['TotalAmount'], 2); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

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
