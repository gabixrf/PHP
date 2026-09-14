<?php

session_start();

require_once "conexao.php";
require_once "protecao.php";

if (!isset($_SESSION["usuario_id"])) {
    die("Você precisa estar logado para acessar esta página.");
}

$usuario_id = $_SESSION["usuario_id"];
$usuario_nome = $_SESSION["usuario_nome"] ?? "Usuário";


// Busca as metas do usuário
$sql = "SELECT *
        FROM metas
        WHERE usuario_id = ?
        ORDER BY data_limite ASC";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();

$resultado_metas = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Metas - Koplo</title>


    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f7f9;
            color: #17212b;
        }


        /* LAYOUT */

        .layout {
            display: flex;
            min-height: 100vh;
        }


        /* SIDEBAR */

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;

            width: 240px;
            height: 100vh;

            background: #092333;

            padding: 28px 16px;
        }


        .logo {
            color: white;
            font-size: 32px;
            font-weight: bold;

            margin-bottom: 40px;
        }


        .menu {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }


        .menu a {
            display: block;

            padding: 12px 14px;

            border-radius: 8px;

            color: #dce5eb;

            text-decoration: none;

            transition: 0.2s;
        }


        .menu a:hover {
            background: rgba(255,255,255,0.08);
        }


        .menu a.active {
            background: #31b984;
            color: white;
        }


        /* CONTEÚDO */

        .content {
            margin-left: 240px;

            width: calc(100% - 240px);

            padding: 30px 34px 40px;
        }


        /* TOPO */

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;

            margin-bottom: 35px;
        }


        .welcome {
            font-size: 15px;
            color: #68737d;
        }


        /* CABEÇALHO */

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;

            margin-bottom: 25px;
        }


        .page-header h1 {
            margin: 0;

            font-size: 28px;
        }


        .page-header p {
            margin-top: 6px;

            color: #68737d;
        }


        /* BOTÃO */

        .btn {
            display: inline-block;

            background: #31b984;
            color: white;

            padding: 11px 18px;

            border-radius: 8px;

            text-decoration: none;

            font-weight: 600;
        }


        .btn:hover {
            background: #29a875;
        }


        /* ÁREA DAS METAS */

        .section {
            background: white;

            border: 1px solid #e9edef;

            border-radius: 10px;

            box-shadow: 0 2px 8px rgba(0,0,0,0.03);

            padding: 24px;
        }


        /* TABELA */

        .table {
            width: 100%;

            border-collapse: collapse;
        }


        .table th {
            text-align: left;

            padding: 14px 10px;

            color: #68737d;

            font-size: 14px;

            border-bottom: 1px solid #e9edef;
        }


        .table td {
            padding: 18px 10px;

            border-bottom: 1px solid #f0f2f3;

            vertical-align: middle;
        }


        .table tr:last-child td {
            border-bottom: none;
        }


        /* NOME DA META */

        .meta-nome {
            font-weight: 600;
        }


        /* BARRA DE PROGRESSO */

        .progress-container {
            width: 150px;

            height: 8px;

            background: #e9edef;

            border-radius: 20px;

            overflow: hidden;

            margin-bottom: 6px;
        }


        .progress-bar {
            height: 100%;

            background: #31c48d;

            border-radius: 20px;
        }


        .progress-text {
            font-size: 13px;

            color: #68737d;
        }


        /* AÇÕES */

        .acoes {
            display: flex;

            gap: 8px;
        }


        .btn-editar {
            color: #31b984;

            text-decoration: none;

            font-size: 14px;

            font-weight: 600;
        }


        .btn-excluir {
            color: #d9534f;

            text-decoration: none;

            font-size: 14px;

            font-weight: 600;
        }


        /* QUANDO NÃO TIVER META */

        .empty {
            text-align: center;

            padding: 50px 20px;

            color: #68737d;
        }


    </style>

</head>


<body>


