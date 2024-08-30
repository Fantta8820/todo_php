<?php
include_once "../../connection.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['send'])) {
        unset($_SESSION['error']);
        $name = $_POST['name'];
        $password = $_POST['password'];
        $token = bin2hex(random_bytes(32));

        $selectQuery = $connection->prepare("SELECT * FROM users WHERE name = :name");
        $selectQuery->bindParam("name", $name);
        $selectQuery->execute();      

        if ($selectQuery->rowCount() === 1) {
            $userData = $selectQuery->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $userData['password'])) {
                $updateQuery = $connection->prepare("UPDATE users SET token = :token WHERE name = :name");
                $updateQuery->bindParam("name", $name);
                $updateQuery->bindParam("token", $token);
                if ($updateQuery->execute()) {

                    $userID = $userData['id_user'];
                    $tableName = "task" . $userID;                                        
                    
                    setcookie("status", "all", strtotime('+7days'), "/todo_php");
                    setcookie("userID", $userID, strtotime('+7days'), "/todo_php");
                    setcookie("tableName", $tableName, strtotime('+7days'), "/todo_php");
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
?>\