<?php

include('db.php');

if (!isset($_SESSION['user'])) {
    header("Location: login-reg.php");
    exit;
}

$playlist_name='';
$songs = [];
$audio_paths=[];

if(isset($_GET['playlist'])) {
    $playlist_name = $_GET['playlist'];
    
   
    $user_name = $_SESSION['user'];
    $query_user_id = "SELECT ID_User FROM users WHERE name = '$user_name'";
    $result_user_id = mysqli_query($conn, $query_user_id);

    if ( $result_user_id && mysqli_num_rows($result_user_id) > 0) {
        $user = mysqli_fetch_assoc($result_user_id);
        $user_id = $user['ID_User'];

        
        $query_playlist_id = "SELECT ID_Playlist FROM playlists WHERE ID_User = '$user_id' AND PName = '$playlist_name'";
        $result_playlist_id = mysqli_query($conn, $query_playlist_id);

        if ($result_playlist_id && mysqli_num_rows($result_playlist_id) > 0) {
            $playlist = mysqli_fetch_assoc($result_playlist_id);
            $playlist_id = $playlist['ID_Playlist'];

            $query_songs = "SELECT songs.SName FROM songs 
            JOIN playlist_line ON songs.ID_Song = playlist_line.ID_Song 
            WHERE playlist_line.ID_Playlist = '$playlist_id'";
            $result_songs = mysqli_query($conn, $query_songs);

            if ($result_songs && mysqli_num_rows($result_songs) > 0) {
                while ($song = mysqli_fetch_assoc($result_songs)) {
                    $songs[] = $song['SName'];
                }
            }
        }  else {
                   die ("Error: Playlist not found.");
                }
            } else {
                die("Error: User not found.");
            }
        } else {
            die("Error: Playlist parameter missing.");
        }

        $query_audio_paths = "SELECT file_path FROM songs JOIN playlist_line ON songs.ID_Song = playlist_line.ID_Song WHERE playlist_line.ID_Playlist = '$playlist_id'";
         $result_audio_paths = mysqli_query($conn, $query_audio_paths);

  
    if ($result_audio_paths && mysqli_num_rows($result_audio_paths) > 0) {
        
        $audio_paths = array();

        
        while ($row = mysqli_fetch_assoc($result_audio_paths)) {
            $audio_paths[] = $row['file_path'];
        }
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title><?php echo $playlist_name; ?> - Playlist Details</title>
    
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
    display: block;
    margin-top: 10px;
    
}

.song {
    padding: 5px;
    background-color: #f1f1f1;
    border-radius: 5px;
    margin: 5px 0;
    text-align: left;
}

.btn {
    display: inline-block;
    padding: 10px 20px;
    margin: 20px 0;
    background-color: #fff;
    color: #2c3e50;
    text-decoration: none;
    border-radius: 5px;
    transition: background-color 0.3s ease;
}

.btn:hover {
    background-color: #64e8ce;
}

.play-button {
    background-color: transparent;
    border: none;
    cursor: pointer;
    margin-right: 10px;
}

.play-button i {
    font-size: 20px;
    color: #2c3e50;
}

.play-button:hover i {
    color: #64e8ce;
}
.audio-control {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background-color:  #ecf0f1;
    border-top: 1px solid #ccc;
    display: flex;
    flex-direction: column;
    align-items: center;
   
    padding: 10px 0;
}


.audio-info {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 20px;
    margin-bottom: 10px;
    overflow: hidden;
}

.audio-info .song-title {
    text-align: center;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-bottom: 10px;
        }

.audio-buttons {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 10px;
}


.audio-buttons button {
    background-color: transparent;
    border: none;
    cursor: pointer;
    outline: none;
    margin: 0 5px;
}
.audio-progress-container {
    width: 100%;
    padding: 0 20px;
    box-sizing: border-box;
}

.audio-progress {
    flex-grow: 5;
    height: 5px;
    background-color: #ddd;
    /*margin: 0 10px;*/
    position: relative;
}

