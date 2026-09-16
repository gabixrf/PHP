<?php

require_once "conexao.php";
require_once "protecao.php";

// Verifica se o usuário está logado
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION["usuario_id"];
$usuario_nome = $_SESSION["usuario_nome"];

// ==============================
// TOTAL DE RECEITAS
// ==============================

$sql_receitas = "SELECT COALESCE(SUM(valor), 0) AS total
                 FROM transacoes
                 WHERE usuario_id = ?
                 AND tipo = 'receita'";

$stmt = $conexao->prepare($sql_receitas);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();

$resultado = $stmt->get_result();
$receitas = $resultado->fetch_assoc()["total"];

$stmt->close();


// ==============================
// TOTAL DE DESPESAS
// ==============================

$sql_despesas = "SELECT COALESCE(SUM(valor), 0) AS total
                 FROM transacoes
                 WHERE usuario_id = ?
                 AND tipo = 'despesa'";

$stmt = $conexao->prepare($sql_despesas);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();

$resultado = $stmt->get_result();
$despesas = $resultado->fetch_assoc()["total"];

$stmt->close();


// ==============================
// SALDO
// ==============================

$saldo = $receitas - $despesas;


// ==============================
// ÚLTIMAS TRANSAÇÕES
// ==============================

$sql_transacoes = "SELECT
                        transacoes.id,
                        transacoes.descricao,
                        transacoes.valor,
                        transacoes.tipo,
                        transacoes.data_transacao,
                        categorias.nome AS categoria
                   FROM transacoes
                   INNER JOIN categorias
                       ON transacoes.categoria_id = categorias.id
                   WHERE transacoes.usuario_id = ?
                   ORDER BY transacoes.data_transacao DESC
                   LIMIT 5";

$stmt = $conexao->prepare($sql_transacoes);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();

$resultado_transacoes = $stmt->get_result();

