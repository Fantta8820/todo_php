<?php
if (!isset($_COOKIE['token'])) {
    header('Location: ../auth/login.php');
}
?>