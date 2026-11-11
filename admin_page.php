<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['user_id']) && !isset($_POST['movie_id'])) {
        $userId = intval($_POST['user_id']);

        $stmt = $conn->prepare("SELECT email FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($emailToDelete);
        $stmt->fetch();
        $stmt->close();

        if ($emailToDelete === $_SESSION['email']) {
            $deleteError = "You cannot delete your own account.";
        } else {
            $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->close();

            header("Location: admin_page.php");
            exit();
        }
    }

    if (isset($_POST['movie_id']) && isset($_POST['delete_movie'])) {
        $movieId = intval($_POST['movie_id']);

        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE movie_id = ?");
        $checkStmt->bind_param("i", $movieId);
        $checkStmt->execute();
        $checkStmt->bind_result($bookingCount);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($bookingCount > 0) {
            $deleteMovieError = "Cannot delete movie. It has existing bookings.";
        } else {
            $stmt = $conn->prepare("DELETE FROM movies WHERE movie_id = ?");
            $stmt->bind_param("i", $movieId);
            $stmt->execute();
            $stmt->close();

            header("Location: admin_page.php");
            exit();
        }
    }
    if (isset($_POST['movie_title'], $_POST['show_date'], $_POST['show_time'], $_POST['price'], $_POST['trailer_url'], $_FILES['poster_image'])) {
        $title = $_POST['movie_title'];
        $show_date = $_POST['show_date'];
        $show_time = $_POST['show_time'];
        $price = floatval($_POST['price']);
        $trailerUrl = trim($_POST['trailer_url']);
        $poster = file_get_contents($_FILES['poster_image']['tmp_name']);
        $null = NULL;

        if (preg_match('/youtu\.be\/([^\?&]+)/', $trailerUrl, $matches)) {
            $videoId = $matches[1];
            $trailerUrl = "https://www.youtube.com/embed/" . $videoId;
        } elseif (preg_match('/youtube\.com\/watch\?v=([^\?&]+)/', $trailerUrl, $matches)) {
            $videoId = $matches[1];
            $trailerUrl = "https://www.youtube.com/embed/" . $videoId;
        } elseif (!preg_match('/youtube\.com\/embed\//', $trailerUrl)) {
            $trailerUrl = "";
        }

        $stmt = $conn->prepare("INSERT INTO movies (title, show_date, show_time, poster, price, trailer_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssbds", $title, $show_date, $show_time, $null, $price, $trailerUrl);
        $stmt->send_long_data(3, $poster);
        $stmt->execute();
        $stmt->close();

        header("Location: admin_page.php");
        exit();
    }


    if (isset($_POST['edit_movie_id'], $_POST['edit_movie_title'], $_POST['edit_movie_price'], $_POST['edit_trailer_url'])) {
        $movieId = intval($_POST['edit_movie_id']);
        $newTitle = $_POST['edit_movie_title'];
        $newPrice = floatval($_POST['edit_movie_price']);
        $trailerUrl = trim($_POST['edit_trailer_url']);

        if (preg_match('/youtu\.be\/([^\?&]+)/', $trailerUrl, $matches)) {
            $videoId = $matches[1];
            $trailerUrl = "https://www.youtube.com/embed/" . $videoId;
        } elseif (preg_match('/youtube\.com\/watch\?v=([^\?&]+)/', $trailerUrl, $matches)) {
            $videoId = $matches[1];
            $trailerUrl = "https://www.youtube.com/embed/" . $videoId;
        } elseif (!preg_match('/youtube\.com\/embed\//', $trailerUrl)) {
            $trailerUrl = "";
        }

        $stmt = $conn->prepare("UPDATE movies SET title = ?, price = ?, trailer_url = ? WHERE movie_id = ?");
        $stmt->bind_param("sdsi", $newTitle, $newPrice, $trailerUrl, $movieId);
        $stmt->execute();
        $stmt->close();

        header("Location: admin_page.php");
        exit();
    }

    if (isset($_POST['upcoming_movie_title'], $_POST['upcoming_trailer_url'], $_FILES['upcoming_poster_image'])) {
        $title = $_POST['upcoming_movie_title'];
        $trailerUrl = trim($_POST['upcoming_trailer_url']);
        $poster = file_get_contents($_FILES['upcoming_poster_image']['tmp_name']);
        $null = NULL;

        if (preg_match('/youtu\.be\/([^\?&]+)/', $trailerUrl, $matches)) {
            $videoId = $matches[1];
            $trailerUrl = "https://www.youtube.com/embed/" . $videoId;
        } elseif (preg_match('/youtube\.com\/watch\?v=([^\?&]+)/', $trailerUrl, $matches)) {
            $videoId = $matches[1];
            $trailerUrl = "https://www.youtube.com/embed/" . $videoId;
        } elseif (!preg_match('/youtube\.com\/embed\//', $trailerUrl)) {
            $trailerUrl = "";
        }

        $stmt = $conn->prepare("INSERT INTO upcoming_movies (title, trailer_url, poster) VALUES (?, ?, ?)");
        $stmt->bind_param("ssb", $title, $trailerUrl, $null);
        $stmt->send_long_data(2, $poster);
        $stmt->execute();
        $stmt->close();

        header("Location: admin_page.php");
        exit();
    }

    if (isset($_POST['delete_upcoming_movie_id'])) {
        $movieId = intval($_POST['delete_upcoming_movie_id']);

        $stmt = $conn->prepare("DELETE FROM upcoming_movies WHERE id = ?");
        $stmt->bind_param("i", $movieId);
        $stmt->execute();
        $stmt->close();

        header("Location: admin_page.php");
        exit();
    }

    if (isset($_POST['edit_upcoming_movie_id'], $_POST['edit_upcoming_movie_title'], $_POST['edit_upcoming_movie_trailer'])) {
        $movieId = intval($_POST['edit_upcoming_movie_id']);
        $newTitle = $_POST['edit_upcoming_movie_title'];
        $newTrailerUrl = $_POST['edit_upcoming_movie_trailer'];

        $stmt = $conn->prepare("UPDATE upcoming_movies SET title = ?, trailer_url = ? WHERE id = ?");
        $stmt->bind_param("ssi", $newTitle, $newTrailerUrl, $movieId);
        $stmt->execute();
        $stmt->close();

        header("Location: admin_page.php");
        exit();
    }
}