$stmt->close();

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">
    <link rel="stylesheet" href="dashboard.css">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard | Koplo</title>

    <style>

        
        /* =====================================
           HEADER
        ===================================== */

        .page-header {
            display: flex;

            align-items: flex-end;

            justify-content: space-between;

            margin-bottom: 25px;
        }

        .page-title {
            font-size: 25px;

            font-weight: 700;

            letter-spacing: -0.5px;

            color: #17212b;

            margin-bottom: 5px;
        }

        .page-subtitle {
            color: #87949c;

            font-size: 13px;
        }


        /* FILTRO */

        .period-select {
            background: #ffffff;

            border: 1px solid #e2e7ea;

            border-radius: 7px;

            padding: 9px 13px;

            color: #53636d;

            font-size: 12px;

            outline: none;
        }


        /* =====================================
           CARDS PRINCIPAIS
        ===================================== */

        .cards {
            display: grid;

            grid-template-columns: 1.35fr 1fr 1fr;

            gap: 16px;

            margin-bottom: 17px;
        }

        .card {
            background: #ffffff;

            border: 1px solid #e9edef;

            border-radius: 10px;

            padding: 21px 22px;

            min-height: 128px;

            box-shadow:
                0 2px 8px rgba(15, 30, 40, 0.025);
        }

        .card-header {
            display: flex;

            align-items: center;

            justify-content: space-between;

            margin-bottom: 15px;
        }

        .card-label {
            color: #7b8992;

            font-size: 12px;

            font-weight: 500;
        }

        .card-icon {
            width: 30px;
            height: 30px;

            border-radius: 7px;

            display: flex;

            align-items: center;
            justify-content: center;
        }

        .card-icon svg {
            width: 16px;
            height: 16px;
        }

        .saldo-icon {
            background: #e5f6ef;
            color: #16815d;
        }

        .receita-icon {
            background: #eaf1ff;
            color: #477ee8;
        }

        .despesa-icon {
            background: #fff0f0;
            color: #df5c5c;
        }

        .card-value {
            color: #18242d;

            font-size: 24px;

            font-weight: 700;

            letter-spacing: -0.5px;
        }

        .card-description {
            margin-top: 7px;

            color: #9aa5ab;

            font-size: 11px;
        }


        /* =====================================
           GRID PRINCIPAL
        ===================================== */

        .dashboard-grid {
            display: grid;

            grid-template-columns: 1.6fr 1fr;

            gap: 17px;

            margin-bottom: 17px;
        }

        .section {
            background: #ffffff;

            border: 1px solid #e9edef;

            border-radius: 10px;

            padding: 21px;

            box-shadow:
                0 2px 8px rgba(15, 30, 40, 0.025);
        }

        .section-header {
            display: flex;

            align-items: center;

            justify-content: space-between;

            margin-bottom: 19px;
        }

        .section-title {
            color: #26343d;

            font-size: 15px;

            font-weight: 650;
        }

        .section-link {
            color: #209a70;

            font-size: 11px;

            font-weight: 600;
        }

        .section-link:hover {
            color: #167956;
        }


        /* =====================================
           TRANSAÇÕES
        ===================================== */

        .transaction {
            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 12px 0;

            border-bottom: 1px solid #eef1f2;
        }

        .transaction:last-child {
            border-bottom: none;
        }

        .transaction-info {
            display: flex;

            align-items: center;

            gap: 11px;
        }

        .transaction-icon {
            width: 35px;
            height: 35px;

            border-radius: 8px;

            display: flex;

            align-items: center;
            justify-content: center;
        }

        .transaction-icon svg {
            width: 16px;
            height: 16px;
        }

        .transaction-icon.receita {
            background: #eaf6f1;
            color: #209b70;
        }

        .transaction-icon.despesa {
            background: #fff0f0;
            color: #d96262;
        }

        .transaction-name {
            color: #33434d;

            font-size: 13px;

            font-weight: 600;

            margin-bottom: 3px;
        }

        .transaction-category {
            color: #96a1a8;

            font-size: 10px;
        }

        .transaction-value {
            font-size: 12px;

            font-weight: 650;
        }

        .valor-receita {
            color: #15946a;
        }

        .valor-despesa {
            color: #d95858;
        }


        /* =====================================
           RESUMO
        ===================================== */

        .summary-row {
            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 15px 0;

            border-bottom: 1px solid #eef1f2;
        }

        .summary-row:last-of-type {
            border-bottom: none;
        }

        .summary-info {
            display: flex;

            align-items: center;

            gap: 9px;
        }

        .summary-dot {
            width: 7px;
            height: 7px;

            border-radius: 50%;
        }

        .dot-receita {
            background: #4d8bea;
        }

        .dot-despesa {
            background: #e86666;
        }

        .dot-saldo {
            background: #31b984;
        }

        .summary-label {
            color: #77858e;

            font-size: 12px;
        }

        .summary-value {
            color: #26343d;

            font-size: 13px;

            font-weight: 650;
        }


        /* =====================================
           BOTÃO
        ===================================== */

        .btn-primary {
            display: flex;

            align-items: center;
            justify-content: center;

            gap: 8px;

            width: 100%;

            margin-top: 18px;

            padding: 11px 15px;

            background: #31b984;

            border: none;

            border-radius: 7px;

            color: #ffffff;

            font-size: 12px;

            font-weight: 600;

            transition: background 0.2s ease;
        }

        .btn-primary:hover {
            background: #269e70;
        }

        .btn-primary svg {
            width: 14px;
            height: 14px;
        }


        /* =====================================
           ÁREA INFERIOR
        ===================================== */

        .bottom-grid {
            display: grid;

            grid-template-columns: 1.2fr 1fr;

            gap: 17px;
        }


        /* =====================================
           BARRA DE PROGRESSO - PLACEHOLDER
        ===================================== */

        .progress-item {
            margin-bottom: 18px;
        }

        .progress-item:last-child {
            margin-bottom: 0;
        }

        .progress-top {
            display: flex;

            justify-content: space-between;

            margin-bottom: 7px;
        }

        .progress-name {
            color: #53616a;

            font-size: 11px;

            font-weight: 600;
        }

        .progress-percent {
            color: #84929a;

            font-size: 10px;
        }

        .progress-bar {
            width: 100%;

            height: 6px;

            background: #edf1f2;

            border-radius: 10px;

            overflow: hidden;
        }

        .progress-fill {
            height: 100%;

            background: #31b984;

            border-radius: 10px;
        }


        /* =====================================
           ESTADO VAZIO
        ===================================== */

        .empty {
            color: #9aa5ab;

            font-size: 12px;

            padding: 20px 0;
        }


        /* =====================================
           RESPONSIVIDADE
        ===================================== */

        @media (max-width: 1050px) {

            .cards {
                grid-template-columns: 1fr 1fr;
            }

            .card:first-child {
                grid-column: span 2;
            }

        }


        @media (max-width: 800px) {

 
            .dashboard-grid,
            .bottom-grid {
                grid-template-columns: 1fr;
            }

        }


        @media (max-width: 600px) {

            .cards {
                grid-template-columns: 1fr;
            }

            .card:first-child {
                grid-column: auto;
            }

            .card-value {
                font-size: 22px;
            }

        }


    </style>

</head>

<body>

