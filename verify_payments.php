<?php
session_start();
require 'koneksi.php';

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Query untuk mengambil data transaksi yang masih dalam status pending
$query = "SELECT t.id, t.user_id, t.game_id, t.amount, t.payment_method, t.status, t.transaction_date,
                 g.title AS game_title, u.username AS user_name
          FROM transactions t
          JOIN games g ON t.game_id = g.id
          JOIN users u ON t.user_id = u.id
          WHERE t.status = 'pending'";

$result = $conn->query($query);

// Verifikasi perubahan status
$verification_message = '';
$verification_class = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_id'])) {
    $verify_id = $_POST['verify_id'];

    // Update status transaksi menjadi completed
    $update_query = "UPDATE transactions SET status = 'completed' WHERE id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("i", $verify_id);
    
    if ($stmt->execute()) {
        $verification_message = 'Pembayaran berhasil diverifikasi!';
        $verification_class = 'success';
    } else {
        $verification_message = 'Terjadi kesalahan saat memverifikasi pembayaran.';
        $verification_class = 'error';
    }

    // Redirect untuk memastikan halaman diperbarui setelah verifikasi
    header("Location: verify_payments.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Pembayaran</title>
    <link rel="stylesheet" href="verify_payments.css">
</head>
<body>

<header>
    <h1>Verifikasi Pembayaran</h1>
</header>

<?php if ($verification_message): ?>
    <div class="notification <?php echo $verification_class; ?>">
        <?php echo $verification_message; ?>
    </div>
<?php endif; ?>

<main>
    <table>
        <thead>
            <tr>
                <th>ID Transaksi</th>
                <th>Nama User</th>
                <th>Game</th>
                <th>Jumlah</th>
                <th>Metode Pembayaran</th>
                <th>Status</th>
                <th>Tanggal Pembayaran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($payment = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $payment['id']; ?></td>
                    <td><?php echo $payment['user_name']; ?></td>
                    <td><?php echo $payment['game_title']; ?></td>
                    <td>Rp <?php echo number_format($payment['amount'], 0, ',', '.'); ?></td>
                    <td><?php echo $payment['payment_method']; ?></td>
                    <td><?php echo $payment['status']; ?></td>
                    <td><?php echo date('d-m-Y H:i:s', strtotime($payment['transaction_date'])); ?></td>
                    <td>
                        <?php if ($payment['status'] == 'pending'): ?>
                            <form method="POST" action="">
                                <input type="hidden" name="verify_id" value="<?php echo $payment['id']; ?>">
                                <button type="submit">Verifikasi Pembayaran</button>
                            </form>
                        <?php else: ?>
                            <span>Sudah Diverifikasi</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</main>

<footer>
    <a href="admin_dashboard.php" class="btn">Kembali ke Dashboard</a>
</footer>

</body>
</html>
