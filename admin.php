<?php
session_start();
include 'db.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$message = "";
$result  = null;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['query'])) {
    $query = trim($_POST['query']);

    if ($query === "") {
        $message = "Please enter a SQL query.";
    }
    elseif (stripos($query, "DROP") !== false || stripos($query, "DELETE") !== false) {
        $message = "Dangerous queries (DROP/DELETE) are not allowed!";
    }
    else {
        try {
            $result = $conn->query($query);
            if ($result instanceof mysqli_result && $result->num_rows === 0) {
                $message = "Query ran successfully but returned no rows.";
            }
        }
        catch (mysqli_sql_exception $ex) {
            $message = "Error executing query: " . htmlspecialchars($ex->getMessage());
            $result = null;
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" /><meta name="viewport" content="width=device-width,initial-scale=1.0"/>
  <title>Admin Panel</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      display: flex;
      flex-direction: column;
      align-items: center;
      background: #f4f4f4;
      padding: 20px;
    }
    .query-boxes {
      display: flex;
      justify-content: space-between;
      width: 34%;
      margin-bottom: 20px;
    }
    .query-box {
      background: #808080;
      color: white;
      padding: 15px 25px;
      font-size: 18px;
      cursor: pointer;
      border-radius: 0px; /* sharp corners */
      width: 24%;
      text-align: center;
      box-shadow: 0 0 5px rgba(0,0,0,0.2);
    }
    .query-box:hover {
      background: #666;
    }
    .container {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
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
    input[type=submit] {
      background: #007bff;
      color: white;
      padding: 10px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      width: 100%;
      margin-top: 10px;
    }
    input[type=submit]:hover { background: #0056b3; }
    .message { color: red; font-weight: bold; margin: 15px 0; }
    .result-container { margin-top: 20px; }
    table { border-collapse: collapse; margin: 0 auto; }
    th, td { border: 1px solid #000; padding: 8px; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Admin SQL Query Panel</h2>
    <?php if ($message): ?>
      <p class="message"><?= $message ?></p>
    <?php endif; ?>

    <form method="POST">
      <textarea name="query"><?= isset($_POST['query']) ? htmlspecialchars($_POST['query']) : "" ?></textarea>
      <input type="submit" value="Execute Query">
    </form>
  </div>
<div class="query-boxes">
    <div class="query-box" onclick="document.querySelector('textarea').value='SELECT * FROM transactions ORDER BY time DESC;'">Show Sales</div>
    <div class="query-box" onclick="document.querySelector('textarea').value='SELECT * FROM admin;'">Show Users</div>
<div class="query-box" onclick="document.querySelector('textarea').value='SELECT * FROM products;'">Show Inventory</div>
  </div>

  <?php if ($result instanceof mysqli_result): ?>
    <div class="result-container">
      <table>
        <tr>
          <?php foreach ($result->fetch_fields() as $f): ?>
            <th><?= htmlspecialchars($f->name) ?></th>
          <?php endforeach; ?>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <?php foreach ($row as $col): ?>
              <td><?= htmlspecialchars($col) ?></td>
            <?php endforeach; ?>
          </tr>
        <?php endwhile; ?>
      </table>
    </div>
  <?php endif; ?>
</body>
</html>
