<?php

require_once 'conexao.php';
require_once 'funcoes.php';

if ($_SERVER['REQUEST_METHOD']=== 'POST'){

    $nome = validarTexto($_POST['nome'] ?? '');
    $preco = validarPreco($_POST['preco'] ?? '');
    $quantidade = validarQuantidade($_POST['quantidade'] ?? '');
    $categoria = validarCategoria($_POST['categoria'] ?? '');
    $anoFabricacao = validarAno($_POST['ano_fabricacao'] ?? '');

    if(!$nome || $preco === false || $quantidade === false || !$categoria || !$anoFabricacao){
        echo "<p style= 'color: red;'> Erro: Existem dados inválidos ou campos obrigatórios não preenchidos.</p>";
        echo "<a href='cadastro.php'> Voltar ao formulário </a>";
        exit;
    }
    
    try{
        $sql = "INSERT INTO produtos (nome, preco, quantidade, categoria, ano_fabricacao)
                VALUES (:nome, :preco, :quantidade, :categoria, :ano_fabricacao)";
        $stmt= $pdo->prepare($sql);

        $stmt->execute([
            ':nome' => $nome,
            ':preco' => $preco,
            ':quantidade' => $quantidade,
            ':categoria' => $categoria,
            ':ano_fabricacao' => $anoFabricacao
        ]);

        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        die("Erro ao salvar no banco de dados: " . $e->getMessage());
    } 
} else {
        headedr("Location: cadastro.php");
        exit;
    }
    ?>
