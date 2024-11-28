<?php
session_start();
require 'koneksi.php';

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php'); // Jika belum login, redirect ke login
    exit();
}

// Query untuk mengambil pembayaran yang belum diverifikasi
$query = "SELECT * FROM payments WHERE verified = 0";
$result = $conn->query($query);

// Cek apakah query berhasil dijalankan
if (!$result) {
    die("Error executing query: " . $conn->error);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Payments</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Verify Payments</h1>
    <table border="1">
        <tr>
            <th>User ID</th>
            <th>Amount</th>
            <th>Payment Method</th>
            <th>Payment Date</th>
            <th>Action</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $row['user_id']; ?></td>
                <td><?php echo $row['amount']; ?></td>
                <td><?php echo $row['payment_method']; ?></td>
                <td><?php echo $row['payment_date']; ?></td>
                <td>
                    <form method="POST" action="verify_payment_action.php">
                        <input type="hidden" name="payment_id" value="<?php echo $row['id']; ?>">
                        <button type="submit">Verify</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
