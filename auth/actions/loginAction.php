<?php
include_once "../../connection.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['send'])) {
        unset($_SESSION['error']);
        $name = $_POST['name'];
        $password = $_POST['password'];
        $token = bin2hex(random_bytes(32));

        $selectQuery = $connection->prepare("SELECT password FROM users WHERE name = :name");
        $selectQuery->bindParam("name", $name);
        $selectQuery->execute();

        if ($selectQuery->rowCount() === 1) {
            if (password_verify($password, $selectQuery->fetch(PDO::FETCH_ASSOC)['password'])) {
                $updateQuery = $connection->prepare("UPDATE users SET token = :token WHERE name = :name");
                $updateQuery->bindParam("name", $name);
                $updateQuery->bindParam("token", $token);
                if ($updateQuery->execute()) {
                    setcookie("token", $token, strtotime('+7days'), "/todo_php");
                    header('Location: ../../main/homepage.php');
                };
            }else{
                $_SESSION['error'] = "Senha incorreta!";
                header('Location: ../login.php');
            }
        } else {
            $_SESSION['error'] = "Usuário não registrado!";
            header('Location: ../login.php');
        }
    }
}
?>