$upcomingMoviesCountResult = $conn->query("SELECT COUNT(*) AS upcoming_movies_count FROM upcoming_movies");
$upcomingMoviesCountRow = $upcomingMoviesCountResult->fetch_assoc();
$upcomingMoviesCount = $upcomingMoviesCountRow['upcoming_movies_count'] ?? 0;

$bookings = $conn->query("SELECT b.*, u.email, m.title FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    LEFT JOIN movies m ON b.movie_id = m.movie_id
    ORDER BY b.booking_time DESC");

$today = date("Y-m-d");
$todaySalesResult = $conn->query("
    SELECT SUM(m.price) AS total_sales_today
    FROM bookings b
    JOIN movies m ON b.movie_id = m.movie_id
    WHERE DATE(b.booking_time) = '$today'
");
$todaySalesRow = $todaySalesResult->fetch_assoc();
$todaySales = $todaySalesRow['total_sales_today'] ?? 0;

$totalSalesResult = $conn->query("
    SELECT SUM(m.price) AS total_sales_all
    FROM bookings b
    JOIN movies m ON b.movie_id = m.movie_id
");
$totalSalesRow = $totalSalesResult->fetch_assoc();
$totalSales = $totalSalesRow['total_sales_all'] ?? 0;

$users = $conn->query("SELECT * FROM users");
$movies = $conn->query("SELECT * FROM movies");
$upcomingMovies = $conn->query("SELECT * FROM upcoming_movies");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Page</title>
    <link rel="stylesheet" href="admin.css?v=1.0">
</head>

<body>

    <header>
        <h1>Admin Dashboard</h1>
    </header>

    <nav>
        <a href="#" onclick="showSection('dashboard')">Dashboard</a>
        <a href="#" onclick="showSection('users')">Manage Users</a>
        <a href="#" onclick="showSection('movies')">Manage Movies</a>
        <a href="#" onclick="showSection('upcoming-movie')">Upcoming Movie</a>
    </nav>

    <main>
        <section id="dashboard" class="section">
            <h2>Dashboard Overview</h2>
            <div class="stats">
                <div class="card">Total Users: <?= $users->num_rows ?></div>
                <div class="card">Movies: <?= $movies->num_rows ?></div>
                <div class="card">Upcoming Movies: <?= $upcomingMoviesCount ?></div>
                <div class="card">Sales Today: ₱<?= number_format($todaySales, 2) ?></div>
                <div class="card">Total Sales: ₱<?= number_format($totalSales, 2) ?></div>
                <div class="card">Bookings Today:
                    <?php
                    $today = date("Y-m-d");
                    $todayBookings = $conn->query("SELECT COUNT(*) AS count FROM bookings WHERE DATE(booking_time) = '$today'");
                    $countRow = $todayBookings->fetch_assoc();
                    echo $countRow['count'];
                    ?>
                </div>
            </div>

            <h3 class="recent-bookings">Recent Bookings</h3>
            <table>
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Movie Name</th>
                        <th>Theater</th>
                        <th>Seat</th>
                        <th>Payment Method</th>
                        <th>Date and time</th>
                </thead>
                <tbody>
                    <?php while ($row = $bookings->fetch_assoc()) : ?>
                        <tr>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['theater_name']) ?></td>
                            <td><?= htmlspecialchars($row['seat_number']) ?></td>
                            <td><?= htmlspecialchars($row['payment_method']) ?></td>
                            <td><?= htmlspecialchars($row['booking_time']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <section id="users" class="section" style="display: none;">
            <h2>User Management</h2>
            <?php if (isset($deleteError)) : ?>
                <p style="color: red;"><?= htmlspecialchars($deleteError); ?></p>
            <?php endif; ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Role</th>
                </thead>
                <tbody>
                    <?php $users->data_seek(0);
                    while ($user = $users->fetch_assoc()) : ?>
                        <tr>
                            <td><?= $user['user_id']; ?></td>
                            <td><?= htmlspecialchars($user['email']); ?></td>
                            <td><?= htmlspecialchars($user['role']); ?></td>
                            <td>
                                <form method="post" style="display:inline;" onsubmit="return confirm('Delete this user?');">
                                    <input type="hidden" name="user_id" value="<?= $user['user_id']; ?>">
                                    <button type="submit" class="delete-btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <section id="upcoming-movie" class="section" style="display: none;">
            <h2>Upcoming Movie Management</h2>

            <form class="movie-form" enctype="multipart/form-data" method="post" action="admin_page.php">
                <div class="input-row">
                    <input type="text" class="movie-input" name="upcoming_movie_title" placeholder="Movie Title" required>
                </div>
                <div class="input-row">
                    <input type="url" class="movie-input" name="upcoming_trailer_url" placeholder="Trailer URL" required>
                </div>
                <label class="custom-file-upload">
                    <input type="file" name="upcoming_poster_image" accept="image/*" required>
                    Upload Movie Poster
                </label>
                <div class="form-action-row">
                    <button type="submit" class="add-movie-btn">Add Upcoming Movie</button>
                </div>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Poster</th>
                        <th>Trailer</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($upcoming = $upcomingMovies->fetch_assoc()) : ?>
                        <tr>
                            <td><?= $upcoming['id']; ?></td>
                            <td><?= htmlspecialchars($upcoming['title']); ?></td>
                            <td>
                                <?php
                                $posterData = $upcoming['poster'];
                                if (!empty($posterData)) {
                                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                                    $mimeType = $finfo->buffer($posterData);
                                    echo '<img src="data:' . $mimeType . ';base64,' . base64_encode($posterData) . '" width="60" />';
                                } else {
                                    echo 'No Poster';
                                }
                                ?>
                            </td>
                            <td><a href="<?= htmlspecialchars($upcoming['trailer_url']); ?>" target="_blank">Watch Trailer</a></td>
                            <td>
                                <button class="edit-btn" onclick="openEditUpcomingMovieModal(
                                     <?= $upcoming['id']; ?>,
                                    '<?= htmlspecialchars($upcoming['title'], ENT_QUOTES); ?>',
                                    '<?= htmlspecialchars($upcoming['trailer_url'], ENT_QUOTES); ?>'
                                    )">Edit</button>
                                <form method="post" action="admin_page.php" style="display:inline;" onsubmit="return confirm('Delete this upcoming movie?');">
                                    <input type="hidden" name="delete_upcoming_movie_id" value="<?= $upcoming['id']; ?>">
                                    <button type="submit" class="delete-btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <section id="movies" class="section" style="display: none;">
            <h2>Movie Management</h2>
            <?php if (isset($deleteMovieError)) : ?>
                <script>
                    alert("<?= addslashes($deleteMovieError); ?>");
                </script>
            <?php endif; ?>

            <form class="movie-form" enctype="multipart/form-data" method="post" action="admin_page.php">
                <div class="input-row">
                    <input type="text" class="movie-input" name="movie_title" placeholder="Movie Title" required>
                </div>
                <div class="input-row">
                    <input type="date" class="movie-input" name="show_date" required>
                </div>
                <div class="input-row">
                    <input type="time" class="movie-input" name="show_time" required>
                </div>
                <div class="input-row">
                    <input type="number" step="0.01" class="movie-input" name="price" placeholder="Price" required>
                </div>
                <div class="input-row">
                    <input type="url" class="movie-input" name="trailer_url" placeholder="Trailer URL" required>
                </div>
                <label class="custom-file-upload">
                    <input type="file" name="poster_image" accept="image/*" required>
                    Upload Movie Poster
                </label>
                <div class="form-action-row">
                    <button type="submit" class="add-movie-btn">Add Movie</button>
                </div>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Poster</th>
                        <th>Price</th>
                        <th>Trailer</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($movie = $movies->fetch_assoc()) : ?>
                        <tr>
                            <td><?= $movie['movie_id']; ?></td>
                            <td><?= htmlspecialchars($movie['title']); ?></td>
                            <td><img src="data:image/jpeg;base64,<?= base64_encode($movie['poster']); ?>" width="60" /></td>
                            <td><?= number_format($movie['price'], 2); ?></td>
                            <td><a href="<?= htmlspecialchars($movie['trailer_url']); ?>" target="_blank">Watch Trailer</a></td>
                            <td>
                                <button class="edit-btn" onclick="openEditModal(<?= $movie['movie_id']; ?>, '<?= htmlspecialchars($movie['title'], ENT_QUOTES); ?>', <?= $movie['price']; ?>, '<?= htmlspecialchars($movie['trailer_url'], ENT_QUOTES); ?>')">Edit</button>
                                <form method="post" action="admin_page.php" style="display:inline;" onsubmit="return confirm('Delete this movie?');">
                                    <input type="hidden" name="movie_id" value="<?= $movie['movie_id']; ?>">
                                    <input type="hidden" name="delete_movie" value="1">
                                    <button type="submit" class="delete-btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>

        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeEditModal()">&times;</span>
                <form method="post" action="admin_page.php">
                    <input type="hidden" name="edit_movie_id" id="edit_movie_id">
                    <label>Title:</label>
                    <input type="text" name="edit_movie_title" id="edit_movie_title" required><br><br>
                    <label>Price:</label>
                    <input type="number" name="edit_movie_price" id="edit_movie_price" step="0.01" required><br><br>
                    <label>Trailer URL:</label>
                    <input type="text" name="edit_trailer_url" id="edit_trailer_url" required><br><br>
                    <button type="submit" name="edit_movie" class="edit-btn">Save Changes</button>
                </form>
            </div>
        </div>

        <div id="editUpcomingMovieModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeEditUpcomingMovieModal()">&times;</span>
                <form method="post" action="admin_page.php">
                    <input type="hidden" name="edit_upcoming_movie_id" id="edit_upcoming_movie_id">

                    <label>Title:</label>
                    <input type="text" name="edit_upcoming_movie_title" id="edit_upcoming_movie_title" required><br><br>

                    <label>Trailer URL:</label>
                    <input type="text" name="edit_upcoming_movie_trailer" id="edit_upcoming_movie_trailer" required><br><br>

                    <button type="submit" name="edit_upcoming_movie" class="edit-btn">Save Changes</button>
                </form>
            </div>
        </div>

        <div id="deleteMovieErrorModal" class="modal" style="display: none;">
            <div class="modal-content">
                <span class="close" onclick="closeDeleteMovieErrorModal()">&times;</span>
                <p id="deleteMovieErrorMessage"></p>
                <button onclick="closeDeleteMovieErrorModal()">OK</button>
            </div>
        </div>

        <div id="logout">
            <button onclick="window.location.href='logout.php'">Logout</button>
        </div>
    </main>

    <script src="admin.js"></script>
</body>

</html>