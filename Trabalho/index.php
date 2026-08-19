<?php

require_once 'conexao.php';
require_once 'funcoes.php';

try {
    $stmt = $pdo->query("SELECT * FROM produtos");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao buscar produtos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Produtos</title>
</head>
<body>
    <h1>Lista de Produtos</h1>
    <a href="cadastro.php"> Cadastrar Novo Produto </a>
    <br><br>
    <?php if (empty($produtos)): ?>
        <p>Nenhum produto encontrado.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Quantidade</th>
                    <th>Categoria</th>
                    <th>Ano de Fabricação</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $produto): ?>
                    <tr>
                        <td><?php echo $produto['id']; ?></td>
                        <td><?php echo sanitizar($produto['nome']); ?></td>
                        <td><?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                        <td><?php echo $produto['quantidade']; ?></td>
                        <td><?php echo sanitizar($produto['categoria']); ?></td>
                        <td><?php echo $produto['ano_fabricacao']; ?></td>
                        <td>
                            <a href="editar.php?id=<?php echo sanitizar($produto['id']); ?>">Editar</a>
                            <a href="excluir.php?id=<?php echo sanitizar($produto['id']); ?>" onclick="return confirm('Tem certeza que deseja excluir este produto?')">Excluir</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>