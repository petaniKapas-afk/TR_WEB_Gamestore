<?php
session_start();
require 'koneksi.php';

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Query untuk mengambil riwayat transaksi semua user
$query_transactions = "SELECT * FROM transactions";
$transactions_result = $conn->query($query_transactions);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History</title>
    <link rel="stylesheet" href="transaction_history.css">
</head>
<body>

<header>
    <h1>Transaction History</h1>
</header>
<mai    n>
    <table>
        <tr>
            <th>ID Transaksi</th>
            <th>User ID</th>
            <th>Game ID</th>
            <th>Jumlah</th>
            <th>Tanggal Pembayaran</th>
            <th>Status</th>
        </tr>
        <?php while ($transaction = $transactions_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $transaction['id']; ?></td>
                <td><?php echo $transaction['user_id']; ?></td>
                <td><?php echo $transaction['game_id']; ?></td>
                <td><?php echo $transaction['amount']; ?></td>
                <td><?php echo $transaction['transaction_date']; ?></td>
                <td><?php echo $transaction['status']; ?></td>
            </tr>
        <?php } ?>
    </table>
</main>

<footer>
    <a href="admin_dashboard.php" class="btn">Back to Dashboard</a>
</footer>

</body>
</html>
