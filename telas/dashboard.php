<?php
session_start();

// Impede acesso se ninguém estiver logado
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['tipo_usuario'])) {
    header('Location: login.php');
    exit;
}

$paginaAtual = 'dashboard';
require_once '../conexao/conexao.php';

$admin_id = $_SESSION['admin_id'];

// Contadores principais
function contar($conn, $admin_id, $status = null)
{
    $sql = "SELECT COUNT(*) AS total FROM tarefas WHERE criado_por = ? AND criado_por_tipo = 'admin'";
    if ($status) $sql .= " AND status = ?";
    $stmt = $conn->prepare($sql);
    $status ? $stmt->bind_param("is", $admin_id, $status) : $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc()['total'];
}

$totalTarefas = contar($conn, $admin_id);
$concluidas = contar($conn, $admin_id, 'Concluída');
$emAndamento = contar($conn, $admin_id, 'Em andamento');
$pendentes = contar($conn, $admin_id, 'Pendente');

// Criticidade
$sql = "SELECT criticidade, COUNT(*) AS total FROM tarefas 
        WHERE criado_por = ? AND criado_por_tipo = 'admin'
        GROUP BY criticidade";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$criticidadeLabels = [];
$criticidadeData = [];
while ($row = $result->fetch_assoc()) {
    $criticidadeLabels[] = $row['criticidade'];
    $criticidadeData[] = $row['total'];
}

// Equipes
$sql = "SELECT e.nome AS equipe, COUNT(t.id) AS total 
        FROM tarefas t
        INNER JOIN equipes e ON t.equipe_id = e.id
        WHERE t.criado_por = ? AND t.criado_por_tipo = 'admin'
        GROUP BY e.nome";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$equipeLabels = [];
$equipeData = [];
while ($row = $result->fetch_assoc()) {
    $equipeLabels[] = $row['equipe'];
    $equipeData[] = $row['total'];
}

// Funcionários
$sql = "SELECT u.nome, t.status, COUNT(*) AS total
        FROM tarefas t
        INNER JOIN usuarios u ON t.atribuido_para = u.id
        WHERE t.criado_por = ? AND t.criado_por_tipo = 'admin'
        GROUP BY u.nome, t.status";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$funcionarios = [];
$statuses = ['Concluída', 'Em andamento', 'Pendente'];
while ($row = $result->fetch_assoc()) {
    $funcionarios[$row['nome']][$row['status']] = $row['total'];
}
foreach ($funcionarios as &$f) foreach ($statuses as $s) if (!isset($f[$s])) $f[$s] = 0;
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - SIGTO</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/estilo_menu.css">
    <link rel="stylesheet" href="../css/estilo_titulo_paginas.css">
    <?php include '../componentes/bootstrap_refs.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <?php include '../componentes/botao_menu.php'; ?>
    <div class="d-md-flex">
        <?php include '../componentes/menu.php'; ?>
        <main class="container py-4" style="max-width: 1200px;">
            <div class="mb-4">
                <h1 class="titulo-pagina">Dashboard de Tarefas</h1>
            </div>

            <!-- Cartões de Métricas -->
            <div class="row g-4 mb-4">
                <?php
                $cards = [
                    ['Total de Tarefas', $totalTarefas, 'primary', 'bi-list-check'],
                    ['Concluídas', $concluidas, 'success', 'bi-check-circle'],
                    ['Em Andamento', $emAndamento, 'warning text-dark', 'bi-play-circle'],
                    ['Pendentes', $pendentes, 'danger', 'bi-clock-history']
                ];
                foreach ($cards as [$label, $valor, $cor, $icone]) {
                    echo "<div class='col-md-3'>
                        <div class='card shadow-sm border-0'>
                            <div class='card-body d-flex align-items-center gap-3'>
                                <div class='rounded-circle bg-{$cor} d-flex align-items-center justify-content-center text-white' style='width: 45px; height: 45px;'>
                                    <i class='bi {$icone}'></i>
                                </div>
                                <div>
                                    <div class='small text-muted'>{$label}</div>
                                    <h5 class='mb-0 fw-bold'>{$valor}</h5>
                                </div>
                            </div>
                        </div>
                      </div>";
                }
                ?>
            </div>

            <!-- Gráficos -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-3">Distribuição por Criticidade</h6>
                            <canvas id="graficoCriticidade" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-3">Tarefas por Equipe</h6>
                            <canvas id="graficoEquipes" height="200"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <h6 class="fw-semibold mb-3">Tarefas por Funcionário</h6>
                            <canvas id="graficoFuncionarios" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../js/js_menu.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Criticidade
        new Chart(document.getElementById('graficoCriticidade'), {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($criticidadeLabels) ?>,
                datasets: [{
                    data: <?= json_encode($criticidadeData) ?>,
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

        // Equipes
        new Chart(document.getElementById('graficoEquipes'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($equipeLabels) ?>,
                datasets: [{
                    label: 'Nº de Tarefas',
                    data: <?= json_encode($equipeData) ?>,
                    backgroundColor: '#0d6efd'
                }]
            },
            options: {
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Funcionários (stacked)
        const funcionariosData = <?= json_encode($funcionarios) ?>;
        const labelsFuncionarios = Object.keys(funcionariosData);

        const dadosFuncionarios = {
            labels: labelsFuncionarios,
            datasets: [{
                    label: 'Concluída',
                    data: labelsFuncionarios.map(nome => funcionariosData[nome]['Concluída']),
                    backgroundColor: '#198754'
                },
                {
                    label: 'Em andamento',
                    data: labelsFuncionarios.map(nome => funcionariosData[nome]['Em andamento']),
                    backgroundColor: '#ffc107'
                },
                {
                    label: 'Pendente',
                    data: labelsFuncionarios.map(nome => funcionariosData[nome]['Pendente']),
                    backgroundColor: '#dc3545'
                }
            ]
        };


        new Chart(document.getElementById('graficoFuncionarios'), {
            type: 'bar',
            data: dadosFuncionarios,
            options: {
                indexAxis: 'y',
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        beginAtZero: true
                    },
                    y: {
                        stacked: true
                    }
                }
            }
        });
    </script>
</body>

</html>