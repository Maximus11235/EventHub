<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Hub - Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main>
        <h1>Event Hub</h1>
        
        <form action="index.php" method="post">
            <label for="nome">nome:</label>
            <input type="text" id="nome" name="nome" placeholder="campo de texto" required>
            
            
            <label for="senha">senha:</label>
            <input type="password" id="senha" name="senha" placeholder="campo de senha" required>
            
            
            <!-- Botão de entrar que, no HTML puro, enviará o form -->
            <button type="submit">Entrar</button>
        </form>
        
        <!-- Link para a tela de criar conta -->
        <a href="cadastro.php">Criar nova conta</a>
    </main>
</body>
</html>