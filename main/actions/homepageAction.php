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
        $tableName = $_SESSION['task'];

        $insertQuery = $connection->prepare("INSERT INTO $tableName(task_name, task_description, isChecked, id_user) VALUES (:task_name, :task_description, :isChecked, :id_user)");
        $insertQuery->bindParam("task_name", $task_name);
        $insertQuery->bindParam("task_description", $task_description);
        $insertQuery->bindParam("isChecked", $isChecked);
        $insertQuery->bindParam("id_user", $_SESSION['userID']);
        $insertQuery->execute();

        header("Location: ../homepage.php");
    }

    if (isset($_POST['edit'])) {
        $task_name = $_POST['task_name'];
        $task_description = $_POST['task_description'];
        $id_task = $_POST['id_task'];
        $tableName = $_SESSION['task'];

        $updateQuery = $connection->prepare("UPDATE $tableName SET task_name = :task_name, task_description = :task_description WHERE id_task = :id_task");
        $updateQuery->bindParam("task_name", $task_name);
        $updateQuery->bindParam("task_description", $task_description);
        $updateQuery->bindParam("id_task", $id_task);
        $updateQuery->execute();

        header("Location: ../homepage.php");
    }

    if (isset($_POST['delete'])) {
        $id_task = $_POST['id_task'];
        $tableName = $_SESSION['task'];

        $deleteQuery = $connection->prepare("DELETE FROM $tableName WHERE id_task = :id_task");
        $deleteQuery->bindParam("id_task", $id_task);
        $deleteQuery->execute();

        header("Location: ../homepage.php");
    }
}
?>