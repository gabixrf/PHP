

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Cadastrar Produtos</title>
</head>

<body>
    <h2>Cadastrar Novo Produto</h2>

    <a href="index.php"> <- Voltar para a Listagem</a>
    <hr>

    <form action="salvar.php" method="POST">

        <p>
            <label for = "nome"> Nome do Produto: </label><br>
            <input type="text" id="nome" name="nome" required><br><br>
        </p>

        <p>
            <label for="preco"> Preço:</label><br>
            <input type="number" id="preco" name="preco" step="0.01" min="0" required><br><br>
        </p>

        <p>
            <label for= "quantidade"> Quantidade em Estoque:</label><br>
            <input type="number" id="quantidade" name="quantidade" min="0" required><br><br>
        </p>

        <p>
            <label for="categoria"> Categoria:</label><br>
            <select id="categoria" name="categoria" required>
                <option value=""> -- Selecione uma categoria --</option>
                <option value="Eletrônicos"> Eletrônicos </option>
                <option value="Roupas"> Roupas </option>
                <option value="Alimentos"> Alimentos </option>
                <option value="Outros"> Outros </option>
        </select><br><br>
        </p>

        <p>
            <label for="ano_fabricacao"> Ano de Fabricação:</label><br>
            <input type="number" id="ano_fabricacao" name="ano_fabricacao" max="<?php echo date('Y'); ?>" required><br><br>
        </p>

        <button type="submit">Salvar Produtos</button>
    </form>

    
</body>
</html>