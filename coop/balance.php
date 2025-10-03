<?php
    include 'dbconnect.php';
    session_start();

    $role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
    $userID = isset($_SESSION['UserID']) ? $_SESSION['UserID'] : '';
    $profileImagePath = 'uploads/admin.jpg';

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    $balance = 0;

    if ($userID && $role) {
        if ($role === 'Administrator') {
            $sql = "SELECT * FROM Administrator WHERE User_id = ?";
        } elseif ($role === 'Accountant') {
            $sql = "SELECT * FROM Accountant WHERE User_id = ?";
        } elseif ($role === 'Member') {
            $sql = "SELECT * FROM Member WHERE User_id = ?";
        } else {
            echo "Invalid role or table.";
        }

        if (!empty($sql)) {
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $userID);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $profileImagePath = isset($user['ProfilePic']) && !empty($user['ProfilePic']) ? $user['ProfilePic'] : 'uploads/admin.jpg';
                if ($role === 'Member') {
                    $balance = isset($user['Balance']) ? $user['Balance'] : 0;
                    $loan = isset($user['Loan']) ? $user['Loan'] : 0;
                    $topay = isset($user['Topay']) ? $user['Topay'] : 0;
                }
            }
        }
    }

    $loanExists = false; 
    $loanBalance = 0;
    $monthlyPayment = 0;

    if ($userID && $role === 'Member') {
        $loanQuery = "SELECT Amount, MonthlyPay FROM Loans WHERE Member_id = '$userID' AND Status = 'Active'";
        $loanResult = mysqli_query($conn, $loanQuery);

        if ($loanResult && mysqli_num_rows($loanResult) > 0) {
            $loanData = mysqli_fetch_assoc($loanResult);
            $loanExists = true;
            $loanBalance = $loanData['Amount'];
            $monthlyPayment = $loanData['MonthlyPay'];
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Homepage</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .center-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
            text-align: center;
        }

        .content {
            display: inline-block;
            background: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            max-width: 700px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .content h2 {
            color: #333;
            margin-bottom: 15px;
        }

        .balance-message {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }

        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 30px;
        }

        .action-buttons button {
            padding: 10px 20px;
            font-size: 24px;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .action-buttons button:hover {
            background-color: #0056b3;
        }

        .profile {
            position: relative;
        }

        .profile-btn {
            background: none;
            border: none;
            cursor: pointer;
        }

        .profile-btn img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 50px;
            right: 0;
            background-color: white;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
            overflow: hidden;
            z-index: 1000;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-menu a {
            display: block;
            padding: 10px 20px;
            color: #333;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .dropdown-menu a:hover {
            background-color: #f0f0f0;
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
                    echo '<a href="pendingloans.php">Loans</a>';
                }
            ?>
            <?php 
                if (isset($role) && $role === 'Member') {
            ?>
                <a href="memtransac_history.php">Transactions</a>
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

    <div class="center-container">
        <div class="content">
            <label style="font-size: 45px;"><b>Welcome, <?php echo $user['Name'] ?? 'Member'; ?></b></label>
            <p class="balance-message" style="font-size: 45px;">
                Your current balance is Php <?php echo number_format($balance, 2); ?>
            </p>

            <div class="action-buttons">
                <button onclick="openDepositModal()">Deposit</button>
                <button onclick="openWithdrawModal()">Withdraw</button>
                <?php if (!$loanExists) { ?>
                    <button onclick="openGetLoanModal()">Get a Loan</button>
                <?php } ?>
            </div>

            <?php if ($loanExists) { ?>
                <p class="balance-message" style="font-size: 45px;">
                    <br>Your Loan balance is <br> Php <?php echo number_format($loanBalance, 2); ?>
                </p>
                <p class="balance-message" style="font-size: 45px;">
                    Your monthly payment is <br> Php <?php echo number_format($monthlyPayment, 2); ?>
                </p>
                <?php
                    $sql = "SELECT COUNT(*) AS count FROM Invoice WHERE Member_id = $userID AND Status = 'Pending'";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($result);
                    $exist = $row['count'] > 0;
                ?>
                <div class="action-buttons">
                    <?php if ($exist) { ?>
                        <button onclick="openPayLoanModal()">Pay Loan</button>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>

    <div id="pay-loan-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closePayLoanModal()">&times;</span>
            <h2>Pay Loan</h2>
            <form method="POST" action="pay_loan.php">
                <p>Pay your monthly loan installment of Php <strong><?php echo number_format($monthlyPayment, 2); ?></strong></p>
                <button type="submit" name="payLoan">Confirm Payment</button>
            </form>
        </div>
    </div>

    <script>
        let payLoanModal = document.getElementById('pay-loan-modal');

        function openPayLoanModal() {
            payLoanModal.style.display = 'flex';
        }

        function closePayLoanModal() {
            payLoanModal.style.display = 'none';
        }
    </script>

    <div id="get-loan-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeGetLoanModal()">&times;</span>
            <h2>Get a Loan</h2>
            <form method="POST" action="get_loan.php" style="display: flex; flex-direction: column; gap: 15px; align-items: center;">
                <input type="number" name="amount" placeholder="Enter loan amount" min="1" step="0.01" required 
                    style="width: 80%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                
                <label for="months" style="width: 80%; text-align: left; font-size: 14px; color: #555;">Select Repayment Period:</label>
                <select name="months" required 
                    style="width: 80%; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    <option value="6">6 months</option>
                    <option value="12">12 months</option>
                    <option value="24">24 months</option>
                    <option value="36">36 months</option>
                </select>
                <button type="submit" name="applyLoan" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.3s ease;">Apply for Loan</button>
            </form>
        </div>
    </div>

    <script>
        let getLoanModal = document.getElementById('get-loan-modal');

        function openGetLoanModal() {
            getLoanModal.style.display = 'flex';
        }

        function closeGetLoanModal() {
            getLoanModal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == getLoanModal) {
                closeGetLoanModal();
            }
        };
    </script>

    <div id="deposit-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeDepositModal()">&times;</span>
            <h2>Deposit</h2>
            <form method="POST" action="deposit.php">
                <input type="number" name="amount" placeholder="Enter amount" min="1" step="0.01" required>
                <button type="submit" name="deposit">Confirm Deposit</button>
            </form>
        </div>
    </div>

    <div id="withdraw-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeWithdrawModal()">&times;</span>
            <h2>Withdraw</h2>
            <form method="POST" action="withdraw.php">
                <input type="number" name="amount" placeholder="Enter amount" min="1" step="0.01" required>
                <button type="submit" name="withdraw">Confirm Withdraw</button>
            </form>
        </div>
    </div>
    <script>
        let depositModal = document.getElementById('deposit-modal');
        let withdrawModal = document.getElementById('withdraw-modal');

        function openDepositModal() {
            depositModal.style.display = 'flex';
        }

        function closeDepositModal() {
            depositModal.style.display = 'none';
        }

        function openWithdrawModal() {
            withdrawModal.style.display = 'flex';
        }

        function closeWithdrawModal() {
            withdrawModal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target === depositModal) {
                closeDepositModal();
            }
            if (event.target === withdrawModal) {
                closeWithdrawModal();
            }
        };
    </script>

    <script>
        let getLoanModal = document.getElementById('get-loan-modal');

        function openGetLoanModal() {
            getLoanModal.style.display = 'flex';
        }

        function closeGetLoanModal() {
            getLoanModal.style.display = 'none'; 
        }

        window.onclick = function(event) {
            if (event.target === getLoanModal) {
                closeGetLoanModal();
            }
        };


    </script>


    <div id="profile-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeProfileModal()">&times;</span>
            <h2>Profile Details</h2>
            <table class="profile-table">
                <tr>
                    <td colspan="2">
                        <img class="profile-pic-large" src="<?php echo $profileImagePath; ?>" alt="Profile Picture">
                    </td>
                </tr>
                <tr><th>User ID</th><td><?php echo $user['User_id'] ?? 'N/A'; ?></td></tr>
                <tr><th>Name</th><td><?php echo $user['Name'] ?? 'N/A'; ?></td></tr>
                <tr><th>Email</th><td><?php echo $user['Email'] ?? 'N/A'; ?></td></tr>
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