<div class="layout">

    <!-- =====================================
         SIDEBAR
    ====================================== -->

    <?php include "sidebar.php"; ?>


    <!-- =====================================
         CONTEÚDO
    ====================================== -->

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
                    stroke-linejoin="round"
                >
                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                    <path d="M10 21h4"></path>
                </svg>


                <!-- PERFIL -->

                <div class="profile-wrapper">

                    <button
                        type="button"
                        class="profile"
                        id="profileButton"
                        aria-expanded="false"
                    >

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
                            stroke-linejoin="round"
                        >
                            <path d="m6 9 6 6 6-6"></path>
                        </svg>

                    </button>


                    <!-- DROPDOWN -->

                    <div
                        class="profile-dropdown"
                        id="profileDropdown"
                    >

                        <!-- Trocar usuário -->

                        <a href="login.php">

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                                <path d="M10 17l5-5-5-5"></path>
                                <path d="M15 12H3"></path>
                            </svg>

                            <span>
                                Trocar usuário/cadastro
                            </span>

                        </a>


                        <!-- Separador -->

                        <div class="dropdown-divider"></div>


                        <!-- Sair -->

                        <a
                            href="logout.php"
                            class="logout-option"
                        >

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
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


        <!-- HEADER -->

        <header class="page-header">

            <div>

                <h1 class="page-title">
                    Olá, <?php echo htmlspecialchars($usuario_nome); ?>
                </h1>

                <p class="page-subtitle">
                    Resumo da sua vida financeira
                </p>

            </div>


            <select class="period-select">

                <option>
                    Este período
                </option>

                <option>
                    Este mês
                </option>

                <option>
                    Últimos 30 dias
                </option>

            </select>

        </header>


        <!-- =====================================
             CARDS
        ====================================== -->

        <section class="cards">


            <!-- SALDO -->

            <div class="card">

                <div class="card-header">

                    <span class="card-label">
                        Saldo total
                    </span>

                    <div class="card-icon saldo-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <rect
                                x="3"
                                y="6"
                                width="18"
                                height="13"
                                rx="2"
                            ></rect>

                            <path d="M16 10h5"></path>

                            <circle
                                cx="16"
                                cy="13"
                                r="1"
                            ></circle>

                            <path d="M7 6V4h10v2"></path>
                        </svg>

                    </div>

                </div>

                <div class="card-value">

                    R$
                    <?php
                    echo number_format(
                        $saldo,
                        2,
                        ",",
                        "."
                    );
                    ?>

                </div>

                <div class="card-description">
                    Saldo disponível
                </div>

            </div>


            <!-- RECEITAS -->

            <div class="card">

                <div class="card-header">

                    <span class="card-label">
                        Receitas
                    </span>

                    <div class="card-icon receita-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M12 19V5"></path>
                            <path d="M6 11l6-6 6 6"></path>
                        </svg>

                    </div>

                </div>

                <div class="card-value">

                    R$
                    <?php
                    echo number_format(
                        $receitas,
                        2,
                        ",",
                        "."
                    );
                    ?>

                </div>

                <div class="card-description">
                    Total recebido
                </div>

            </div>


            <!-- DESPESAS -->

            <div class="card">

                <div class="card-header">

                    <span class="card-label">
                        Despesas
                    </span>

                    <div class="card-icon despesa-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M12 5v14"></path>
                            <path d="M6 13l6 6 6-6"></path>
                        </svg>

                    </div>

                </div>

                <div class="card-value">

                    R$
                    <?php
                    echo number_format(
                        $despesas,
                        2,
                        ",",
                        "."
                    );
                    ?>

                </div>

                <div class="card-description">
                    Total gasto
                </div>

            </div>

        </section>


        <!-- =====================================
             PRINCIPAL
        ====================================== -->

        <section class="dashboard-grid">


            <!-- TRANSAÇÕES -->

            <div class="section">

                <div class="section-header">

                    <h2 class="section-title">
                        Últimas transações
                    </h2>

                    <a
                        href="transacoes.php"
                        class="section-link"
                    >
                        Ver todas
                    </a>

                </div>


                <?php if ($resultado_transacoes->num_rows > 0) { ?>


                    <?php while (
                        $transacao =
                        $resultado_transacoes->fetch_assoc()
                    ) { ?>


                        <div class="transaction">


                            <div class="transaction-info">


                                <div
                                    class="transaction-icon
                                    <?php
                                    echo $transacao["tipo"];
                                    ?>"
                                >

                                    <?php if (
                                        $transacao["tipo"]
                                        == "receita"
                                    ) { ?>

                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path d="M12 19V5"></path>
                                            <path d="M6 11l6-6 6 6"></path>
                                        </svg>

                                    <?php } else { ?>

                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.8"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >
                                            <path d="M12 5v14"></path>
                                            <path d="M6 13l6 6 6-6"></path>
                                        </svg>

                                    <?php } ?>

                                </div>


                                <div>

                                    <div class="transaction-name">

                                        <?php
                                        echo htmlspecialchars(
                                            $transacao["descricao"]
                                        );
                                        ?>

                                    </div>


                                    <div class="transaction-category">

                                        <?php
                                        echo htmlspecialchars(
                                            $transacao["categoria"]
                                        );
                                        ?>

                                        ·

                                        <?php
                                        echo date(
                                            "d/m/Y",
                                            strtotime(
                                                $transacao[
                                                    "data_transacao"
                                                ]
                                            )
                                        );
                                        ?>

                                    </div>

                                </div>

                            </div>


                            <div
                                class="transaction-value
                                <?php
                                echo $transacao["tipo"]
                                    == "receita"
                                    ? "valor-receita"
                                    : "valor-despesa";
                                ?>"
                            >

                                <?php
                                echo $transacao["tipo"]
                                    == "receita"
                                    ? "+ "
                                    : "- ";
                                ?>

                                R$

                                <?php
                                echo number_format(
                                    $transacao["valor"],
                                    2,
                                    ",",
                                    "."
                                );
                                ?>

                            </div>


                        </div>


                    <?php } ?>


                <?php } else { ?>


                    <div class="empty">

                        Nenhuma transação cadastrada.

                    </div>


                    <a
                        href="transacao.php"
                        class="btn-primary"
                    >
                        Nova transação
                    </a>


                <?php } ?>

            </div>


            <!-- RESUMO -->

            <div class="section">

                <div class="section-header">

                    <h2 class="section-title">
                        Resumo mensal
                    </h2>

                </div>


                <!-- RECEITAS -->

                <div class="summary-row">

                    <div class="summary-info">

                        <span
                            class="summary-dot dot-receita"
                        ></span>

                        <span class="summary-label">
                            Receitas
                        </span>

                    </div>

                    <span class="summary-value">

                        R$
                        <?php
                        echo number_format(
                            $receitas,
                            2,
                            ",",
                            "."
                        );
                        ?>

                    </span>

                </div>


                <!-- DESPESAS -->

                <div class="summary-row">

                    <div class="summary-info">

                        <span
                            class="summary-dot dot-despesa"
                        ></span>

                        <span class="summary-label">
                            Despesas
                        </span>

                    </div>

                    <span class="summary-value">

                        R$
                        <?php
                        echo number_format(
                            $despesas,
                            2,
                            ",",
                            "."
                        );
                        ?>

                    </span>

                </div>


                <!-- SALDO -->

                <div class="summary-row">

                    <div class="summary-info">

                        <span
                            class="summary-dot dot-saldo"
                        ></span>

                        <span class="summary-label">
                            Saldo
                        </span>

                    </div>

                    <span class="summary-value">

                        R$
                        <?php
                        echo number_format(
                            $saldo,
                            2,
                            ",",
                            "."
                        );
                        ?>

                    </span>

                </div>


                <a
                    href="transacao.php"
                    class="btn-primary"
                >

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                    >
                        <path d="M12 5v14"></path>
                        <path d="M5 12h14"></path>
                    </svg>

                    Nova transação

                </a>

            </div>


        </section>


        <!-- =====================================
             ÁREA INFERIOR
        ====================================== -->

        <section class="bottom-grid">


            <!-- METAS - PREPARAÇÃO -->

            <div class="section">

                <div class="section-header">

                    <h2 class="section-title">
                        Metas financeiras
                    </h2>

                    <a
                        href="#"
                        class="section-link"
                    >
                        Ver metas
                    </a>

                </div>


                <div class="progress-item">

                    <div class="progress-top">

                        <span class="progress-name">
                            Metas em breve
                        </span>

                        <span class="progress-percent">
                            --
                        </span>

                    </div>

                    <div class="progress-bar">

                        <div
                            class="progress-fill"
                            style="width: 0%;"
                        ></div>

                    </div>

                </div>


                <div class="empty">
                    O sistema de metas será adicionado aqui.
                </div>

            </div>


            <!-- KOPLO IA -->

            <div class="section">

                <div class="section-header">

                    <h2 class="section-title">
                        Análise financeira
                    </h2>

                    <a
                        href="#"
                        class="section-link"
                    >
                        Koplo IA
                    </a>

                </div>


                <div class="empty">

                    A análise inteligente das suas finanças
                    estará disponível em breve.

                </div>


            </div>


        </section>


    </main>

</div>

<script>
    const profileButton = document.getElementById("profileButton");
    const profileWrapper = document.querySelector(".profile-wrapper");

    profileButton.addEventListener("click", function (event) {
        event.stopPropagation();

        const aberto = profileWrapper.classList.toggle("open");

        profileButton.setAttribute(
            "aria-expanded",
            aberto ? "true" : "false"
        );
    });

    document.addEventListener("click", function (event) {
        if (!profileWrapper.contains(event.target)) {
            profileWrapper.classList.remove("open");

            profileButton.setAttribute(
                "aria-expanded",
                "false"
            );
        }
    });
</script>

</body>

</html>