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
    <style>
        /* Gaya tata letak */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }

        main {
            padding: 20px;
            text-align: center;
        }

        /* Gaya untuk kontainer tabel dan form box */
        .container {
            position: relative;
            margin-top: 20px;
            text-align: center;
        }

        /* Gaya untuk tabel */
        table {
            width: 50%;
            /* Lebar tabel lebih kecil */
            margin: 0 auto;
            /* Tabel berada di tengah */
            border-collapse: collapse;
            position: relative;
            z-index: 2;
            /* Tabel berada di atas form box */
            background: white;
            /* Pastikan tabel tetap putih */
            color: black;
            /* Teks tabel terlihat jelas */
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        /* Gaya untuk form box */
        .form-box {
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            width: 52%;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: rgba(255, 255, 255, 0.8);
            /* Transparan tapi cukup jelas */
            z-index: 1;
            /* Form box berada di bawah tabel */
            box-sizing: border-box;
        }
        .qwe{
            color : black;
        }
    </style>

</head>

<body>
    <header>
        <h1>Selamat Datang, <?php echo $_SESSION['username']; ?></h1>
        <nav>
            <ul>
                <button type="submit">
                    <li><a href="game_list.php">Lihat Daftar Game</a></li>
                </button>
                <button type="submit">
                    <li><a href="transaction_history.php">Lihat Histori Transaksi</a></li>
                </button>
                <button type="submit">
                    <li><a href="buy_game.php">Beli Game</a></li>
                </button>
                <button type="submit">
                    <li><a href="logout.php">Logout</a></li>
                </button>
            </ul>
        </nav>
    </header>

    <main>
        <h2>Dashboard Pengguna</h2>
        <p>Selamat Datang di Dasboard Anda. Silakan pilih opsi di atas untuk melanjutkan.</p>

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
            echo "<p><strong>Status:</strong> " . $transaction['status'] . "</p>";
        } else {
            echo "<p>Anda belum melakukan transaksi apapun.</p>";
        }
        ?>
        <div class="container">
            <!-- Form box untuk tampilan lebih rapi -->
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