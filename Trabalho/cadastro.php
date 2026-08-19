<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Produto</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="app-container">
        <!-- Cabeçalho -->
        <header class="page-header">
            <div class="header-title">
                <h1>Cadastrar Novo Produto</h1>
                <p>Preencha os dados do item para adicionar ao estoque</p>
            </div>
            <a href="index.php" class="btn-secondary">
                ← Voltar para a Listagem
            </a>
        </header>

        <!-- Formulário -->
        <form action="salvar.php" method="POST" class="form-layout">

            <div class="form-group">
                <label for="nome">Nome do Produto</label>
                <input type="text" id="nome" name="nome" placeholder="Ex: Monitor UltraWide 29''" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="preco">Preço (R$)</label>
                    <input type="number" id="preco" name="preco" step="0.01" min="0" placeholder="0.00" required>
                </div>

                <div class="form-group">
                    <label for="quantidade">Quantidade em Estoque</label>
                    <input type="number" id="quantidade" name="quantidade" min="0" placeholder="0" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="categoria">Categoria</label>
                    <select id="categoria" name="categoria" required>
                        <option value="">-- Selecione uma categoria --</option>
                        <option value="Eletrônicos">Eletrônicos</option>
                        <option value="Roupas">Roupas</option>
                        <option value="Alimentos">Alimentos</option>
                        <option value="Outros">Outros</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="ano_fabricacao">Ano de Fabricação</label>
                    <input type="number" id="ano_fabricacao" name="ano_fabricacao" max="<?php echo date('Y'); ?>" placeholder="Ex: <?php echo date('Y'); ?>" required>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary btn-submit">
                    Salvar Produto
                </button>
            </div>

        </form>
    </div>

</body>
</html>