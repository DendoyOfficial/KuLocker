<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "Kulocker";

$conn = mysqli_connect(
    $host,
    $user,
    $pass,
    $db
);

if(!$conn){

    die(
        "Koneksi database gagal : " .
        mysqli_connect_error()
    );
}

mysqli_query($conn, "SET time_zone = '+08:00'");

