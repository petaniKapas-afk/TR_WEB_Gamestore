<!-- index.php -->
<!DOCTYPE html>
<html lang="en" id="home-page">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gamestore</title>
    <!-- Link to styles.css -->
    <link rel="stylesheet" href="home.css">
    <!-- Link to Pixelated Font (e.g., Press Start 2P from Google Fonts) -->
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
</head>
<body>
       <!-- Header with full-screen image and overlay -->
       <header class="full-screen-image">
           <div class="overlay">
               <h1 class="pixelated-font">Welcome to Gamestore</h1>
               <div class="auth-links">
                   <a href="login.php">Login</a>
                   <a href="daftar.php">Register</a>
               </div>
           </div>
       </header>

    <!-- Explore Section with "See Games" button -->
    <section class="explore-section">
        <h2>Explore Our Games</h2>
        <button id="see-games">See Games</button>
        <div id="game-list-container" style="display: none;">
            <!-- Include the game list -->
            <?php include 'game_list.php'; ?>
        </div>
    </section>

    <!-- JavaScript to handle "See Games" button -->
    <script>
        document.getElementById('see-games').addEventListener('click', function() {
            var gameListContainer = document.getElementById('game-list-container');
            if (gameListContainer.style.display === 'none') {
                gameListContainer.style.display = 'block';
                this.textContent = 'Hide Games';
            } else {
                gameListContainer.style.display = 'none';
                this.textContent = 'See Games';
            }
        });
    </script>
</body>
</html>
