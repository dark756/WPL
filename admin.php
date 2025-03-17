<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$message = "";
$result = null;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['query']) && !empty(trim($_POST['query']))) {
    $query = trim($_POST['query']);

    if (stripos($query, "DROP") !== false || stripos($query, "DELETE") !== false) {
        $message = "Dangerous queries are not allowed!";
    } else {
        $result = $conn->query($query);

        if (!$result) {
            $message = "Error: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 600px;
            text-align: center;
        }
        textarea {
            width: 95%;
            height: 100px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            resize: none;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }
        input[type="submit"]:hover {
            background: #0056b3;
        }
        .message {
            color: red;
            font-weight: bold;
        }
        .result-container {
            margin-top: 20px;
            text-align: center;
            width: 100%;
        }
        table {
            border-collapse: collapse;
            margin: 0 auto;
            width: auto;
        }
        table, th, td {
            border: 1px solid black;
            padding: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin SQL Query Panel</h2>
        <?php if (!empty($message)) echo "<p class='message'>$message</p>"; ?>
        <form method="POST">
            <textarea name="query"><?php echo isset($_POST['query']) ? htmlspecialchars($_POST['query']) : "SELECT * FROM ADMIN;"; ?></textarea>
            <input type="submit" value="Execute Query">
        </form>
    </div>

    <?php if ($result && $result instanceof mysqli_result): ?>
        <div class="result-container">
            <table>
                <tr>
                    <?php while ($field = $result->fetch_field()): ?>
                        <th><?php echo htmlspecialchars($field->name); ?></th>
                    <?php endwhile; ?>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <?php foreach ($row as $col): ?>
                            <td><?php echo htmlspecialchars($col); ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endwhile; ?>
            </table>
        </div>
    <?php endif; ?>
</body>
</html>
