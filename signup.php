<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $gender = trim($_POST['gender']);
    $dob = trim($_POST['dob']);

    if (empty($username) || empty($password) || empty($email) || empty($name) || empty($address) || empty($gender) || empty($dob)) {
        die("All fields are required! <a href='signup.html'>Try again</a>");
    }

    $dob_date = new DateTime($dob);
    $today = new DateTime();
    $age = $today->diff($dob_date)->y;

    if ($age < 16) {
        die("You must be at least 16 years old to sign up. <a href='signup.html'>Try again</a>");
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $check_query = "SELECT userid FROM Users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($check_query);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $check_result = $stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "Username or Email already exists! <a href='signup.html'>Try again</a>";
    } else {
        $sql = "INSERT INTO Users (username, passwd, email, name, address, gender, dob) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $username, $hashed_password, $email, $name, $address, $gender, $dob);

        if ($stmt->execute()) {
            echo "Registration successful! <a href='index.php'>Login here</a>";
        } else {
            echo "Error: " . $conn->error;
        }
    }

    $stmt->close();
    $conn->close();
}
?>
