<?php

include('db.php');

if (!isset($_SESSION['user'])) {
    header("Location: login-rec.php");
    exit;
}

$user_name = $_SESSION['user'];


$query = "SELECT ID_User FROM users WHERE name = '$user_name'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    $user_id = $user['ID_User'];

    
    $query_playlists = "SELECT * FROM playlists WHERE ID_User = '$user_id'";
    $result_playlists = mysqli_query($conn, $query_playlists);
} else {
    echo "Error: User not found.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Playlists</title>
    <style>
        
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap');

body, html {
    margin: 0;
    padding: 0;
    font-family: 'Montserrat', sans-serif;
    background-color: #ecf0f1;
    color: #2c3e50;
}

.container {
    display: flex;
    min-height: 100vh;
}

.sidebar {
    width: 250px;
    background-color: #280a50;
    color: #ecf0f1;
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px 0;
    box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
}

.logo {
    display: flex;
    align-items: center;
    margin-bottom: 30px;
}

.logo i {
    font-size: 28px;
    margin-right: 10px;
}

.logo span {
    font-size: 22px;
    font-weight: bold;
}

.menu {
    list-style: none;
    padding: 0;
    width: 100%;
}

.menu li {
    width: 100%;
}

.menu a {
    color: #ecf0f1;
    text-decoration: none;
    display: flex;
    align-items: center;
    padding: 15px 20px;
    transition: background-color 0.3s ease;
}

.menu a:hover {
    background-color: #64e8ce;
}

.menu a i {
    font-size: 20px;
    margin-right: 15px;
}

.main-content {
    flex-grow: 1;
    padding: 40px;
    background-color: #8e68b8;
    display: flex;
    justify-content: center;
    align-items: center;
}

.custom-welcome {
    text-align: center;
    max-width: 600px;
    padding: 20px;
    background: #280a50;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.custom-welcome h1 {
    font-size: 32px;
    margin-bottom: 20px;
    color: #ffffff;
}

.custom-welcome p {
    font-size: 18px;
    color: #7f8c8d;
}
.playlist {
            margin: 10px 0;
            padding: 10px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            cursor: pointer;
        }

        .playlist h3 {
            margin: 0;
            color: #2c3e50;
        }

        .songs {
            display: none;
            margin-top: 10px;
        }

        .song {
            padding: 5px;
            background-color: #f1f1f1;
            border-radius: 5px;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="logo">
                <i class='bx bx-music'></i>
                <span>MusicPlayer</span>
            </div>
            <ul class="menu">
                <li><a href="playlists.php"><i class='bx bxs-playlist'></i>Playlists</a></li>
                <li><a href="new_playlist.php"><i class='bx bx-plus'></i> New Playlist</a></li>
                <li><a href="account.php"><i class='bx bx-user'></i> My Account</a></li>
                <li><a href="logout.php"><i class='bx bx-log-out'></i> Log Out</a></li>
            </ul>
        </aside>
        <main class="main-content">
            <div class="custom-welcome">
                <h1>Your Playlists</h1>
                <div id="playlists">
                   
                <?php
                if (mysqli_num_rows($result_playlists) > 0) {
                    while ($playlist = mysqli_fetch_assoc($result_playlists)) {
                        
                        echo '<div class="playlist">';
                        echo '<a href="playlist_details.php?playlist=' . urlencode($playlist['PName']) . '">';

                        echo '<h3>' . $playlist['PName'] . '</h3>';
                        echo '</a>';
                        echo '</div>';
                    }
                } else {
                    echo "You don't have any playlists yet.";
                }
                ?>
                </div>
            </div>
        </main>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const playlists = document.querySelectorAll('.playlist h3');
            playlists.forEach(playlist => {
                playlist.addEventListener('click', () => {
                    const songs = playlist.nextElementSibling;
                    songs.style.display = songs.style.display === 'none' ? 'block' : 'none';
                });
            });
        });
    </script>
</body>
</html>
