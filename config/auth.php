<?php
session_start();

if(!isset($_SESSION['user'])){

    header("Location: sign-in.php");
    exit;
}