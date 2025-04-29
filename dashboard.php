<?php
session_start();
include 'db.php';

if (!isset($_SESSION['userid'])) {
    header("Location: index.php");
    exit();
}

$userid = $_SESSION['userid'];
// Fetch user info
$sql = "SELECT name, username FROM Users WHERE userid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch products from DB
$products = [];
$sql = "SELECT pid, pname, price FROM products";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        body {
            background-color: #333;
            color: white;
            font-family: Arial, sans-serif;
            margin: 0;
        }
        .header {
            background: #222;
            padding: 15px;
            display: flex;
            justify-content: flex-end;
        }
        .user-info {
            font-size: 18px;
        }
        .user-info a {
            color: lightblue;
            margin-left: 10px;
            text-decoration: none;
        }
        .container {
            width: 90%;
            margin: auto;
            padding: 20px 0;
        }
        h2 {
            background: #444;
            padding: 10px;
            text-align: center;
        }
        .products {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .product {
            background: #555;
            width: 30%;
            margin-bottom: 20px;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
        }
        .product img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 8px;
        }
        .product h3 {
            margin: 15px 0;
        }
        .product p {
            font-size: 20px;
            font-weight: bold;
            color: #28a745;
        }
        .quantity {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 10px;
        }
        .quantity button {
            background: #007bff;
            color: white;
            border: none;
            padding: 7px 12px;
            font-size: 18px;
            cursor: pointer;
            border-radius: 5px;
        }
        .quantity input {
            width: 40px;
            text-align: center;
            font-size: 16px;
            border: 1px solid #ddd;
            margin: 0 5px;
            border-radius: 5px;
            background: #eee;
        }
        .product button.buy-now {
            background: #ff5722;
            color: white;
            border: none;
            padding: 12px 15px;
            margin-top: 15px;
            cursor: pointer;
            border-radius: 5px;
            display: block;
            width: 100%;
            font-size: 16px;
        }
        .product button.buy-now:hover {
            background: #e64a19;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        .modal-content {
            background: white;
            color: black;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            min-width: 300px;
        }
        .modal-content form {
            margin-top: 15px;
        }
        .modal-content button {
            padding: 8px 15px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-confirm {
            background: #28a745;
            color: white;
        }
        .btn-cancel {
            background: #ccc;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="user-info">
	Welcome <?= htmlspecialchars($user['name']) ?> (<a href="profile.php"><?=htmlspecialchars($user['username'])?></a> )
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Featured Products</h2>
    <div class="products">
        <?php foreach ($products as $product): ?>
            <div class="product">
                <img src="images/<?= $product['pid'] ?>.png" alt="<?= htmlspecialchars($product['pname']) ?>">
                <h3><?= htmlspecialchars($product['pname']) ?></h3>
                <p>₹<?= number_format($product['price']) ?></p>
                <div class="quantity">
                    <button onclick="decreaseQty(this)">-</button>
                    <input type="text" value="1" readonly>
                    <button onclick="increaseQty(this)">+</button>
                </div>
                <button class="buy-now" onclick="openModal(<?= $product['pid'] ?>, '<?= htmlspecialchars($product['pname']) ?>', <?= $product['price'] ?>, this)">Buy Now</button>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Modal -->
<div class="modal" id="purchaseModal">
    <div class="modal-content">
        <p id="modalText">Confirm purchase?</p>
        <form method="POST" action="purchase.php">
            <input type="hidden" name="pid" id="modalPid">
            <input type="hidden" name="quantity" id="modalQty">
            <button type="submit" class="btn-confirm">Confirm</button>
            <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
        </form>
    </div>
</div>

<script>
    function increaseQty(button) {
        let input = button.previousElementSibling;
        let currentValue = parseInt(input.value);
        input.value = currentValue + 1;
    }

    function decreaseQty(button) {
        let input = button.nextElementSibling;
        let currentValue = parseInt(input.value);
        if (currentValue > 1) {
            input.value = currentValue - 1;
        }
    }

    function openModal(pid, name, price, button) {
        const qty = parseInt(button.parentElement.querySelector('input').value);
        const total = qty * price;

        document.getElementById('modalPid').value = pid;
        document.getElementById('modalQty').value = qty;
        document.getElementById('modalText').textContent =
            `Buy "${name}" × ${qty} = ₹${total.toLocaleString()}?`;

        document.getElementById('purchaseModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('purchaseModal').style.display = 'none';
    }
</script>
</body>
</html>
