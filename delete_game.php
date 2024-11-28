<?php
session_start();
require 'koneksi.php';

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Cek jika ID game tersedia di URL
if (isset($_GET['id'])) {
    $game_id = $_GET['id'];
} else {
    echo "Game ID is required!";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Game</title>
    <script>
        // Fungsi untuk konfirmasi penghapusan
        function confirmDeletion() {
            var confirmation = confirm("Are you sure you want to delete this game?");
            if (confirmation) {
                // Jika user memilih "OK", kirimkan form untuk menghapus game
                window.location.href = "delete_game.php?id=<?php echo $game_id; ?>";  // Mengirimkan ke halaman delete
            } else {
                // Jika user memilih "Cancel", jangan lakukan apa-apa
                return false;
            }
        }
    </script>
</head>
<body>
    <h1>Delete Game</h1>

    <p>Are you sure you want to delete this game?</p>

    <!-- Tombol untuk mengonfirmasi penghapusan -->
    <button onclick="confirmDeletion()">Yes, Delete Game</button>
    <a href="admin_dashboard.php"><button>No, Go Back</button></a>

    <?php
    // Jika sudah terkonfirmasi dan ada ID, lakukan penghapusan game
    if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($game_id)) {
        // Query untuk menghapus game
        $query = "DELETE FROM games WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $game_id);

        // Eksekusi query
        if ($stmt->execute()) {
            echo "Game deleted successfully!";
            header('Location: admin_dashboard.php'); // Kembali ke halaman dashboard admin
        } else {
            echo "Error deleting game: " . $conn->error;
        }
    }
    ?>
</body>
</html>
