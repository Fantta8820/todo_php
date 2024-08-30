<?php
include_once "../../connection.php";
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['send'])) {
        $_SESSION['error'] = "";
        setcookie("token", $_COOKIE['token'], strtotime('-7days'), "/todo_php");
        header('Location: ../homepage.php');
    }

    if (isset($_POST['add'])) {
        $task_name = $_POST['task_name'];
        $task_description = $_POST['task_description'];
        $isChecked = 0;
        $tableName = $_COOKIE['tableName'];
        $userID = $_COOKIE['userID'];

        $insertQuery = $connection->prepare("INSERT INTO $tableName(task_name, task_description, isChecked, id_user) VALUES (:task_name, :task_description, :isChecked, :id_user)");
        $insertQuery->bindParam("task_name", $task_name);
        $insertQuery->bindParam("task_description", $task_description);
        $insertQuery->bindParam("isChecked", $isChecked);
        $insertQuery->bindParam("id_user", $userID);
        $insertQuery->execute();

        header("Location: ../homepage.php");
    }

    if (isset($_POST['edit'])) {
        $task_name = $_POST['task_name'];
        $task_description = $_POST['task_description'];
        $id_task = $_POST['id_task'];
        $tableName = $_COOKIE['tableName'];

        $updateQuery = $connection->prepare("UPDATE $tableName SET task_name = :task_name, task_description = :task_description WHERE id_task = :id_task");
        $updateQuery->bindParam("task_name", $task_name);
        $updateQuery->bindParam("task_description", $task_description);
        $updateQuery->bindParam("id_task", $id_task);
        $updateQuery->execute();

        header("Location: ../homepage.php");
    }

    if (isset($_POST['delete'])) {
        $id_task = $_POST['id_task'];
        $tableName = $_COOKIE['tableName'];

        $deleteQuery = $connection->prepare("DELETE FROM $tableName WHERE id_task = :id_task");
        $deleteQuery->bindParam("id_task", $id_task);
        $deleteQuery->execute();        

        $selectQuery = $connection->prepare("SELECT id_task FROM $tableName");
        $selectQuery->execute();
    
        $count = 1;

        while ($task = $selectQuery->fetch(PDO::FETCH_ASSOC)) {
            $updateQuery = $connection->prepare("UPDATE $tableName SET id_task = :count WHERE id_task = :task");
            $updateQuery->bindParam("count", $count);
            $updateQuery->bindParam("task", $task['id_task']);
            $updateQuery->execute();
            $count++;
        }
        
        $resetAutoIncrement = $connection->prepare("ALTER TABLE $tableName AUTO_INCREMENT = 1");
        $resetAutoIncrement->execute();

        header("Location: ../homepage.php");
    }

    if (isset($_POST['check'])) {
        $id_task = $_POST['id_task'];
        $tableName = $_COOKIE['tableName'];

        $selectTask = $connection->prepare("SELECT isChecked FROM $tableName WHERE id_task = :id_task");
        $selectTask->bindParam("id_task", $id_task);
        $selectTask->execute();

        $isChecked = $selectTask->fetch(PDO::FETCH_ASSOC);

        if ($isChecked['isChecked'] === 0) {
            $checked = 1;

            $updateTaskQuery = $connection->prepare("UPDATE $tableName SET isChecked = :checked WHERE id_task = :id_task");
            $updateTaskQuery->bindParam("checked", $checked);
            $updateTaskQuery->bindParam("id_task", $id_task);
            $updateTaskQuery->execute();

            header("Location: ../homepage.php");
        } else {
            $checked = 0;

            $updateTaskQuery = $connection->prepare("UPDATE $tableName SET isChecked = :checked WHERE id_task = :id_task");
            $updateTaskQuery->bindParam("checked", $checked);
            $updateTaskQuery->bindParam("id_task", $id_task);
            $updateTaskQuery->execute();

            header("Location: ../homepage.php");
        }
    }
}
?>