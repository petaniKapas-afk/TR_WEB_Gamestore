<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Game</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #121212; /* Background hitam sesuai tema */
            color: #f0f0f0; /* Teks terang */
        }
        .container {
            width: 50%;
            margin: 20px auto;
            padding: 20px;
            background-color: #1e1e1e; /* Background form lebih gelap */
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            border-radius: 8px;
        }
        h2 {
            text-align: center;
            color: #ff9800; /* Warna oranye untuk judul */
            font-family: 'Press Start 2P', cursive; /* Font pixelated */
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #f0f0f0; /* Teks terang untuk label */
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #444; /* Warna border yang lebih gelap */
            border-radius: 4px;
            font-size: 16px;
            background-color: #2a2a2a; /* Background input lebih gelap */
            color: #f0f0f0; /* Teks terang untuk input */
        }
        button {
            background-color: #ff9800; /* Tombol oranye */
            color: #121212; /* Teks hitam */
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        button:hover {
            background-color: #e68900; /* Warna hover tombol */
            transform: scale(1.05);
        }
        button:active {
            background-color: #d97700; /* Warna saat tombol diklik */
            transform: scale(0.95);
        }
        a {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #ff9800; /* Warna oranye untuk link */
            font-size: 16px;
        }
        a:hover {
            color: #e68900; /* Warna hover untuk link */
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
