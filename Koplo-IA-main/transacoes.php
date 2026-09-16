```php
<?php

require_once "protecao.php";
require_once "conexao.php";

$usuario_id = $_SESSION["usuario_id"];
$usuario_nome = $_SESSION["usuario_nome"];

/*
|--------------------------------------------------------------------------
| Busca as transações do usuário logado
|--------------------------------------------------------------------------
*/

$sql = "SELECT
            transacoes.id,
            categorias.nome AS categoria,
            transacoes.descricao,
            transacoes.valor,
            transacoes.tipo,
            transacoes.data_transacao

        FROM transacoes

        INNER JOIN categorias
            ON transacoes.categoria_id = categorias.id

        WHERE transacoes.usuario_id = ?

        ORDER BY transacoes.data_transacao DESC,
                 transacoes.id DESC";

$stmt = $conexao->prepare($sql);

$stmt->bind_param("i", $usuario_id);

$stmt->execute();

$resultado = $stmt->get_result();

$total_transacoes = $resultado->num_rows;

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">


    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Minhas transações - Koplo</title>
    <link rel="stylesheet" href="dashboard.css">

    <style>
        /* CONTAINER */

        .container {
            padding: 34px;
            max-width: 1500px;
        }

        /* CABEÇALHO DA PÁGINA */

        .page-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 28px;
        }

        .page-title h2 {
            font-size: 25px;
            color: #26343d;
            font-weight: 700;
            margin-bottom: 7px;
            letter-spacing: -0.3px;
        }

        .page-title p {
            color: #82909a;
            font-size: 13px;
        }

        .new-transaction {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #31c48d;
            color: #ffffff;
            padding: 11px 17px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 600;
            transition:
                background 0.2s ease,
                transform 0.2s ease;
        }

        .new-transaction:hover {
            background: #28b17d;
            transform: translateY(-1px);
        }

        .new-transaction svg {
            width: 16px;
            height: 16px;
        }

        /* CARD DA TABELA */

        .transactions-card {
            background: #ffffff;
            border: 1px solid #e8edef;
            border-radius: 11px;
            overflow: hidden;
        }

        .card-header {
            min-height: 68px;
            padding: 0 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #edf0f1;
        }

        .card-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header-icon {
            width: 32px;
            height: 32px;
            border-radius: 7px;
            background: #e9f7f2;
            color: #249b73;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-header-icon svg {
            width: 17px;
            height: 17px;
        }

        .card-header h3 {
            font-size: 14px;
            color: #34444e;
            font-weight: 600;
        }

        .transaction-count {
            color: #8a979f;
            font-size: 11px;
        }

        /* TABELA */

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 760px;
        }

        .transactions-table th {
            height: 47px;
            padding: 0 20px;
            background: #fafbfb;
            color: #89969e;
            font-size: 10px;
            font-weight: 600;
            text-align: left;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            border-bottom: 1px solid #edf0f1;
            white-space: nowrap;
        }

        .transactions-table td {
            height: 68px;
            padding: 0 20px;
            border-bottom: 1px solid #f0f2f3;
            color: #53636d;
            font-size: 12px;
            white-space: nowrap;
        }

        .transactions-table tbody tr {
            transition: background 0.15s ease;
        }

        .transactions-table tbody tr:hover {
            background: #fafcfc;
        }

        .transactions-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* CATEGORIA */

        .category-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .category-icon {
            width: 31px;
            height: 31px;
            border-radius: 7px;
            background: #f1f5f4;
            color: #61736e;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .category-icon svg {
            width: 15px;
            height: 15px;
        }

        .category-name {
            color: #394a54;
            font-weight: 600;
        }

        /* DESCRIÇÃO */

        .description {
            color: #65747d;
            max-width: 240px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* TIPO */

        .transaction-type {
            display: inline-flex;
            align-items: center;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 600;
        }

        .transaction-type.receita {
            background: #e8f7f1;
            color: #218963;
        }

        .transaction-type.despesa {
            background: #fff0f0;
            color: #c75a5a;
        }

        /* VALOR */

        .transaction-value {
            font-weight: 700;
        }

        .value-receita {
            color: #24966d;
        }

        .value-despesa {
            color: #c95d5d;
        }

        /* AÇÕES */

        .actions {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .action-button {
            width: 31px;
            height: 31px;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition:
                background 0.18s ease,
                color 0.18s ease;
        }

        .action-button svg {
            width: 15px;
            height: 15px;
        }

        .action-edit {
            color: #687982;
            background: #f3f5f6;
        }

        .action-edit:hover {
            background: #e8f6f1;
            color: #24966d;
        }

        .action-delete {
            color: #bd6868;
            background: #faf1f1;
        }

        .action-delete:hover {
            background: #ffe7e7;
            color: #c84d4d;
        }

        /* ESTADO VAZIO */

        .empty-state {
            padding: 65px 20px;
            text-align: center;
        }

        .empty-icon {
            width: 54px;
            height: 54px;
            margin: 0 auto 17px;
            border-radius: 12px;
            background: #edf7f4;
            color: #31a77c;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .empty-icon svg {
            width: 25px;
            height: 25px;
        }

        .empty-state h3 {
            color: #3c4c55;
            font-size: 15px;
            margin-bottom: 7px;
        }

        .empty-state p {
            color: #8b979e;
            font-size: 12px;
            margin-bottom: 20px;
        }

        /* RESPONSIVIDADE */

        @media (max-width: 1050px) {

            .container {
                padding: 28px;
            }

            .transactions-table {
                min-width: 720px;
            }

        }

        @media (max-width: 800px) {

            .container {
                padding: 25px 20px;
            }

        }

        @media (max-width: 600px) {

            .new-transaction {
                width: 100%;
                justify-content: center;
            }

            .container {
                padding: 22px 14px;
            }

            .card-header {
                padding: 0 16px;
            }

            .transaction-count {
                display: none;
            }

        }
    </style>

</head>

<body>

    <div class=" layout">

        <?php include "sidebar.php"; ?>

        <!-- CONTEÚDO PRINCIPAL -->

        <main class="content">

                <!-- TOPBAR -->

                <div class="topbar">

                    <div class="topbar-right">

                        <svg
                            class="notification"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                            <path d="M10 21h4"></path>
                        </svg>

                        <!-- PERFIL -->

                        <div class="profile-wrapper">

                            <button
                                type="button"
                                class="profile"
                                id="profileButton"
                                aria-expanded="false">

                                <span class="profile-name">
                                    <?php echo htmlspecialchars($usuario_nome); ?>
                                </span>

                                <div class="profile-avatar">

                                    <?php
                                    echo strtoupper(
                                        substr($usuario_nome, 0, 1)
                                    );
                                    ?>

                                </div>

                                <!-- Seta -->

                                <svg
                                    class="profile-arrow"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="m6 9 6 6 6-6"></path>
                                </svg>

                            </button>

                            <!-- DROPDOWN -->

                            <div
                                class="profile-dropdown"
                                id="profileDropdown">

                                <a href="login.php">

                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                                        <path d="M10 17l5-5-5-5"></path>
                                        <path d="M15 12H3"></path>
                                    </svg>

                                    <span>
                                        Trocar usuário/cadastro
                                    </span>

                                </a>

                                <div class="dropdown-divider"></div>

                                <a
                                    href="logout.php"
                                    class="logout-option">

                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                        <path d="M16 17l5-5-5-5"></path>
                                        <path d="M21 12H9"></path>
                                    </svg>

                                    <span>
                                        Sair
                                    </span>

                                </a>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- CONTAINER -->

                <div class="container">


                    <!-- CABEÇALHO -->

                    <div class="page-header">

                        <div class="page-title">

                            <h2>Minhas transações</h2>

                            <p>
                                Visualize e gerencie todas as suas movimentações financeiras.
                            </p>

                        </div>


                        <a
                            href="transacao.php"
                            class="new-transaction">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M12 5v14"></path>
                                <path d="M5 12h14"></path>
                            </svg>

                            Nova transação

                        </a>

                    </div>


                    <!-- TABELA -->

                    <section class="transactions-card">


                        <div class="card-header">

                            <div class="card-header-left">

                                <div class="card-header-icon">

                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M4 5h16"></path>
                                        <path d="M4 9h16"></path>
                                        <path d="M4 13h10"></path>
                                        <path d="M4 17h8"></path>
                                    </svg>

                                </div>

                                <h3>Histórico financeiro</h3>

                            </div>


                            <span class="transaction-count">

                                <?php echo $total_transacoes; ?>

                                <?php
                                echo $total_transacoes == 1
                                    ? " transação"
                                    : " transações";
                                ?>

                            </span>

                        </div>


                        <?php if ($resultado->num_rows > 0) { ?>


                            <div class="table-wrapper">

                                <table class="transactions-table">

                                    <thead>

                                        <tr>

                                            <th>Categoria</th>

                                            <th>Descrição</th>

                                            <th>Tipo</th>

                                            <th>Data</th>

                                            <th>Valor</th>

                                            <th>Ações</th>

                                        </tr>

                                    </thead>


                                    <tbody>


                                        <?php while ($transacao = $resultado->fetch_assoc()) { ?>


                                            <tr>


                                                <!-- CATEGORIA -->

                                                <td>

                                                    <div class="category-cell">

                                                        <div class="category-icon">

                                                            <svg
                                                                viewBox="0 0 24 24"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                stroke-width="1.8"
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                                <circle
                                                                    cx="12"
                                                                    cy="12"
                                                                    r="8"></circle>

                                                                <path d="M12 8v8"></path>

                                                                <path d="M9.5 10.5c0-1 1-1.5 2.5-1.5s2.5.5 2.5 1.5-1 1.5-2.5 1.5-2.5.5-2.5 1.5 1 1.5 2.5 1.5 2.5-.5 2.5-1.5"></path>
                                                            </svg>

                                                        </div>

                                                        <span class="category-name">

                                                            <?php
                                                            echo htmlspecialchars(
                                                                $transacao["categoria"]
                                                            );
                                                            ?>

                                                        </span>

                                                    </div>

                                                </td>


                                                <!-- DESCRIÇÃO -->

                                                <td>

                                                    <div class="description">

                                                        <?php
                                                        echo htmlspecialchars(
                                                            $transacao["descricao"]
                                                        );
                                                        ?>

                                                    </div>

                                                </td>


                                                <!-- TIPO -->

                                                <td>

                                                    <?php if ($transacao["tipo"] === "receita") { ?>

                                                        <span class="transaction-type receita">
                                                            Receita
                                                        </span>

                                                    <?php } else { ?>

                                                        <span class="transaction-type despesa">
                                                            Despesa
                                                        </span>

                                                    <?php } ?>

                                                </td>


                                                <!-- DATA -->

                                                <td>

                                                    <?php

                                                    echo date(
                                                        "d/m/Y",
                                                        strtotime(
                                                            $transacao["data_transacao"]
                                                        )
                                                    );

                                                    ?>

                                                </td>


                                                <!-- VALOR -->

                                                <td>

                                                    <span
                                                        class="
                                                    transaction-value
                                                    <?php
                                                    echo $transacao["tipo"] === "receita"
                                                        ? "value-receita"
                                                        : "value-despesa";
                                                    ?>
                                                ">

                                                        <?php

                                                        echo $transacao["tipo"] === "receita"
                                                            ? "+ "
                                                            : "- ";

                                                        echo "R$ " . number_format(
                                                            $transacao["valor"],
                                                            2,
                                                            ",",
                                                            "."
                                                        );

                                                        ?>

                                                    </span>

                                                </td>


                                                <!-- AÇÕES -->

                                                <td>

                                                    <div class="actions">


                                                        <a
                                                            href="editar_transacao.php?id=<?php echo $transacao["id"]; ?>"
                                                            class="action-button action-edit"
                                                            title="Editar transação">

                                                            <svg
                                                                viewBox="0 0 24 24"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                stroke-width="1.8"
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                                <path d="M12 20h9"></path>
                                                                <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4 11.5-11.5z"></path>
                                                            </svg>

                                                        </a>


                                                        <a
                                                            href="excluir_transacao.php?id=<?php echo $transacao["id"]; ?>"
                                                            class="action-button action-delete"
                                                            title="Excluir transação"
                                                            onclick="return confirm('Tem certeza que deseja excluir esta transação?');">

                                                            <svg
                                                                viewBox="0 0 24 24"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                stroke-width="1.8"
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round">
                                                                <path d="M3 6h18"></path>
                                                                <path d="M8 6V4h8v2"></path>
                                                                <path d="M19 6l-1 14H6L5 6"></path>
                                                                <path d="M10 11v5"></path>
                                                                <path d="M14 11v5"></path>
                                                            </svg>

                                                        </a>


                                                    </div>

                                                </td>


                                            </tr>


                                        <?php } ?>


                                    </tbody>

                                </table>

                            </div>


                        <?php } else { ?>


                            <!-- ESTADO VAZIO -->

                            <div class="empty-state">

                                <div class="empty-icon">

                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M4 5h16"></path>
                                        <path d="M4 9h16"></path>
                                        <path d="M4 13h10"></path>
                                        <path d="M4 17h8"></path>
                                    </svg>

                                </div>

                                <h3>Nenhuma transação cadastrada</h3>

                                <p>
                                    Comece registrando sua primeira movimentação financeira.
                                </p>

                                <a
                                    href="transacao.php"
                                    class="new-transaction">

                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M12 5v14"></path>
                                        <path d="M5 12h14"></path>
                                    </svg>

                                    Nova transação

                                </a>

                            </div>


                        <?php } ?>


                    </section>


                </div>

        </main>

    </div>


    <!-- =========================================================
         JAVASCRIPT
    ========================================================= -->

    <script>
        const profileButton =
            document.getElementById("profileButton");

        const profileWrapper =
            document.querySelector(".profile-wrapper");


        profileButton.addEventListener(
            "click",
            function(event) {

                event.stopPropagation();

                const aberto =
                    profileWrapper.classList.toggle("open");

                profileButton.setAttribute(
                    "aria-expanded",
                    aberto ? "true" : "false"
                );

            }
        );


        document.addEventListener(
            "click",
            function(event) {

                if (!profileWrapper.contains(event.target)) {

                    profileWrapper.classList.remove("open");

                    profileButton.setAttribute(
                        "aria-expanded",
                        "false"
                    );

                }

            }
        );

        
    </script>


</body>

</html>

<?php

$stmt->close();

?>
```