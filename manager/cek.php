<!-- kalo user belum login, maka akan dikembalikan ke login.php -->

<?php
    if(!isset($_SESSION['username'])){
        header("location: /login.php");
    }
?>