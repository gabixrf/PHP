<?php

require_once 'conexao.php';

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];

    try{ 
     
        $sql = "DELETE FROM produtos WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        header("Location: index.php");
        exit;

    } catch (PDOException $e) {
    die("Erro ao excluir produto: " . $e->getMessage());
    } 
} else {
    header("Location: index.php");
    exit;
}



?>