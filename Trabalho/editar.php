<?php
    require_once 'conexao.php';
    require_once 'funcoes.php';

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = (int)$_GET['id'];

        try {
            $sql = "SELECT * FROM produtos WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['id' => $id]);
            $produto = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$produto) {
                header("Location: index.php");
                exit;
            }

        } catch (PDOException $e) {
            die("Erro ao buscar produto: " . $e->getMessage());
        }
    } else {
        header("Location: index.php");
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Produto</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Editar Produto</h1>
    <a href="index.php">Voltar para a lista de produtos</a>
    <hr>

    <form action="atualizar.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $produto['id']; ?>">

        <label for="nome">Nome:</label>
        <input type="text" name="nome" id="nome" value="<?php echo sanitizar($produto['nome']); ?>" required>
        <br>

        <label for="preco">Preço:</label>
        <input type="number" name="preco" id="preco"  min="0"value="<?php echo $produto['preco']; ?>" step="0.01" required>
        <br>

        <label for="quantidade">Quantidade:</label>
        <input type="number" name="quantidade" id="quantidade" min="0" value="><?php echo $produto['quantidade']; ?>" required>
        <br>

        <label for="categoria">Categoria:</label>
        <select id="categoria" name="categoria" required>
            <?php
                $categorias = ['Eletrônicos', 'Roupas', 'Alimentos', 'Livros', 'Outros'];
                foreach ($categorias as $categoria) {
                    $selected = ($produto['categoria'] === $categoria) ? 'selected' : '';
                    echo "<option value=\"$categoria\" $selected>$categoria</option>";
                }
            ?>
        </select>
        <br>

        <label for="ano_fabricacao">Ano de Fabricação:</label>
        <input type="number" name="ano_fabricacao" id="ano_fabricacao"max="<?php echo date('Y'); ?>" value="<?php echo $produto['ano_fabricacao']; ?>" required>
        <br>

        <button type="submit">Atualizar</button>
    </form>
</body>
</html>