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
    if (isset($_POST['title'], $_POST['price'], $_POST['description'], $_POST['stock'], $_FILES['image'])) {
        // Pastikan semua data ada
        $title = $_POST['title'];
        $price = $_POST['price'];
        $description = $_POST['description'];
        $stock = $_POST['stock'];

        // Menangani upload gambar
        $image = $_FILES['image']['name'];
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_path = 'uploads/' . basename($image);

        // Pindahkan gambar ke folder uploads
        if (move_uploaded_file($image_tmp, $image_path)) {
            // Query untuk menambah game baru
            $insert_query = "INSERT INTO games (title, description, price, stock, image) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_query);
            $stmt->bind_param("ssdis", $title, $description, $price, $stock, $image);  // "ssdis" untuk string, string, decimal, integer, string

            if ($stmt->execute()) {
                echo "Game added successfully!";
            } else {
                echo "Error adding game: " . $stmt->error;
            }
        } else {
            echo "Error uploading image!";
        }
    } else {
        echo "Missing game information or image!";
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
        /* Basic reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7f6;
            color: #333;
            margin: 0;
            padding: 0;
            margin-bottom: 100px;
        }

        header {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
        }

        nav {
            background-color: #444;
            padding: 15px 0;
            margin-bottom: 20px;
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
            text-align: center;
            margin-bottom: 15px;
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
            color: #333;
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
            margin: 5px;
            font-weight: bold;
            text-align: center;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .btn-danger {
            background-color: red;
        }

        .btn-danger:hover {
            background-color: #cc0000;
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

        .game-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
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
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php while ($game = $games_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $game['id']; ?></td>
                <td><?php echo $game['title']; ?></td>
                <td><?php echo $game['price']; ?></td>
                <td>
                    <?php if ($game['image']) { ?>
                        <img src="uploads/<?php echo $game['image']; ?>" alt="Game Image" class="game-image">
                    <?php } else { ?>
                        No image available
                    <?php } ?>
                </td>
                <td>
                    <a href="edit_game.php?id=<?php echo $game['id']; ?>" class="btn">Edit</a>
                    <a href="delete_game.php?id=<?php echo $game['id']; ?>" class="btn btn-danger">Delete</a>
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
    <h2>Active Discount Events</h2>
    <table>
        <tr>
            <th>Event Name</th>
            <th>Discount Type</th>
            <th>Discount Value</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Actions</th>
        </tr>
        <?php
        $current_time = date('Y-m-d H:i:s');
        $events_result = $conn->query("SELECT * FROM discount_events WHERE end_date > '$current_time' ORDER BY start_date ASC");
        while ($event = $events_result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($event['event_name']) . '</td>';
            echo '<td>' . ucfirst($event['discount_type']) . '</td>';
            echo '<td>' . $event['discount_value'] . '</td>';
            echo '<td>' . $event['start_date'] . '</td>';
            echo '<td>' . $event['end_date'] . '</td>';
            echo '<td><a href="edit_discount_event.php?id=' . $event['id'] . '" class="btn">Edit</a></td>';
            echo '</tr>';
        }
        ?>
    </table>
</main>

<footer>
    <a href="login.php" class="btn">Log Out</a>
</footer>

</body>
</html>
