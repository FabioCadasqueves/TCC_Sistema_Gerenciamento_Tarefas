<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: login.php');
    exit;
}
$paginaAtual = 'solicitacoes';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitações - SIGTO</title>
    <link rel="stylesheet" href="../css/estilo_menu.css">
    <?php include '../componentes/bootstrap_refs.php'; ?>
    <link rel="stylesheet" href="../css/estilo_funcionarios.css">
    <style>
        .list-group-item+.list-group-item {
            margin-top: 1rem;
        }
    </style>
</head>

<body>
    <?php include '../componentes/botao_menu.php'; ?>

    <div class="d-md-flex">
        <?php include '../componentes/menu.php'; ?>

        <main class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-4" style="padding-bottom: 15px">
                <h1 class="h3">Solicitações de Tarefas Críticas</h1>
            </div>

            <div style="max-height: 100vh; overflow-y: auto;" class="custom-scrollbar">
                <ul class="list-group">
                    <?php for ($i = 1; $i <= 8; $i++): ?>
                        <li class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 p-3 shadow-sm rounded-3 mb-1 border border-light-subtle">
                            <div class="flex-grow-1">
                                <div class="fw-semibold text-primary">Solicitação #<?= $i ?> - Apoio com empilhadeira</div>
                                <small class="text-muted d-block mt-1">
                                    <i class="bi bi-person-circle me-1"></i>Solicitante: Maria - Mecânica<br>
                                    <i class="bi bi-box-arrow-in-right me-1"></i>Sugerido para: João - Operador
                                </small>
                            </div>
                            <div>
                                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAvaliarSolicitacao">
                                    Avaliar
                                </button>
                            </div>
                        </li>
                    <?php endfor; ?>
                </ul>
            </div>


        </main>
    </div>

    <!-- MODAL AVALIAR SOLICITACAO -->
    <div class="modal fade" id="modalAvaliarSolicitacao" tabindex="-1" aria-labelledby="modalAvaliarSolicitacaoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAvaliarSolicitacaoLabel">Avaliar Solicitação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tituloTarefa" class="form-label">Título da Tarefa</label>
                        <input type="text" class="form-control" id="tituloTarefa" value="Movimentação de peça pesada" required>
                    </div>

                    <div class="mb-3">
                        <label for="criticidade" class="form-label">Criticidade</label>
                        <select class="form-select" id="criticidade" required>
                            <option>Baixa</option>
                            <option>Média</option>
                            <option selected>Alta</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="responsavel" class="form-label">Atribuir para</label>
                        <select class="form-select" id="responsavel" required>
                            <option value="">Selecione um funcionário</option>
                            <option selected>João - Operador</option>
                            <option>Ana - Operadora</option>
                            <option>Lucas - Supervisor</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Atribuir Tarefa</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/js_menu.js"></script>
</body>

</html>