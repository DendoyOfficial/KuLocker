<?php
session_start();

require 'connection.php';

if(isset($_POST['signin'])){

    $username = mysqli_real_escape_string(
        $conn,
        $_POST['username']
    );

    $password = $_POST['password'];

    $query = mysqli_query(

        $conn,

        "SELECT * FROM users
         WHERE username = '$username'
         LIMIT 1"

    );

    if(mysqli_num_rows($query) > 0){

        $user = mysqli_fetch_assoc($query);

        if(password_verify(
            $password,
            $user['password']
        )){

            $_SESSION['user'] = [

                'id'        => $user['id'],
                'full_name' => $user['full_name'],
                'username'  => $user['username'],
                'nim'       => $user['nim']

            ];

            header(
                "Location: ../dashboard-utama.php"
            );

            exit;

        } else {

            $_SESSION['error'] =
                "Password salah!";


            header(
                "Location: ../sign-in.php"
            );

            exit;

        }

    } else {

        $_SESSION['error'] =
            "Username tidak ditemukan!";

        header(
            "Location: ../sign-in.php"
        );

        exit;

    }

} else {

    header(
        "Location: ../sign-in.php"
    );

    exit;

}