<?php
try {
    $connection = new PDO('mysql:host=localhost;dbname=todophp', 'root', '');
} catch (PDOException $e) {
    echo 'An error has been encountered' . $e->getMessage();
}
?>