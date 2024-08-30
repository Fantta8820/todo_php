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

                $selectIdQuery = $connection->prepare("SELECT id_user FROM users WHERE name = :name");
                $selectIdQuery->bindParam("name", $name);
                $selectIdQuery->execute();

                $userData = $selectIdQuery->fetch(PDO::FETCH_ASSOC);
                $userID = $userData['id_user'];
                $tableName = "task" . $userID;                                

                $sqlTable = "CREATE TABLE IF NOT EXISTS $tableName ( id_task INT AUTO_INCREMENT PRIMARY KEY, task_name VARCHAR(255) NOT NULL, task_description TEXT NOT NULL, isChecked BOOLEAN NOT NULL DEFAULT FALSE, id_user INT NOT NULL, FOREIGN KEY (id_user) REFERENCES users(id_user))";

                $createQuery = $connection->prepare($sqlTable);                
                $createQuery->execute();

                setcookie("userID", $userID, strtotime('+7days'), "/todo_php");
                setcookie("tableName", $tableName, strtotime('+7days'), "/todo_php");
                setcookie("token", $token, strtotime('+7days'), "/todo_php");
                header('Location: ../../main/homepage.php');
            }
            ;
        } else {
            $_SESSION['error'] = "Usuário já registrado!";
            header('Location: ../register.php');
        }
    }
}
?>