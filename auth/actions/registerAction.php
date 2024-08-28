<?php
include_once "../../connection.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['send'])) {
        $name = $_POST['name'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $token = bin2hex(random_bytes(32));        

        $selectQuery = $connection->prepare("SELECT * FROM users WHERE name = :name");
        $selectQuery->bindParam("name", $name);
        $selectQuery->execute();

        if ($selectQuery->rowCount() === 0) {
            $insertQuery = $connection->prepare("INSERT INTO users(name, password, token) VALUES (:name, :pass, :token)");
            $insertQuery->bindParam("name", $name);
            $insertQuery->bindParam("pass", $password);
            $insertQuery->bindParam("token", $token);
            if ($insertQuery->execute()) {                
                setcookie("token", $token, strtotime('+7days'), "/todo_php");
                header('Location: ../../main/homepage.php');
            }
            ;
        }else{        
            $_SESSION['error'] = "Usuário já registrado!";
            header('Location: ../register.php');
        }
    }
}
?>