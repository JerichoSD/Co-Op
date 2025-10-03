-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql103.byetcluster.com
-- Generation Time: Dec 12, 2024 at 02:00 AM
-- Server version: 10.6.19-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_37716369_Coop`
--

-- --------------------------------------------------------

--
-- Table structure for table `Accountant`
--

CREATE TABLE `Accountant` (
  `User_id` int(6) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(20) NOT NULL,
  `ProfilePic` varchar(30) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `Accountant`
--

INSERT INTO `Accountant` (`User_id`, `Name`, `Email`, `Password`, `ProfilePic`) VALUES
(1, 'Accountant A. Accountant', 'accountant@gmail.com', '123', 'uploads/admin.jpg'),
(3, 'James Karl Silpao', 'silpao@gmail.com', '123', 'uploads/jamespic.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `Administrator`
--

CREATE TABLE `Administrator` (
  `User_id` int(5) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `ProfilePic` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `Administrator`
--

INSERT INTO `Administrator` (`User_id`, `Name`, `Email`, `Password`, `ProfilePic`) VALUES
(1, 'Admin A. Administrator', 'admin@gmail.com', '1234', 'uploads/admin.jpg'),
(2, 'Practice', 'prac@gmail.com', '1234', 'uploads/bsulogo.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `Invoice`
--

CREATE TABLE `Invoice` (
  `Invoice_id` int(10) NOT NULL,
  `Member_id` int(10) NOT NULL,
  `Pay` decimal(10,2) NOT NULL,
  `Due` date NOT NULL,
  `Status` varchar(20) NOT NULL,
  `OverdueEmail` varchar(3) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `Invoice`
--

INSERT INTO `Invoice` (`Invoice_id`, `Member_id`, `Pay`, `Due`, `Status`, `OverdueEmail`) VALUES
(5, 1, '917.00', '2024-12-30', 'Pending', 'No'),
(7, 5, '1833.33', '2024-12-10', 'Paid', ''),
(8, 6, '916.67', '2024-12-10', 'Paid', ''),
(10, 4, '1833.00', '2024-12-30', 'Overdue', 'Yes');

-- --------------------------------------------------------

--
-- Table structure for table `Loans`
--

CREATE TABLE `Loans` (
  `Loan_id` int(11) NOT NULL,
  `Accountant_id` int(6) NOT NULL,
  `Member_id` int(6) NOT NULL,
  `Amount` decimal(10,2) DEFAULT NULL,
  `Months` int(11) NOT NULL,
  `MonthlyPay` decimal(10,2) DEFAULT NULL,
  `AlreadyPaid` decimal(10,2) DEFAULT NULL,
  `PaymentStart` date NOT NULL,
  `PaymentEnd` date NOT NULL,
  `Status` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `Loans`
--

INSERT INTO `Loans` (`Loan_id`, `Accountant_id`, `Member_id`, `Amount`, `Months`, `MonthlyPay`, `AlreadyPaid`, `PaymentStart`, `PaymentEnd`, `Status`) VALUES
(2, 1, 3, '10083.00', 12, '917.00', '917.00', '2024-12-30', '2025-11-30', 'Active'),
(3, 1, 1, '22000.00', 24, '917.00', '0.00', '2024-12-30', '2026-11-30', 'Active'),
(5, 3, 5, '5500.01', 6, '1833.33', '5499.99', '2025-01-10', '2025-06-10', 'Active'),
(6, 3, 6, '10083.33', 12, '916.67', '916.67', '2025-01-10', '2025-12-10', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `Member`
--

CREATE TABLE `Member` (
  `User_id` int(5) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Password` varchar(20) NOT NULL,
  `ProfilePic` varchar(30) NOT NULL,
  `Balance` decimal(10,2) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `Member`
--

INSERT INTO `Member` (`User_id`, `Name`, `Email`, `Password`, `ProfilePic`, `Balance`) VALUES
(1, 'Member M. Member', 'member@gmail.com', 'password', 'uploads/admin.jpg', '800.00'),
(3, 'Krystian Alcantara', 'alcantara@gmail.com', '123', 'uploads/krystianpic.png', '20849.00'),
(5, 'Cat', 'cat@gmail.com', '123', 'uploads/cat.jpg', '166.67'),
(6, 'dog', 'dog@gmail.com', '123', 'uploads/dog.jpg', '2083.33');

