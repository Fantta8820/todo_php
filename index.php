<?php
    session_start();
    $_SESSION['error'] = "";
    header('Location: auth/login.php');
?>