<?php
session_start();
include 'db.php';

if (!isset($_SESSION['userid'])) {
    header("Location: index.php");
    exit();
}

$userid = $_SESSION['userid'];
$sql = "SELECT name, username FROM Users WHERE userid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$products = [
    ["name" => "Stylish Watch", "price" => 8500, "image" => "1.png"],  
    ["name" => "Running Shoes", "price" => 6500, "image" => "2.png"],  
    ["name" => "Wireless Headphones", "price" => 4000, "image" => "3.png"],  
    ["name" => "Gaming Laptop", "price" => 108000, "image" => "4.png"],  
    ["name" => "Smartphone", "price" => 58000, "image" => "5.png"],  
    ["name" => "DSLR Camera", "price" => 42000, "image" => "6.png"]  
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        body {
            background-color: #333;
            color: white;
        }
        .header {
            background: #222;
            padding: 15px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
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
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
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
    </style>
</head>
<body>

    <div class="header">
        <div class="user-info">

            Welcome <a href="profile.php?userid=<?= $userid; ?>"><?= htmlspecialchars($user['name']); ?></a>
              (<?= htmlspecialchars($user['username']); ?>)  
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>Featured Products</h2>
        <div class="products">
            <?php foreach ($products as $product) { ?>
                <div class="product">
                    <img src="<?= htmlspecialchars($product['image']); ?>" alt="Product Image">
                    <h3><?= htmlspecialchars($product['name']); ?></h3>
                    <p>₹<?= number_format($product['price'], 2); ?></p>
                    <div class="quantity">
                        <button onclick="decreaseQty(this)">-</button>
                        <input type="text" value="1" readonly>
                        <button onclick="increaseQty(this)">+</button>
                    </div>
                    <button class="buy-now" onclick="confirmPurchase('<?= htmlspecialchars($product['name']); ?>')">Buy Now</button>
                </div>
            <?php } ?>
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

        function confirmPurchase(productName) {
            if (confirm("Are you sure you want to buy " + productName + "?")) {
                alert("Purchase confirmed!");
            }
        }
    </script>
</body>
</html>