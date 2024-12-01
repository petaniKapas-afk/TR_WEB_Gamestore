<?php
session_start();
require 'koneksi.php';

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Ambil data admin dari session
$admin_id = $_SESSION['admin_id'];

// Query untuk mengambil daftar game
$query_games = "SELECT * FROM games";
$games_result = $conn->query($query_games);

// Query untuk mengambil daftar pembayaran yang belum diverifikasi
$query_payments = "SELECT * FROM payments WHERE verified = 0";
$payments_result = $conn->query($query_payments);

// Query untuk mengambil riwayat transaksi semua user
$query_transactions = "SELECT * FROM transactions";
$transactions_result = $conn->query($query_transactions);

// Periksa apakah form ditambahkan atau diedit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['title'], $_POST['price'], $_POST['description'], $_POST['stock'])) {
        // Pastikan semua data ada
        $title = $_POST['title'];
        $price = $_POST['price'];
        $description = $_POST['description'];
        $stock = $_POST['stock'];

        // Query untuk menambah game baru
        $insert_query = "INSERT INTO games (title, description, price, stock) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ssdi", $title, $description, $price, $stock);  // "ssdi" untuk string, string, decimal, integer

        if ($stmt->execute()) {
            echo "Game added successfully!";
        } else {
            echo "Error adding game: " . $stmt->error;
        }
    } else {
        echo "Missing game information!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7f6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
        }

        nav {
            background-color: #444;
            padding: 10px;
        }

        nav ul {
            list-style-type: none;
            padding: 0;
            text-align: center;
        }

        nav ul li {
            display: inline;
            margin: 0 15px;
        }

        nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        nav ul li a:hover {
            text-decoration: underline;
        }

        h1, h2 {
            color: #444;
        }

        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #f4f4f4;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
    </style>
</head>
<body>

<header>
    <h1>Welcome Admin</h1>
</header>

<nav>
    <ul>
        <li><a href="add_game.php">Add New Game</a></li>
        <li><a href="verify_payments.php">Verify Payments</a></li>
        <li><a href="transaction_history.php">Transaction History</a></li>
        <li><a href="discount_event.php">Create Discount Event</a></li>
    </ul>
</nav>

<main>
    <h2>Game List</h2>
    <table>
        <tr>
            <th>Game ID</th>
            <th>Game Title</th>
            <th>Price</th>
            <th>Actions</th>
        </tr>
        <?php while ($game = $games_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $game['id']; ?></td>
                <td><?php echo $game['title']; ?></td>
                <td><?php echo $game['price']; ?></td>
                <td>
                    <a href="edit_game.php?id=<?php echo $game['id']; ?>" class="btn">Edit</a>
                    <a href="delete_game.php?id=<?php echo $game['id']; ?>" class="btn" style="background-color: red;">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </table>

    <h2>Pending Payments</h2>
    <table>
        <tr>
            <th>Payment ID</th>
            <th>User ID</th>
            <th>Amount</th>
            <th>Payment Method</th>
            <th>Actions</th>
        </tr>
        <?php while ($payment = $payments_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $payment['id']; ?></td>
                <td><?php echo $payment['user_id']; ?></td>
                <td><?php echo $payment['amount']; ?></td>
                <td><?php echo $payment['payment_method']; ?></td>
                <td>
                    <a href="verify_payment_action.php?id=<?php echo $payment['id']; ?>" class="btn">Verify</a>
                </td>
            </tr>
        <?php } ?>
    </table>

    <h2>Transaction History</h2>
    <table>
        <tr>
            <th>Transaction ID</th>
            <th>User ID</th>
            <th>Game ID</th>
            <th>Amount</th>
            <th>Payment Date</th>
        </tr>
        <?php while ($transaction = $transactions_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $transaction['id']; ?></td>
                <td><?php echo $transaction['user_id']; ?></td>
                <td><?php echo $transaction['game_id']; ?></td>
                <td><?php echo $transaction['amount']; ?></td>
                <td><?php echo $transaction['transaction_date']; ?></td>
            </tr>
        <?php } ?>
    </table>
</main>

<footer>
    <a href="login.php" class="btn">Log Out</a>
</footer>

</body>
</html>
