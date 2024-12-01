<?php
session_start();
require 'koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil riwayat transaksi user
$query = "SELECT t.id, g.title, t.amount, t.payment_method, t.status, t.transaction_date 
          FROM transactions t
          JOIN games g ON t.game_id = g.id
          WHERE t.user_id = ?
          ORDER BY t.transaction_date DESC";
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
    <title>Riwayat Transaksi</title>
    <link rel="stylesheet" href="transaction_history.css">
</head>
<body>
    <header>
        <h1>Transaction History</h1>
    </header>
    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Game</th>
                    <th>Jumlah</th>
                    <th>Metode Pembayaran</th>
                    <th>Status</th>
                    <th>Tanggal Transaksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $counter = 1; while ($transaction = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $counter++; ?></td>
                        <td><?php echo htmlspecialchars($transaction['title']); ?></td>
                        <td>Rp <?php echo number_format($transaction['amount'], 0, ',', '.'); ?></td>
                        <td><?php echo htmlspecialchars($transaction['payment_method']); ?></td>
                        <td><?php echo htmlspecialchars($transaction['status']); ?></td>
                        <td><?php echo date('d-m-Y H:i:s', strtotime($transaction['transaction_date'])); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <footer>
    <a href="dashboard.php" class="btn">Back to Dashboard</a>
    </footer>
</body>
</html>
