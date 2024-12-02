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
               
               * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Arial', sans-serif;
    background-color: #121212; /* Background hitam sesuai tema */
    color: #f0f0f0; /* Teks terang */
    line-height: 1.6;
    margin-bottom: 100px;
}

/* Header */
header {
    background-color: #1e1e1e; /* Warna lebih gelap untuk header */
    color: #f0f0f0; /* Teks terang */
    padding: 30px 0;
    text-align: center;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.3);
}

header h1 {
    font-size: 2.5rem;
}

/* Navigation */
nav {
    background-color: #1e1e1e; /* Warna gelap untuk navigasi */
    padding: 15px;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.3);
}

nav ul {
    list-style: none;
    display: flex;
    justify-content: center;
}

nav ul li {
    margin: 0 20px;
}

nav ul li a {
    color: #ff9800; /* Warna oranye untuk teks link */
    font-family: 'Press Start 2P', cursive; /* Gaya font pixelated */
    text-decoration: none;
    padding: 10px 20px;
    transition: background-color 0.3s ease;
}

nav ul li a:hover {
    background-color: #2a2a2a; /* Warna hover yang lebih gelap */
    border-radius: 5px;
}

/* Main Content */
main {
    padding: 30px 0;
}

h2 {
    text-align: center;
    font-size: 2rem;
    margin-bottom: 30px;
    color: #ff9800; /* Warna oranye untuk heading */
}

/* Table Styling */
table {
    width: 90%;
    margin: 0 auto;
    border-collapse: collapse;
    background-color: #1e1e1e; /* Background tabel gelap */
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    border-radius: 8px;
}

th, td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #444; /* Garis tabel */
}

th {
    background-color: #2a2a2a; /* Warna lebih gelap untuk header tabel */
    color: #ff9800; /* Teks oranye */
    text-transform: uppercase;
    font-size: 0.9rem;
}

td {
    color: #f0f0f0; /* Teks terang */
}

tr:hover {
    background-color: #2e2e2e; /* Warna saat baris di-hover */
}

tr:nth-child(even) {
    background-color: #2a2a2a; /* Background selang-seling */
}

/* Game Image */
.game-image {
    width: 80px;
    height: 80px;
    border-radius: 5px;
    object-fit: cover;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.3);
}

/* Buttons */
.btn {
    display: inline-block;
    padding: 10px 20px;
    background-color: #ff9800; /* Tombol oranye */
    color: #121212; /* Teks hitam */
    text-decoration: none;
    border-radius: 5px;
    margin: 5px;
    font-weight: bold;
    transition: background-color 0.3s ease;
    text-align: center;
}

.btn:hover {
    background-color: #e68900; /* Warna hover */
}

.btn-danger {
    background-color: red;
}

.btn-danger:hover {
    background-color: #cc0000;
}

.btn-out {
    display: inline-block;
    padding: 10px 20px;
    background-color: #444; /* Warna latar belakang berbeda */
    color: #f0f0f0; /* Warna teks terang */
    text-decoration: none;
    border: 2px solid #ff9800; /* Border oranye */
    border-radius: 5px;
    margin: 5px;
    font-weight: bold;
    transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
    text-align: center;
}

.btn-out:hover {
    background-color: #ff9800; /* Warna hover oranye */
    color: #121212; /* Teks hitam saat hover */
    border-color: #e68900; /* Warna border saat hover */
}



    </style>
</head>
<body>

<header>
    <h1>Welcome Admin</h1>
</header>

<nav>
    <ul>
        <li><a href="add_game.php" class="btn">Add New Game</a></li>
        <li><a href="verify_payments.php" class="btn">Verify Payments</a></li>
        <li><a href="transaction_history_admin.php" class="btn">Transaction History</a></li>
        <li><a href="discount_event.php" class="btn">Create Discount Event</a></li>
        <li><a href="index.php" class="btn-out">Log Out</a></li>
    </ul>
</nav>

<main>
    <h2>Game List</h2>
    <table>
        <tr>
            <th> Id </th>
            <th> Judul </th>
            <th> Harga </th>
            <th> Stok </th>
            <th> Gambar </th>
            <th> Aksi </th>
        </tr>
        <?php while ($game = $games_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $game['id']; ?></td>
                <td><?php echo $game['title']; ?></td>
                <td><?php echo $game['price']; ?></td>
                <td><?php echo $game['stock']; ?></td>
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
</body>
</html>
