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
        setcookie("status", $_COOKIE['status'], strtotime('-7days'), "/todo_php");
        setcookie("userID", $_COOKIE['userID'], strtotime('-7days'), "/todo_php");
        setcookie("tableName", $_COOKIE['tableName'], strtotime('-7days'), "/todo_php");
        setcookie("token", $_COOKIE['token'], strtotime('-7days'), "/todo_php");
        header('Location: ../auth/login.php');
    }
    ?>
    <main class="w-screen h-screen grid place-items-center">
        <section class="w-1/2 h-3/4 flex flex-col items-center bg-white rounded-xl overflow-y-auto pb-12">
            <h1 class="text-center text-3xl font-bold text-black pt-8">TODO LIST</h1>
            <div class="pt-4 flex gap-4">

                <form method="POST" action="actions/homepageAction.php">
                    <label
                        class="input input-bordered flex items-center gap-2 bg-secondary text-white w-96 h-8 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                            class="h-4 w-4 opacity-70">
                            <path fill-rule="evenodd"
                                d="M9.965 11.026a5 5 0 1 1 1.06-1.06l2.755 2.754a.75.75 0 1 1-1.06 1.06l-2.755-2.754ZM10.5 7a3.5 3.5 0 1 1-7 0 3.5 3.5 0 0 1 7 0Z"
                                clip-rule="evenodd" />
                        </svg>
                        <input type="text" class="grow" placeholder="Pesquisar" name="search" />
                    </label>
                    <button name="result" class="hidden"></button>
                </form>

                <button class="btn btn-primary btn-sm w-32" onclick="my_modal_2.showModal()">Adicionar tarefa</button>
                <dialog id="my_modal_2" class="modal">
                    <div class="modal-box w-full flex flex-col items-center">
                        <form method="POST" action="actions/homepageAction.php">
                            <h3 class="text-lg font-bold text-center font-bold">Adicionar Tarefa</h3>
                            <input
                                class="input input-bordered flex items-center gap-2 bg-white text-black w-96 h-8 rounded-lg border border-secondary border-2 mt-4"
                                placeholder="Nome da tarefa" name="task_name" maxlength="200" required>
                            <input
                                class="input input-bordered flex items-center gap-2 bg-white text-black w-96 h-8 rounded-lg border border-secondary border-2 mt-4"
                                placeholder="Descrição da tarefa" name="task_description" maxlength="200" required>
                            <button class="btn btn-secondary btn-sm w-full mt-4" name="add">Adicionar</button>
                        </form>
                    </div>
                    <form method="dialog" class="modal-backdrop">
                        <button>Fechar</button>
                    </form>
                </dialog>

                <details class="dropdown">
                    <summary class="btn btn-primary btn-sm">Filtro</summary>
                    <ul class="menu dropdown-content bg-secondary rounded-box z-[1] w-52 p-2 shadow gap-y-4">
                        <form method="POST" action="actions/homepageAction.php">
                            <li><button class="btn btn-secondary bg-transparent border border-transparent"
                                    name="all">Todas as Tarefas</button></li>
                            <li><button class="btn btn-secondary bg-transparent border border-transparent"
                                    name="pending">Tarefas Pendentes</button></li>
                            <li><button class="btn btn-secondary bg-transparent border border-transparent"
                                    name="completed">Tarefas Finalizadas</button></li>
                        </form>
                    </ul>
                </details>

                <form method="POST" action="actions/homepageAction.php">
                    <button class="btn btn-sm btn-primary" name="send">Deslogar</button>
                </form>
            </div>
            <main class="w-2/3 pt-8">
                <?php
                $tableName = $_COOKIE['tableName'];

                $selectUncheckedQuery = $connection->prepare("SELECT * FROM $tableName WHERE isChecked = 0");
                $selectUncheckedQuery->execute();

                if ($_COOKIE['status'] === "all" || $_COOKIE['status'] === "pending") {
                    ?>
                    <h1 class="text-center text-black text-xl font-semibold">Tarefas Pendentes</h1>
                    <div class="divider divider-secondary"></div>
                    <?php
                    if ($selectUncheckedQuery->rowCount() === 0) {
                        ?>
                        <h1 class="text-center text-black text-lg pb-12">Nenhuma tarefa pendente</h1>
                        <?php
                    } else {
                        while ($taskInfo = $selectUncheckedQuery->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                            <label class="flex justify-between pt-4 pb-6">
                                <section class="flex gap-4">
                                    <form method="POST" action="actions/homepageAction.php">
                                        <input type="hidden" name="id_task" value="<?php echo $taskInfo['id_task'] ?>">

                                        <button name="check"><svg xmlns="http://www.w3.org/2000/svg" width="2.5em" height="2.5em"
                                                viewBox="0 0 24 24">
                                                <path fill="#38bdf8"
                                                    d="M3 6.25A3.25 3.25 0 0 1 6.25 3h11.5A3.25 3.25 0 0 1 21 6.25v11.5A3.25 3.25 0 0 1 17.75 21H6.25A3.25 3.25 0 0 1 3 17.75zM6.25 5C5.56 5 5 5.56 5 6.25v11.5c0 .69.56 1.25 1.25 1.25h11.5c.69 0 1.25-.56 1.25-1.25V6.25C19 5.56 18.44 5 17.75 5z" />
                                            </svg></button>
                                    </form>
                                    <p class="text-xl text-black font-bold pt-1">
                                        <?php echo $taskInfo['id_task'] . ". " . $taskInfo['task_name'] ?>
                                    </p>
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
                                                    value="<?php echo $taskInfo['task_name'] ?>" maxlength="200" required>
                                                <input
                                                    class="input input-bordered flex items-center gap-2 bg-white text-black w-96 h-8 rounded-lg border border-secondary border-2 mt-4"
                                                    placeholder="Descrição da tarefa" name="task_description"
                                                    value="<?php echo $taskInfo['task_description'] ?>" maxlength="200" required>
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
                    }
                }
                ?>
                <?php
                $tableName = $_COOKIE['tableName'];

                $selectCheckedQuery = $connection->prepare("SELECT * FROM $tableName WHERE isChecked = 1");
                $selectCheckedQuery->execute();

                if ($_COOKIE['status'] === "all" || $_COOKIE['status'] === "completed") {
                    ?>
                    <h1 class="text-center text-black text-xl font-semibold">Tarefas Finalizadas</h1>
                    <div class="divider divider-secondary"></div>
                    <?php
                    if ($selectCheckedQuery->rowCount() === 0) {
                        ?>
                        <h1 class="text-center text-black text-lg pb-8">Nenhuma tarefa concluída</h1>
                        <?php
                    } else {
                        while ($taskInfo = $selectCheckedQuery->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                            <label class="flex justify-between pt-4">
                                <section class="flex items-center gap-4">
                                    <form method="POST" action="actions/homepageAction.php">
                                        <input type="hidden" name="id_task" value="<?php echo $taskInfo['id_task'] ?>">

                                        <button name="check"><svg xmlns="http://www.w3.org/2000/svg" width="2.5em" height="2.5em"
                                                viewBox="0 0 32 32">
                                                <path fill="#38bdf8"
                                                    d="M26 4H6a2 2 0 0 0-2 2v20a2 2 0 0 0 2 2h20a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2M14 21.5l-5-4.957L10.59 15L14 18.346L21.409 11L23 12.577Z" />
                                                <path fill="none" d="m14 21.5l-5-4.957L10.59 15L14 18.346L21.409 11L23 12.577Z" />
                                            </svg></button>
                                    </form>
                                    <p class="text-xl text-black font-bold pb-2 line-through">
                                        <?php echo $taskInfo['id_task'] . ". " . $taskInfo['task_name'] ?>
                                    </p>
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
                                                    value="<?php echo $taskInfo['task_name'] ?>" maxlength="200" required>
                                                <input
                                                    class="input input-bordered flex items-center gap-2 bg-white text-black w-96 h-8 rounded-lg border border-secondary border-2 mt-4"
                                                    placeholder="Descrição da tarefa" name="task_description"
                                                    value="<?php echo $taskInfo['task_description'] ?>" maxlength="200" required>
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
                    }
                }
                ?>

                <?php
                $tableName = $_COOKIE['tableName'];
                $result = $_COOKIE['result'];

                $searchUncheckedQuery = $connection->prepare("SELECT * FROM $tableName WHERE task_name LIKE '%$result%' AND isChecked = 0 OR task_description LIKE '%$result%' AND isChecked = 0");
                $searchUncheckedQuery->execute();

                $searchCheckedQuery = $connection->prepare("SELECT * FROM $tableName WHERE task_name LIKE '%$result%' AND isChecked = 1 OR task_description LIKE '%$result%' AND isChecked = 1");
                $searchCheckedQuery->execute();

                if ($_COOKIE['status'] === "search") {
                    if ($searchUncheckedQuery->rowCount() === 0 && $searchCheckedQuery->rowCount() === 0) {
                        ?>
                        <h1 class="text-center text-black text-xl font-semibold">Resultados Semelhantes</h1>
                        <div class="divider divider-secondary"></div>
                        <h1 class="text-center text-black text-lg">Nenhuma busca foi encontrada</h1>
                        <?php
                    } else {
                        ?>
                        <h1 class="text-center text-black text-xl font-semibold">Resultados Semelhantes</h1>
                        <div class="divider divider-secondary"></div>
                        <?php
                        while ($taskInfo = $searchUncheckedQuery->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                            <label class="flex justify-between pt-4">
                                <section class="flex items-center gap-4">
                                    <form method="POST" action="actions/homepageAction.php">
                                        <input type="hidden" name="id_task" value="<?php echo $taskInfo['id_task'] ?>">

                                        <button name="check"><svg xmlns="http://www.w3.org/2000/svg" width="2.5em" height="2.5em"
                                                viewBox="0 0 24 24">
                                                <path fill="#38bdf8"
                                                    d="M3 6.25A3.25 3.25 0 0 1 6.25 3h11.5A3.25 3.25 0 0 1 21 6.25v11.5A3.25 3.25 0 0 1 17.75 21H6.25A3.25 3.25 0 0 1 3 17.75zM6.25 5C5.56 5 5 5.56 5 6.25v11.5c0 .69.56 1.25 1.25 1.25h11.5c.69 0 1.25-.56 1.25-1.25V6.25C19 5.56 18.44 5 17.75 5z" />
                                            </svg></button>
                                    </form>
                                    <p class="text-xl text-black font-bold pb-3">
                                        <?php echo $taskInfo['id_task'] . ". " . $taskInfo['task_name'] ?>
                                    </p>
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
                                                    value="<?php echo $taskInfo['task_name'] ?>" maxlength="200" required>
                                                <input
                                                    class="input input-bordered flex items-center gap-2 bg-white text-black w-96 h-8 rounded-lg border border-secondary border-2 mt-4"
                                                    placeholder="Descrição da tarefa" name="task_description"
                                                    value="<?php echo $taskInfo['task_description'] ?>" maxlength="200" required>
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
                        <?php
                        while ($taskInfo = $searchCheckedQuery->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                            <label class="flex justify-between pt-4">
                                <section class="flex items-center gap-4">
                                    <form method="POST" action="actions/homepageAction.php">
                                        <input type="hidden" name="id_task" value="<?php echo $taskInfo['id_task'] ?>">

                                        <button name="check"><svg xmlns="http://www.w3.org/2000/svg" width="2.5em" height="2.5em"
                                                viewBox="0 0 32 32">
                                                <path fill="#38bdf8"
                                                    d="M26 4H6a2 2 0 0 0-2 2v20a2 2 0 0 0 2 2h20a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2M14 21.5l-5-4.957L10.59 15L14 18.346L21.409 11L23 12.577Z" />
                                                <path fill="none" d="m14 21.5l-5-4.957L10.59 15L14 18.346L21.409 11L23 12.577Z" />
                                            </svg></button>
                                    </form>
                                    <p class="text-xl text-black font-bold pb-3 line-through">
                                        <?php echo $taskInfo['id_task'] . ". " . $taskInfo['task_name'] ?>
                                    </p>
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
                                                    value="<?php echo $taskInfo['task_name'] ?>" maxlength="200" required>
                                                <input
                                                    class="input input-bordered flex items-center gap-2 bg-white text-black w-96 h-8 rounded-lg border border-secondary border-2 mt-4"
                                                    placeholder="Descrição da tarefa" name="task_description"
                                                    value="<?php echo $taskInfo['task_description'] ?>" maxlength="200" required>
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
                    } ?>
                    ?>
                    <form method="POST" action="actions/homepageAction.php">
                        <button class="w-full bg-primary btn-sm font-bold mt-6" name="clear">Limpar Pesquisa</button>
                    </form><?php
                }
                ?>
            </main>
        </section>
    </main>
</body>

</html>