-- --------------------------------------------------------

--
-- Table structure for table `Transactions`
--

CREATE TABLE `Transactions` (
  `Transaction_id` int(11) NOT NULL,
  `Accountant_Name` varchar(100) NOT NULL,
  `Member_Name` varchar(100) NOT NULL,
  `Transaction_Type` varchar(20) NOT NULL,
  `Amount` decimal(10,2) DEFAULT NULL,
  `Date` varchar(20) NOT NULL,
  `Status` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `Transactions`
--

INSERT INTO `Transactions` (`Transaction_id`, `Accountant_Name`, `Member_Name`, `Transaction_Type`, `Amount`, `Date`, `Status`) VALUES
(1, 'Accountant A. Accountant', 'Krystian Alcantara', 'Loan', '11000.00', '2024-11-30', 'Completed'),
(2, 'Accountant A. Accountant', 'Member M. Member', 'Withdraw', '400.00', '2024-11-30', 'Completed'),
(3, 'Accountant A. Accountant', 'Member M. Member', 'Loan', '22000.00', '2024-11-30', 'Active'),
(8, 'James Karl Silpao', 'Krystian Alcantara', 'Deposit', '1000.00', '2024-12-08', 'Completed'),
(10, 'James Karl Silpao', 'Krystian Alcantara', 'Deposit', '2000.00', '2024-12-08', 'Completed'),
(11, 'James Karl Silpao', 'Krystian Alcantara', 'Loan Payment', '917.00', '2024-12-08', 'Completed'),
(12, 'James Karl Silpao', 'Krystian Alcantara', 'Loan Payment', '917.00', '2024-12-08', 'Completed'),
(13, 'James Karl Silpao', 'Krystian Alcantara', 'Deposit', '20000.00', '2024-12-10', 'Completed'),
(15, 'James Karl Silpao', 'Cat', 'Deposit', '2000.00', '2024-12-10', 'Completed'),
(16, 'James Karl Silpao', 'Cat', 'Loan', '11000.00', '2024-12-10', 'Active'),
(17, 'James Karl Silpao', 'Cat', 'Loan Payment', '1833.33', '2024-12-10', 'Completed'),
(23, 'James Karl Silpao', 'dog', 'Withdraw', '1000.00', '2024-12-10', 'Completed'),
(22, 'James Karl Silpao', 'dog', 'Loan', '11000.00', '2024-12-10', 'Active'),
(21, 'James Karl Silpao', 'dog', 'Deposit', '4000.00', '2024-12-10', 'Completed'),
(24, 'James Karl Silpao', 'dog', 'Loan Payment', '916.67', '2024-12-10', 'Completed');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Accountant`
--
ALTER TABLE `Accountant`
  ADD PRIMARY KEY (`User_id`);

--
-- Indexes for table `Administrator`
--
ALTER TABLE `Administrator`
  ADD PRIMARY KEY (`User_id`);

--
-- Indexes for table `Invoice`
--
ALTER TABLE `Invoice`
  ADD PRIMARY KEY (`Invoice_id`);

--
-- Indexes for table `Loans`
--
ALTER TABLE `Loans`
  ADD PRIMARY KEY (`Loan_id`);

--
-- Indexes for table `Member`
--
ALTER TABLE `Member`
  ADD PRIMARY KEY (`User_id`);

--
-- Indexes for table `Transactions`
--
ALTER TABLE `Transactions`
  ADD PRIMARY KEY (`Transaction_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Accountant`
--
ALTER TABLE `Accountant`
  MODIFY `User_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Administrator`
--
ALTER TABLE `Administrator`
  MODIFY `User_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `Invoice`
--
ALTER TABLE `Invoice`
  MODIFY `Invoice_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `Loans`
--
ALTER TABLE `Loans`
  MODIFY `Loan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `Member`
--
ALTER TABLE `Member`
  MODIFY `User_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `Transactions`
--
ALTER TABLE `Transactions`
  MODIFY `Transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
