<?php
session_start();
require 'koneksi.php';

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Periksa apakah data form sudah ada
    if (isset($_POST['title'], $_POST['price'], $_POST['description'], $_POST['stock'], $_FILES['image'])) {
        $title = $_POST['title'];
        $price = $_POST['price'];
        $description = $_POST['description'];
        $stock = $_POST['stock'];

        // Proses upload gambar
        $image = $_FILES['image'];
        $image_name = $image['name'];
        $image_tmp_name = $image['tmp_name'];
        $image_error = $image['error'];

        if ($image_error === 0) {
            // Tentukan direktori untuk menyimpan gambar
            $upload_dir = 'uploads/';
            $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
            $image_new_name = uniqid('', true) . '.' . $image_ext;
            $image_path = $upload_dir . $image_new_name;

            // Pindahkan gambar ke direktori yang ditentukan
            if (move_uploaded_file($image_tmp_name, $image_path)) {
                // Query untuk menambah game baru
                $insert_query = "INSERT INTO games (title, description, price, stock, image) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($insert_query);

                // Periksa apakah prepare berhasil
                if ($stmt === false) {
                    die('MySQL prepare error: ' . $conn->error);
                }

                // Bind parameter dan simpan nama file gambar
                $stmt->bind_param("ssdis", $title, $description, $price, $stock, $image_new_name);

                if ($stmt->execute()) {
                    echo "Game added successfully!";
                } else {
                    echo "Error adding game: " . $stmt->error;
                }
            } else {
                echo "Error uploading image!";
            }
        } else {
            echo "Error with image upload!";
        }
    } else {
        echo "Missing game information!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Game</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 50%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #4CAF50;
            font-size: 16px;
        }
        a:hover {
            color: #45a049;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Add New Game</h2>
        <form method="POST" action="add_game.php" enctype="multipart/form-data">
            <label for="title">Game Title:</label>
            <input type="text" id="title" name="title" required>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea>

            <label for="stock">Stock:</label>
            <input type="number" id="stock" name="stock" required>

            <label for="image">Game Image:</label>
            <input type="file" id="image" name="image" accept="image/*" required>

            <button type="submit">Add Game</button>
        </form>

        <a href="admin_dashboard.php">Back to Dashboard</a>
    </div>

</body>
</html>
