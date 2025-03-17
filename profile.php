<?php
session_start();
include 'db.php';

if (!isset($_GET['userid'])) {
    echo "Invalid user.";
    exit();
}

$userid = intval($_GET['userid']);
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
        }
        h2 {
            color: #ff5722;
        }
        a {
            color: red;
            text-decoration: none;
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
    <a href="dashboard.php">Back to Dashboard</a>
</div>

</body>
</html>
