<?php
session_start();
include 'db.php';

// Check if the user is logged in by ensuring the session has the userid
if (!isset($_SESSION['userid'])) {
    echo "You must be logged in to view this page.";
    exit();
}

$userid = $_SESSION['userid']; // Get the logged-in user's ID from the session

// Fetch user details from the Users table
$sql = "SELECT * FROM Users WHERE userid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "User not found.";
    exit();
}

// Calculate current age from DOB
$dob = new DateTime($user['dob']);
$today = new DateTime();
$age = $today->diff($dob)->y;

// Get the purchase history of the user
$sql = "SELECT t.pname, t.quantity, t.total, t.time FROM transactions t WHERE t.userid = ? ORDER BY t.time DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userid);
$stmt->execute();
$purchaseHistory = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px;
        }
        .profile-container {
            background: #444;
            padding: 20px;
            border-radius: 10px;
            display: inline-block;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            margin-bottom: 30px;
        }
        h2 {
            color: #ff5722;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        th {
            background-color: #555;
        }
        a {
            color: red;
            text-decoration: none;
        }
        .back-link {
            margin-top: 20px;
            display: inline-block;
            padding: 10px 20px;
            background-color: #ff5722;
            color: white;
            border-radius: 5px;
        }
        .back-link:hover {
            background-color: #e64a19;
        }
    </style>
</head>
<body>

<div class="profile-container">
    <h2>User Profile</h2>
    <p><strong>Name:</strong> <?= htmlspecialchars($user['name']); ?></p>
    <p><strong>Username:</strong> <?= htmlspecialchars($user['username']); ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
    <p><strong>Address:</strong> <?= htmlspecialchars($user['address']); ?></p>
    <p><strong>Age:</strong> <?= $age; ?> years</p>
</div>

<div class="purchase-history-container">
    <h2>Purchase History</h2>
    <?php if ($purchaseHistory->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Purchase Time</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $purchaseHistory->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['pname']); ?></td>
                        <td><?= htmlspecialchars($row['quantity']); ?></td>
                        <td><?= number_format($row['total'], 2); ?></td>
                        <td><?= date('Y-m-d H:i:s', strtotime($row['time'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No purchase history found.</p>
    <?php endif; ?>
</div>

<a href="dashboard.php" class="back-link">Back to Dashboard</a>

</body>
</html>
