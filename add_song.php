<?php

include('db.php');

if (!isset($_SESSION['user'])) {
    header("Location: login-reg.php");
    exit;
}


if (isset($_GET['playlist_name'])) {
    $playlist_name = $_GET['playlist_name'];

    
    $user_name = $_SESSION['user'];
    $query_user_id = "SELECT ID_User FROM users WHERE name = '$user_name'";
    $result_user_id = mysqli_query($conn, $query_user_id);

    if (mysqli_num_rows($result_user_id) > 0) {
        $user = mysqli_fetch_assoc($result_user_id);
        $user_id = $user['ID_User'];

        
        $query_playlist_id = "SELECT ID_Playlist FROM playlists WHERE ID_User = '$user_id' AND PName = '$playlist_name'";
        $result_playlist_id = mysqli_query($conn, $query_playlist_id);

        if (mysqli_num_rows($result_playlist_id) > 0) {
            $playlist = mysqli_fetch_assoc($result_playlist_id);
            $playlist_id = $playlist['ID_Playlist'];

            
            $query_songs = "SELECT * FROM songs";
            $result_songs = mysqli_query($conn, $query_songs);

            if (mysqli_num_rows($result_songs) > 0) {
                $songs = mysqli_fetch_all($result_songs, MYSQLI_ASSOC);
            } else {
                echo "<script>showNotification('Error: No songs found.', 'error');</script>";

                exit;
            }
        } else {
            echo "<script>showNotification('Error: Playlist not found.', 'error');</script>";
            exit;
        }
    } else {
        echo "<script>showNotification('Error: User not found.', 'error');</script>";
        exit;
    }
} else {
    echo "<script>showNotification('Error: Playlist parameter missing.', 'error');</script>";
    exit;
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Add Song to Playlist</title>
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

        .btn {
            padding: 10px 20px;
            background-color: #64e8ce;
            color: #2c3e50;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            display: inline-block;
        }

        .btn:hover {
            background-color:  #64e8ce;
        }
        .song {
        padding: 10px;
        background-color: #280a50;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        margin: 10px 0;
        cursor: pointer;
        text-align: left;
        color: #fff; 
        }

        .song label {
            color: #fff; 
            display: block;
            margin-left: 25px; 
        }

        .song input[type="radio"] {
            margin-right: 10px;
            vertical-align: middle;
        }
        .notification-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #f44336;
            text-align: center;
            padding: 10px;
            display: none; 
            z-index: 1000; 
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Adăugat pentru efect de umbră */
            color: #fff;
        }

        .notification-success {
            background-color: #4CAF50; 
        }

        .notification-error {
            background-color: #f44336; 
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
                <h1>Select a Song to Add</h1>
                <form method="post" action="add_song_to_playlist.php">
                <input type="hidden" name="playlist_id" value="<?php echo $playlist_id; ?>">
                <input type="hidden" name="playlist_name" value="<?php echo $playlist_name; ?>">
                <ul>
                    <?php
                    if (!empty($songs)) {
                        foreach ($songs as $song) {
                            echo '<li class="song">';
                            echo '<input type="radio" name="song_id" value="' . $song['ID_Song'] . '"> ' . $song['SName'];
                            echo '</li>';
                        }
                    } else {
                        echo "<li>No songs available.</li>";
                    }
                    ?>
                </ul>
                    <button type="submit" class="btn">Add Song</button>
                </form>
            </div>
        </main>
        
        <div id="notification-bar" class="notification-bar"></div>

    </div>
    <script>
        function showNotification(message, type) {
            var notificationBar = document.getElementById('notification-bar');
            notificationBar.innerHTML = message;
            notificationBar.classList.add('notification-' + type);
            notificationBar.style.display = 'block';
            setTimeout(function() {
                notificationBar.style.display = 'none';
                notificationBar.classList.remove('notification-' + type);
            }, 3000); 
        }
    </script>
      <?php
            if (isset($_SESSION['notification'])) {
                $notification = $_SESSION['notification'];
                echo "<script>showNotification('{$notification['message']}', '{$notification['type']}');</script>";
                unset($_SESSION['notification']);
            }
            ?>
    
</body>
</html>