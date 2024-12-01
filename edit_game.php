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
        $image_name = $game['image']; // Gunakan gambar lama jika tidak diupdate

        // Periksa apakah ada gambar baru yang diupload
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $image_tmp = $_FILES['image']['tmp_name'];
            $image_name = basename($_FILES['image']['name']);
            $image_path = 'uploads/' . $image_name;

            // Pindahkan gambar ke folder uploads
            if (!move_uploaded_file($image_tmp, $image_path)) {
                echo "Gagal mengupload gambar baru!";
                exit();
            }
        }

        if (empty($name) || empty($price) || empty($description) || empty($stock)) {
            echo "Semua kolom harus diisi!";
        } else {
            // Update data game
            $query = "UPDATE games SET title = ?, price = ?, description = ?, stock = ?, image = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sdsisi", $name, $price, $description, $stock, $image_name, $game_id);

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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
        }

        form {
            max-width: 500px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        input, textarea, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .cancel-btn {
            background-color: #f44336;
        }

        .cancel-btn:hover {
            background-color: #e53935;
        }
    </style>
</head>
<body>
    <h1>Edit Game</h1>
    <form method="POST" action="edit_game.php?id=<?php echo $game['id']; ?>" enctype="multipart/form-data">
        <label for="name">Nama Game:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($game['title']); ?>" required>

        <label for="price">Harga:</label>
        <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($game['price']); ?>" required>

        <label for="description">Deskripsi:</label>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($game['description']); ?></textarea>

        <label for="stock">Stok:</label>
        <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($game['stock']); ?>" required>

        <label for="image">Gambar Baru (opsional):</label>
        <input type="file" id="image" name="image" accept="image/*">
        
        <?php if ($game['image']) { ?>
            <p>Gambar Saat Ini:</p>
            <img src="uploads/<?php echo $game['image']; ?>" alt="Game Image" width="100">
        <?php } ?>

        <button type="submit">Perbarui Game</button>
        <a href="admin_dashboard.php"><button type="button" class="cancel-btn">Cancel</button></a>
    </form>
</body>
</html>