<div class="layout">


    <!-- SIDEBAR -->

    <aside class="sidebar">

        <div class="logo">
            Koplo
        </div>


        <nav class="menu">

            <a href="dashboard.php">
                Dashboard
            </a>

            <a href="transacoes.php">
                Transações
            </a>

            <a href="transacao.php">
                Nova transação
            </a>

            <a href="metas.php" class="active">
                Metas
            </a>

            <a href="#">
                IA
            </a>

            <a href="#">
                Configurações
            </a>

        </nav>

    </aside>



    <!-- CONTEÚDO -->

    <main class="content">


        <!-- TOPO -->

        <div class="topbar">

            <div class="welcome">
                Olá, <?php echo htmlspecialchars($usuario_nome); ?>!
            </div>

        </div>



        <!-- CABEÇALHO -->

        <div class="page-header">

            <div>

                <h1>Metas financeiras</h1>

                <p>
                    Acompanhe seus objetivos financeiros.
                </p>

            </div>


            <a href="nova_meta.php" class="btn">
                + Nova meta
            </a>

        </div>



        <!-- METAS -->

        <div class="section">


            <?php if ($resultado_metas->num_rows > 0): ?>


                <table class="table">

                    <thead>

                        <tr>

                            <th>Meta</th>

                            <th>Valor objetivo</th>

                            <th>Valor atual</th>

                            <th>Progresso</th>

                            <th>Data limite</th>

                            <th>Ações</th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php while ($meta = $resultado_metas->fetch_assoc()): ?>


                        <?php

                        if ($meta["valor_objetivo"] > 0) {

                            $progresso =
                                ($meta["valor_atual"] / $meta["valor_objetivo"]) * 100;

                        } else {

                            $progresso = 0;

                        }


                        if ($progresso > 100) {
                            $progresso = 100;
                        }

                        ?>


                        <tr>


                            <td>

                                <div class="meta-nome">

                                    <?php
                                    echo htmlspecialchars($meta["nome"]);
                                    ?>

                                </div>

                            </td>


                            <td>

                                R$

                                <?php
                                echo number_format(
                                    $meta["valor_objetivo"],
                                    2,
                                    ",",
                                    "."
                                );
                                ?>

                            </td>


                            <td>

                                R$

                                <?php
                                echo number_format(
                                    $meta["valor_atual"],
                                    2,
                                    ",",
                                    "."
                                );
                                ?>

                            </td>


                            <td>


                                <div class="progress-container">

                                    <div
                                        class="progress-bar"
                                        style="width: <?php echo $progresso; ?>%;"
                                    ></div>

                                </div>


                                <div class="progress-text">

                                    <?php
                                    echo number_format(
                                        $progresso,
                                        0
                                    );
                                    ?>%

                                </div>


                            </td>


                            <td>

                                <?php

                                if (!empty($meta["data_limite"])) {

                                    echo date(
                                        "d/m/Y",
                                        strtotime($meta["data_limite"])
                                    );

                                } else {

                                    echo "Sem prazo";

                                }

                                ?>

                            </td>


                            <td>

                                <div class="acoes">

                                    <a
                                        href="editar_meta.php?id=<?php echo $meta["id"]; ?>"
                                        class="btn-editar"
                                    >
                                        Editar
                                    </a>


                                    <a
                                        href="excluir_meta.php?id=<?php echo $meta["id"]; ?>"
                                        class="btn-excluir"
                                    >
                                        Excluir
                                    </a>

                                </div>

                            </td>


                        </tr>


                    <?php endwhile; ?>


                    </tbody>

                </table>


            <?php else: ?>


                <div class="empty">

                    <h3>Você ainda não possui metas.</h3>

                    <p>
                        Crie sua primeira meta financeira para começar.
                    </p>

                    <br>

                    <a href="nova_meta.php" class="btn">
                        Criar primeira meta
                    </a>

                </div>


            <?php endif; ?>


        </div>


    </main>


</div>


</body>

</html>
