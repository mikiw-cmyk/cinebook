<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}
require_once 'database.php';
$movies = $conn->query("SELECT * FROM movies");
$upcoming = $conn->query("SELECT * FROM upcoming_movies");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="user.css">
    <title>Movie Booking</title>
</head>

<body>

    <section class="top-bar">
        <div class="left-content">
            <h2 class="title">Movie for You</h2>
            <ul class="navigation">
                <li><a href="#">Home</a></li>
                <li><a class="active" href="#">Movies</a></li>
                <li><a href="#">Theaters</a></li>
            </ul>
        </div>
    </section>

    <div class="movies-container">

        <?php while ($row = $upcoming->fetch_assoc()): ?>
            <div class="upcoming-img-box">
                <img src="data:image/jpeg;base64,<?= base64_encode($row['poster']); ?>" alt="">
                <p class="poster-label">Upcoming Movie</p>
                <p class="upcoming-title"><?= htmlspecialchars($row['title']); ?></p>
                <div class="buttons">
                    <a href="<?= htmlspecialchars($row['trailer_url']); ?>" class="btn-alt btn" target="_blank">Play Trailer</a>
                </div>
            </div>
        <?php endwhile; ?>

        <div class="current-movies">
            <?php while ($movie = $movies->fetch_assoc()) : ?>
                <div class="current-movie">
                    <div class="cm-img-box">
                        <img src="get_poster.php?id=<?= $movie['movie_id']; ?>" alt="Movie Poster">
                    </div>
                    <h3 class="movie-title"><?= htmlspecialchars($movie['title']); ?></h3>
                    <p class="screen-name">Show Date: <?= htmlspecialchars($movie['show_date']); ?> | Time: <?= htmlspecialchars($movie['show_time']); ?></p>
                    <div class="booking">
                        <h2 class="price">&#8369;<?= htmlspecialchars($movie['price']); ?></h2>
                        <a href="#" class="btn buy-ticket-btn"
                            data-movie-id="<?= $movie['movie_id']; ?>"
                            data-title="<?= htmlspecialchars($movie['title']); ?>">
                            Buy Tickets
                        </a>
                        <a href="<?= htmlspecialchars($movie['trailer_url']); ?>" class="btn-alt btn" target="_blank">
                            Play Trailer
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

    </div>

    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <span id="closeModal" class="close">&times;</span>
            <h2>Choose Payment Method</h2>
            <button id="payOnlineBtn">Pay Online</button>
            <button id="payCashBtn">Pay with Cash</button>
        </div>
    </div>

    <div id="amountModal" class="modal">
        <div class="modal-content">
            <span id="closeAmountModal" class="close">&times;</span>
            <h2>Enter Amount</h2>
            <input type="number" id="amountInput" placeholder="Enter Amount" />
            <button id="confirmAmountBtn">Confirm Amount</button>
        </div>
    </div>

    <div id="theaterModal" class="modal">
        <div class="modal-content">
            <span id="closeTheaterModal" class="close">&times;</span>
            <h2>Select Theater</h2>
            <ul id="theaterList">
                <li data-theater="ICM">ICM</li>
                <li data-theater="Alturas">Alturas</li>
                <li data-theater="BQ">BQ</li>
            </ul>
        </div>
    </div>

    <div id="seatModal" class="modal">
        <div class="modal-content">
            <span id="closeSeatModal" class="close">&times;</span>
            <h2>Select Your Seat</h2>
            <div class="seats-container">
                <div class="seat-row">
                    <span class="seat" data-seat="A1">A1</span>
                    <span class="seat" data-seat="A2">A2</span>
                    <span class="seat" data-seat="A3">A3</span>
                    <span class="seat" data-seat="A4">A4</span>
                </div>
                <div class="seat-row">
                    <span class="seat" data-seat="B1">B1</span>
                    <span class="seat" data-seat="B2">B2</span>
                    <span class="seat" data-seat="B3">B3</span>
                    <span class="seat" data-seat="B4">B4</span>
                </div>
            </div>
            <button id="confirmSeatBtn">Confirm Seat</button>
        </div>
    </div>

    <script src="user.js"></script>
</body>

</html>