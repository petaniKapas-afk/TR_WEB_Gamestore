<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengguna</title>
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>
    <header>
        <h1>Selamat Datang, <?php echo $_SESSION['username']; ?></h1>
        <nav>
            <ul>
                <li><a href="game_list.php">Lihat Daftar Game</a></li>
                <li><a href="transaction_history.php">Lihat Histori Transaksi</a></li>
                <li><a href="buy_game.php">Beli Game</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="container">
            <div class="form-box">
                <h3>Riwayat Transaksi Terakhir</h3>
                <?php
                require 'koneksi.php';
                $user_id = $_SESSION['user_id'];
                $query = "SELECT * FROM transactions WHERE user_id = ? ORDER BY transaction_date DESC LIMIT 1";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($transaction = $result->fetch_assoc()) {
                    echo "<table>";
                    echo "<tr><th>ID Transaksi</th><td>" . $transaction['id'] . "</td></tr>";
                    echo "<tr><th>Metode Pembayaran</th><td>" . $transaction['payment_method'] . "</td></tr>";
                    echo "<tr><th>Total Pembayaran</th><td>Rp " . number_format($transaction['total_amount'], 0, ',', '.') . "</td></tr>";
                    echo "<tr><th>Tanggal Transaksi</th><td>" . $transaction['transaction_date'] . "</td></tr>";
                    echo "<tr><th>Status</th><td>" . $transaction['status'] . "</td></tr>";
                    echo "</table>";
                } else {
                    echo "<p>Anda belum melakukan transaksi apapun.</p>";
                }
                ?>
            </div>
        </div>
    </main>
</body>

</html>
