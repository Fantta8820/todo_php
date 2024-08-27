<!DOCTYPE html>
<html lang="pt-BR" data-theme="night">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TODO - Logar</title>
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
        <form class="w-3/4 flex flex-col items-center">
            <h1 class="text-4xl text-center font-bold">LOGIN</h1>
            <input type="text" name="name" placeholder="Nome" class="input input-bordered w-full max-w-md mt-4 bg-gray-200 text-black" />
            <input type="password" name="password" placeholder="Senha" class="input input-bordered w-full max-w-md mt-4 bg-gray-200 text-black" />            
            <button class="btn btn-primary mt-4 w-full max-w-md">Logar</button>
            <a href="register.php" class="pt-4 hover:text-primary hover:underline">Não possui uma conta? Crie já!</a>
        </form>
    </main>
</body>

</html>