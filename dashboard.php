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
    <link rel="stylesheet" href="styles.css">
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
        <h2>Dashboard Pengguna</h2>
        <p>Selamat datang di dasbor Anda. Silakan pilih opsi di atas untuk melanjutkan.</p>
        
        <!-- Menampilkan histori transaksi jika ada -->
        <h3>Riwayat Transaksi Terakhir</h3>
        <?php
        // Mengambil data transaksi terbaru pengguna
        require 'koneksi.php';
        $user_id = $_SESSION['user_id'];
        $query = "SELECT * FROM transactions WHERE user_id = ? ORDER BY transaction_date DESC LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($transaction = $result->fetch_assoc()) {
            // Menampilkan data transaksi terbaru
            echo "<p><strong>ID Transaksi:</strong> " . $transaction['id'] . "</p>";
            echo "<p><strong>Metode Pembayaran:</strong> " . $transaction['payment_method'] . "</p>";
            echo "<p><strong>Total Pembayaran:</strong> Rp " . number_format($transaction['total_amount'], 0, ',', '.') . "</p>";
            echo "<p><strong>Tanggal Transaksi:</strong> " . $transaction['transaction_date'] . "</p>";
        } else {
            echo "<p>Anda belum melakukan transaksi apapun.</p>";
        }
        ?>
    </main>
</body>
</html>
