<?php
session_start();
include 'db.php';

if (!isset($_SESSION['userid'])) {
    header("Location: index.php");
    exit;
}

$userid   = $_SESSION['userid'];
$pid      = intval($_POST['pid'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 0);

if ($pid < 1 || $quantity < 1) {
    die("Invalid product or quantity.");
}

$sql = "INSERT INTO sales (pid, userid, quantity) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $pid, $userid, $quantity);

if ($stmt->execute()) {
    header("Location: dashboard.php?purchase=success");
} else {
    header("Location: dashboard.php?purchase=error");
}
$stmt->close();
?>