.progress-bar {
    height: 100%;
    background-color: #007bff;
    width: 0;
    position: absolute;
    top: 0;
    left: 0;
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
             <h1><?php echo $playlist_name; ?></h1>
             
                 
            <div class="songs">
                <ul>
                    <?php
                        
                        if (!empty($songs) && !empty($audio_paths)) {
                            for ($i = 0; $i < count($songs); $i++) {
                                echo "<li class='song'>";
                                echo "<button class='play-button' data-audio='" . htmlspecialchars($audio_paths[$i]) . "' data-title='" . htmlspecialchars($songs[$i]) . "'><i class='bx bx-play'></i></button>";
                                echo "<span>" . htmlspecialchars($songs[$i]) . "</span>";
                                echo "</li>";
                            }
                        } else {
                            echo "<li class='song'>No songs found in this playlist.</li>";
                        }
                        ?>
                    </ul>
            
            </div>
            <a href="add_song.php?playlist_name=<?php echo urlencode($playlist_name); ?>" class="btn">Add Song</a>
            <div class="audio-control">
        <div class="audio-info">
            <span class="song-title"></span>
        </div>
        <div class="audio-progress-container">
            <div class="audio-progress">
                <div class="progress-bar"></div>
            </div>
            <span class="song-duration">00:00 / 00:00</span>
        </div>
        <div class="audio-buttons">
            <button class="previous-button"><i class='bx bx-skip-previous'></i></button>
            <button class="play-pause-button"><i class='bx bx-play'></i></button>
            <button class="next-button"><i class='bx bx-skip-next'></i></button>
        </div>
    </div>

    <audio id="common-audio">
        <source src="" type="audio/ogg">
    </audio>
           

        </main>

        
    </div>

    
    
   
<script>
   document.addEventListener('DOMContentLoaded', function() {
            const playButtons = document.querySelectorAll('.play-button');
            const audio = document.getElementById('common-audio');
            const songTitle = document.querySelector('.song-title');
            const playPauseButton = document.querySelector('.play-pause-button');
            const nextButton = document.querySelector('.next-button');
            const previousButton = document.querySelector('.previous-button');
            const progressBar = document.querySelector('.progress-bar');
            const songDuration = document.querySelector('.song-duration');
            let currentSongIndex = -1;
            let isPlaying = false;
            let songs = [];

            playButtons.forEach((button, index) => {
                button.addEventListener('click', function() {
                    const audioSrc = this.getAttribute('data-audio');
                    const title = this.getAttribute('data-title');

                    currentSongIndex = index;
                    audio.src = audioSrc;
                    audio.play();
                    songTitle.textContent = title;
                    isPlaying = true;
                    playPauseButton.innerHTML = "<i class='bx bx-pause'></i>";
                });

                songs.push({
                    src: button.getAttribute('data-audio'),
                    title: button.getAttribute('data-title')
                });
            });

            playPauseButton.addEventListener('click', function() {
                if (!isPlaying) {
                    audio.play();
                    isPlaying = true;
                    playPauseButton.innerHTML = "<i class='bx bx-pause'></i>";
                } else {
                    audio.pause();
                    isPlaying = false;
                    playPauseButton.innerHTML = "<i class='bx bx-play'></i>";
                }
            });

            nextButton.addEventListener('click', function() {
                if (currentSongIndex < songs.length - 1) {
                    currentSongIndex++;
                    audio.src = songs[currentSongIndex].src;
                    audio.play();
                    songTitle.textContent = songs[currentSongIndex].title;
                    isPlaying = true;
                    playPauseButton.innerHTML = "<i class='bx bx-pause'></i>";
                }
            });

            previousButton.addEventListener('click', function() {
                if (currentSongIndex > 0) {
                    currentSongIndex--;
                    audio.src = songs[currentSongIndex].src;
                    audio.play();
                    songTitle.textContent = songs[currentSongIndex].title;
                    isPlaying = true;
                    playPauseButton.innerHTML = "<i class='bx bx-pause'></i>";
                }
            });

            audio.addEventListener('timeupdate', function() {
                const progress = (audio.currentTime / audio.duration) * 100;
                progressBar.style.width = progress + '%';
                songDuration.textContent = formatTime(audio.currentTime) + ' / ' + formatTime(audio.duration);
            });

            audio.addEventListener('ended', function() {
                if (currentSongIndex < songs.length - 1) {
                    currentSongIndex++;
                    audio.src = songs[currentSongIndex].src;
                    audio.play();
                    songTitle.textContent = songs[currentSongIndex].title;
                } else {
                    isPlaying = false;
                    playPauseButton.innerHTML = "<i class='bx bx-play'></i>";
                }
            });

            function formatTime(time) {
                let minutes = Math.floor(time / 60);
                let seconds = Math.floor(time % 60);
                minutes = (minutes < 10) ? '0' + minutes : minutes;
                seconds = (seconds < 10) ? '0' + seconds : seconds;
                return minutes + ':' + seconds;
            }
        });
        
</script>
   
</body>
</html>
