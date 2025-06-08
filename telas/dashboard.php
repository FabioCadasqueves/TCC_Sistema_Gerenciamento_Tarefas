<?php
session_start();

// Impede acesso se ninguém estiver logado
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    header('Location: login.php');
    exit;
}

// Define página atual
$paginaAtual = 'dashboard';

require_once '../conexao/conexao.php';

$admin_id = $_SESSION['admin_id'];

// Total de Tarefas
$sqlTotal = "SELECT COUNT(*) AS total FROM tarefas WHERE criado_por = ? AND criado_por_tipo = 'admin'";
$stmt = $conn->prepare($sqlTotal);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$totalTarefas = $result['total'];

// Concluídas
$sqlConcluidas = "SELECT COUNT(*) AS total FROM tarefas WHERE criado_por = ? AND criado_por_tipo = 'admin' AND status = 'Concluída'";
$stmt = $conn->prepare($sqlConcluidas);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$concluidas = $stmt->get_result()->fetch_assoc()['total'];

// Em Andamento
$sqlAndamento = "SELECT COUNT(*) AS total FROM tarefas WHERE criado_por = ? AND criado_por_tipo = 'admin' AND status = 'Em Andamento'";
$stmt = $conn->prepare($sqlAndamento);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$emAndamento = $stmt->get_result()->fetch_assoc()['total'];

// Pendentes
$sqlPendentes = "SELECT COUNT(*) AS total FROM tarefas WHERE criado_por = ? AND criado_por_tipo = 'admin' AND status = 'Pendente'";
$stmt = $conn->prepare($sqlPendentes);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$pendentes = $stmt->get_result()->fetch_assoc()['total'];

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SIGTO</title>
    <link rel="stylesheet" href="../css/estilo_menu.css">
    <?php include '../componentes/bootstrap_refs.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php include '../componentes/botao_menu.php'; ?>

    <div class="d-md-flex">
        <?php include '../componentes/menu.php'; ?>

        <main class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">
                    <i></i>Dashboard
                </h1>
            </div>

            <!-- Cartões de métricas -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card text-white bg-primary shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">Total de Tarefas</h6>
                            <p class="h4"><?= $totalTarefas ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-success shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">Concluídas</h6>
                            <p class="h4"><?= $concluidas ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-warning shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">Em Andamento</h6>
                            <p class="h4"><?= $emAndamento ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-white bg-danger shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">Pendentes</h6>
                            <p class="h4"><?= $pendentes ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Tarefas por Criticidade</h5>
                            <canvas id="graficoCriticidade" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Tarefas por Equipe</h5>
                            <canvas id="graficoEquipe" height="190"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <script src="../js/js_menu.js"></script>
    <script>
        // Gráfico Criticidade
        const ctx1 = document.getElementById('graficoCriticidade').getContext('2d');
        new Chart(ctx1, {
            type: 'doughnut',
            data: {
                labels: ['Alta', 'Média', 'Baixa'],
                datasets: [{
                    data: [20, 50, 58],
                    backgroundColor: ['#dc3545', '#ffc107', '#198754']
                }]
            },
            options: {
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Gráfico por Equipe
        const ctx2 = document.getElementById('graficoEquipe').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: ['Equipe A', 'Equipe B', 'Equipe C', 'Empilhadeira'],
                datasets: [{
                    label: 'Nº de Tarefas',
                    data: [35, 30, 28, 35],
                    backgroundColor: '#0d6efd'
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
</body>

</html>