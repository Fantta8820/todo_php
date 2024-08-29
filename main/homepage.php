<!DOCTYPE html>
<html lang="pt-BR" data-theme="night">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TODO - Homepage</title>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<script>
    tailwind.config = {
        daisyui: {
            themes: ['night'],
        },
    }        
</script>

<body>
    <?php
    session_start();
    include_once "../connection.php";

    if (!isset($_COOKIE['token'])) {
        header('Location: ../auth/login.php');
    }

    $selectQuery = $connection->prepare("SELECT * FROM users WHERE token = :token");
    $selectQuery->bindParam("token", $_COOKIE['token']);
    $selectQuery->execute();

    if ($selectQuery->rowCount() === 0) {
        $_SESSION['error'] = "";
        setcookie("token", $_COOKIE['token'], strtotime('-7days'), "/todo_php");
        header('Location: ../auth/login.php');
    }
    ?>
    <main class="w-screen h-screen grid place-items-center">
        <section class="w-1/2 h-3/4 flex flex-col items-center bg-white rounded-xl">
            <h1 class="text-center text-3xl font-bold text-black pt-8">TODO LIST</h1>
            <div class="pt-4 flex gap-4">

                <label
                    class="input input-bordered flex items-center gap-2 bg-white text-black w-96 h-8 rounded-lg border border-secondary border-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                        class="h-4 w-4 opacity-70">
                        <path fill-rule="evenodd"
                            d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"
                            clip-rule="evenodd" />
                    </svg>
                    <input type="text" class="grow" placeholder="Search" />
                </label>

                <button class="btn btn-primary btn-sm w-32">Todos</button>

                <button class="btn btn-primary btn-sm w-32" onclick="my_modal_2.showModal()">Adicionar tarefa</button>
                <dialog id="my_modal_2" class="modal">
                    <div class="modal-box w-full flex flex-col items-center">
                        <form method="POST" action="actions/homepageAction.php">
                            <h3 class="text-lg font-bold text-center font-bold">Adicionar Tarefa</h3>
                            <input
                                class="input input-bordered flex items-center gap-2 bg-white text-black w-96 h-8 rounded-lg border border-secondary border-2 mt-4"
                                placeholder="Nome da tarefa" name="task_name" required>
                            <input
                                class="input input-bordered flex items-center gap-2 bg-white text-black w-96 h-8 rounded-lg border border-secondary border-2 mt-4"
                                placeholder="Descrição da tarefa" name="task_description" required>
                            <button class="btn btn-secondary btn-sm w-full mt-4" name="add">Adicionar</button>
                        </form>
                    </div>
                    <form method="dialog" class="modal-backdrop">
                        <button>Fechar</button>
                    </form>
                </dialog>

            </div>
            <main class="w-2/3 pt-8">
                <h1 class="text-center text-black text-xl font-semibold">Tarefas Pendentes</h1>
                <div class="divider divider-secondary"></div>
                <?php
                $tableName = $_SESSION['task'];

                $selectTaskQuery = $connection->prepare("SELECT * FROM $tableName");
                $selectTaskQuery->execute();

                while ($taskInfo = $selectTaskQuery->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <label class="flex justify-between pt-4">
                        <section class="flex items-center gap-4">
                            <input type="checkbox" class="checkbox checkbox-primary" />
                            <p class="text-xl text-black font-bold"><?php echo $taskInfo['task_name'] ?></p>
                        </section>
                        <section class="flex gap-4">
                            <button class="btn btn-primary btn-sm w-24"
                                onclick="<?php echo "my_modal_view" . $taskInfo['id_task'] ?>.showModal()">Visualizar</button>

                            <dialog id="<?php echo "my_modal_view" . $taskInfo['id_task'] ?>" class="modal">
                                <div class="modal-box w-full flex flex-col items-center">
                                    <main>
                                        <h1 class="text-center text-2xl font-bold">Informações da Tarefa</h1>
                                        <h1 class="text-center text-xl pt-8 font-semibold">Título</h1>
                                        <p class="text-base text-center"><?php echo $taskInfo['task_name'] ?></p>
                                        <div class="divider divider-secondary"></div>
                                        <h1 class="text-center text-xl font-semibold">Descrição</h1>
                                        <p class="text-base text-justify"><?php echo $taskInfo['task_description'] ?></p>
                                    </main>
                                </div>
                                <form method="dialog" class="modal-backdrop">
                                    <button>Fechar</button>
                                </form>
                            </dialog>

                            <button class="btn btn-primary btn-sm w-24"
                                onclick="<?php echo "my_modal_edit" . $taskInfo['id_task'] ?>.showModal()">Editar</button>

                            <dialog id="<?php echo "my_modal_edit" . $taskInfo['id_task'] ?>" class="modal">
                                <div class="modal-box w-full flex flex-col items-center">
                                    <form method="POST" action="actions/homepageAction.php">
                                        <h3 class="text-lg font-bold text-center font-bold">Editar Tarefa</h3>
                                        <input
                                            class="input input-bordered flex items-center gap-2 bg-white text-black w-96 h-8 rounded-lg border border-secondary border-2 mt-4"
                                            placeholder="Nome da Tarefa" name="task_name"
                                            value="<?php echo $taskInfo['task_name'] ?>" required>
                                        <input
                                            class="input input-bordered flex items-center gap-2 bg-white text-black w-96 h-8 rounded-lg border border-secondary border-2 mt-4"
                                            placeholder="Descrição da tarefa" name="task_description"
                                            value="<?php echo $taskInfo['task_description'] ?>" required>
                                        <input type="hidden" name="id_task" value="<?php echo $taskInfo['id_task'] ?>">
                                        <button class="btn btn-secondary btn-sm w-full mt-4" name="edit">Editar</button>
                                    </form>
                                </div>
                                <form method="dialog" class="modal-backdrop">
                                    <button>Fechar</button>
                                </form>
                            </dialog>

                            <form method="POST" action="actions/homepageAction.php">
                                <input type="hidden" name="id_task" value="<?php echo $taskInfo['id_task'] ?>">
                                <button class="btn btn-error btn-sm w-24" name="delete">Deletar</button>
                            </form>
                        </section>
                    </label>
                    <?php
                }
                ?>
                <h1 class="text-center text-black text-xl font-semibold pt-8">Tarefas Finalizadas</h1>
                <div class="divider divider-secondary"></div>
            </main>
            <form method="POST" action="actions/homepageAction.php">
                <button class="btn btn-primary mt-24" name="send">Deslogar</button>
            </form>
        </section>
    </main>
</body>

</html>