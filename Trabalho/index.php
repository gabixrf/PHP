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
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Produtos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="app-container">
        <!-- Topo da Página -->
        <header class="page-header">
            <div class="header-title">
                <h1>Lista de Produtos</h1>
                <p>Controle de estoque e catálogo</p>
            </div>
            <a href="cadastro.php" class="btn-primary">
                + Cadastrar Novo Produto
            </a>
        </header>

        <!-- Conteúdo quando NÃO há produtos -->
        <?php if (empty($produtos)): ?>
            <div class="empty-state">
                <div class="empty-icon">📦</div>
                <h3>Nenhum produto encontrado</h3>
                <p>Você ainda não tem itens cadastrados no sistema.</p>
                <a href="cadastro.php" class="btn-secondary">Cadastrar o Primeiro Produto</a>
            </div>

        <!-- Conteúdo com Tabela quando HÁ produtos -->
        <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Preço</th>
                            <th>Qtd.</th>
                            <th>Categoria</th>
                            <th>Ano</th>
                            <th style="text-align: right;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($produtos as $produto): ?>
                            <tr>
                                <td class="td-id">#<?php echo $produto['id']; ?></td>
                                <td class="td-nome"><?php echo sanitizar($produto['nome']); ?></td>
                                <td class="td-preco">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                                <td><?php echo $produto['quantidade']; ?></td>
                                <td><span class="badge-categoria"><?php echo sanitizar($produto['categoria']); ?></span></td>
                                <td><?php echo $produto['ano_fabricacao']; ?></td>
                                <td class="actions-cell">
                                    <a href="editar.php?id=<?php echo sanitizar($produto['id']); ?>" class="btn-action edit">Editar</a>
                                    <a href="excluir.php?id=<?php echo sanitizar($produto['id']); ?>" class="btn-action delete" onclick="return confirm('Tem certeza que deseja excluir este produto?')">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>