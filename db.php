<?php
$servername = "localhost";
$username = "root";
$password ="";
$dbname ="baza_de_date";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if(!$conn)
{
    die("Conexiune esuata: ". mysqli_connect_error());
}

session_start();
        ?>