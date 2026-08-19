<?php
require_once 'conexao.php';
require_once 'funcoes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : false;
    $nome = validarTexto($_POST['nome'] ?? '');
    $preco = validarPreco($_POST['preco'] ?? 0);
    $quantidade = validarQuantidade($_POST['quantidade'] ?? 0);
    $categoria = validarCategoria($_POST['categoria'] ?? '');
    $ano_fabricacao = validarAno($_POST['ano_fabricacao'] ?? '');

    if (!$id || !$nome || $preco === false || $quantidade === false || !$categoria || !$ano_fabricacao ) {
       echo "<p style='color: red;'>Erro: Dados inválidos. Por favor, verifique os campos e tente novamente.</p>";
       echo "<a href='editar.php?id=$id'>Voltar para a edição</a>";
       exit;
    }

    try {
        $sql = "UPDATE produtos SET nome = :nome, preco = :preco, quantidade = :quantidade, categoria = :categoria WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nome' => $nome,
            ':preco' => $preco,
            ':quantidade' => $quantidade,
            ':categoria' => $categoria,
            ':id' => $id
        ]);

        header("Location: index.php");
        exit;

    } catch (PDOException $e) {
        die("Erro ao atualizar produto: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
    exit;
}

?>