<?php

include('db.php');

if (!isset($_SESSION['user'])) {
    header("Location: login-reg.php");
    exit;
}

if (isset($_POST['playlist_id']) && isset($_POST['song_id']) && isset($_POST['playlist_name'])) {
    $playlist_id = $_POST['playlist_id'];
    $song_id = $_POST['song_id'];
    $playlist_name = $_POST['playlist_name'];

    echo "Playlist ID: " . $playlist_id . "<br>";
    echo "Song ID: " . $song_id . "<br>";

   
    $query_check = "SELECT * FROM playlist_line WHERE ID_Playlist = '$playlist_id' AND ID_Song = '$song_id'";
    $result_check = mysqli_query($conn, $query_check);

    if (mysqli_num_rows($result_check) == 0) {
       
        $query_add_song = "INSERT INTO playlist_line (ID_Playlist, ID_Song) VALUES ('$playlist_id', '$song_id')";
        if (mysqli_query($conn, $query_add_song)) {
            $_SESSION['notification'] = array('type' => 'success', 'message' => 'Song added successfully.');
        } else {
            $_SESSION['notification'] = array('type' => 'error', 'message' => 'Error: ' . mysqli_error($conn));
        }
    } else {
        $_SESSION['notification'] = array('type' => 'error', 'message' => 'The song is already in the playlist.');
    }

    

} else {
    $_SESSION['notification'] = array('type' => 'error', 'message' => 'Error: Missing playlist or song ID.');
}



header("Location: add_song.php?playlist_name=" . urlencode($playlist_name)); // Redirecționează către pagina anterioară
exit;
?>
