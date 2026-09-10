<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Hub - Criar Conta</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main>
        <h1>Event Hub</h1>
        
        <form action="login.php" method="post">
            <label for="nome_completo">nome completo:</label>
            <input type="text" id="nome_completo" name="nome_completo" required>
            
            <label for="email">email:</label>
            <input type="email" id="email" name="email" required>
            
            <label for="cpf">CPF:</label>
            <input type="text" id="cpf" name="cpf" required>
            
            <label for="telefone">número de telefone:</label>
            <input type="tel" id="telefone" name="telefone" required>
            
            <label for="senha">senha:</label>
            <input type="password" id="senha" name="senha" required>
            
            <label for="confirmar_senha">confirmar senha:</label>
            <input type="password" id="confirmar_senha" name="confirmar_senha" required>
            
            <button type="submit">Criar Conta</button>
        </form>
    </main>
</body>
</html>