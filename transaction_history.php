<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil riwayat transaksi pengguna
$query = "SELECT * FROM transactions WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histori Transaksi</title>
</head>
<body>
    <h1>Histori Transaksi</h1>
    <table border="1">
        <tr>
            <th>ID Transaksi</th>
            <th>Total Pembayaran</th>
            <th>Tanggal Transaksi</th>
        </tr>
        <?php while ($transaction = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $transaction['id']; ?></td>
                <td><?php echo number_format($transaction['total_amount'], 0, ',', '.'); ?></td>
                <td><?php echo $transaction['transaction_date']; ?></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
