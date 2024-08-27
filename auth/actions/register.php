<?php
include_once "../../connection.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['send'])) {
        $name = $_POST['name'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $selectQuery = $connection->prepare("SELECT * FROM users WHERE name = :name");
        $selectQuery->bindParam("name", $name);
        $selectQuery->execute();

        if ($selectQuery->rowCount() === 0) {
            $insertQuery = $connection->prepare("INSERT INTO users(name, password) VALUES (:name, :pass)");
            $insertQuery->bindParam("name", $name);
            $insertQuery->bindParam("pass", $password);
            if ($insertQuery->execute()) {
                header('Location: ../../main/homepage.html');
            }
            ;
        }else{        
            $_SESSION['error'] = "Usuário já registrado!";
            header('Location: ../register.php');
        }
    }
}
?>