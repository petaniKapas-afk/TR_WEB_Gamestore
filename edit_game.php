<?php
session_start();
require 'koneksi.php';

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $game_id = $_GET['id'];

    // Ambil data game yang ingin diubah
    $query = "SELECT * FROM games WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $game_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $game = $result->fetch_assoc();

    if (!$game) {
        echo "Game tidak ditemukan!";
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Validasi input
        $name = trim($_POST['name']);
        $price = trim($_POST['price']);
        $description = trim($_POST['description']);
        $stock = trim($_POST['stock']);

        if (empty($name) || empty($price) || empty($description) || empty($stock)) {
            echo "Semua kolom harus diisi!";
        } else {
            // Update data game
            $query = "UPDATE games SET title = ?, price = ?, description = ?, stock = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssi", $name, $price, $description, $stock, $game_id);

            if ($stmt->execute()) {
                echo "<script>alert('Game berhasil diperbarui!'); window.location.href='admin_dashboard.php';</script>";
                exit();
            } else {
                echo "Error saat memperbarui game: " . $conn->error;
            }
        }
    }
} else {
    echo "ID game diperlukan dan harus berupa angka!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Game</title>
</head>
<body>
    <h1>Edit Game</h1>
    <form method="POST" action="edit_game.php?id=<?php echo $game['id']; ?>">
        <label for="name">Nama Game:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($game['title']); ?>" required><br>

        <label for="price">Harga:</label>
        <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($game['price']); ?>" required><br>

        <label for="description">Deskripsi:</label>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($game['description']); ?></textarea><br>

        <label for="stock">Stok:</label>
        <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($game['stock']); ?>" required><br>

        <button type="submit">Perbarui Game</button>
        <a href="admin_dashboard.php"><button type="button">Cancel</button></a>
    </form>
</body>
</html>
