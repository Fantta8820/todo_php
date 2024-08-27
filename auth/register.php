<!DOCTYPE html>
<html lang="pt-BR" data-theme="night">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TODO - Registrar</title>
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
    <main class="w-screen h-screen grid place-items-center">
        <form class="w-3/4 flex flex-col items-center" method="POST" action="actions/register.php">
            <h1 class="text-4xl text-center font-bold">REGISTRAR</h1>
            <input type="text" name="name" placeholder="Nome"
                class="input input-bordered w-full max-w-md mt-4 bg-gray-200 text-black" required />
            <input type="password" name="password" placeholder="Senha"
                class="input input-bordered w-full max-w-md mt-4 bg-gray-200 text-black" required />
            <button class="btn btn-primary mt-4 w-full max-w-md" name="send">Criar conta</button>
            <a href="login.php" class="pt-4 hover:text-primary hover:underline">Já possui uma conta?</a>
            <?php
            session_start();
            echo "<p class='text-error underline'>" . $_SESSION['error'] . "</p>"
            ?>
        </form>
    </main>
</body>

</html>