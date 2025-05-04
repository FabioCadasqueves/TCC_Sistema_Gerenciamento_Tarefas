<!DOCTYPE html>
<?php $paginaAtual = 'tarefas'; ?>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarefas - SIGTO</title>

    <link rel="stylesheet" href="../css/estilo_menu.css">
    <link rel="stylesheet" href="../css/estilo_tarefas.css">
    <?php include '../componentes/bootstrap_refs.php'; ?>
</head>

<body>

    <?php include '../componentes/botao_menu.php'; ?>

    <div class="d-md-flex">
        <?php include '../componentes/menu.php'; ?>

        <main class="container-fluid px-3" style="position: relative;">

            <!-- Cabeçalho fixo no topo da área principal -->
            <div class="sticky-top bg-white border-bottom" style="z-index: 1020;">
                <div class="container-fluid px-0 py-3">
                    <div class="d-flex justify-content-between align-items-center flex-column flex-md-row gap-3">
                        <div class="w-100 w-md-auto">
                            <h1 class="h4 mb-0">Tarefas</h1>
                        </div>
                        <div class="d-flex gap-2 w-100 w-md-auto align-items-center">
                            <select class="form-select" required>
                                <optgroup label="Operadores">
                                    <option>Minhas tarefas</option>
                                    <option>Lucas - Operador</option>
                                    <option>Rafael - Operador</option>
                                    <option>Fernanda - Operadora</option>
                                    <!-- ... -->
                                </optgroup>

                                <optgroup label="Mecânicos">
                                    <option>João - Mecânico</option>
                                    <option disabled>Maria - Mecânica</option>
                                    <!-- ... -->
                                </optgroup>
                            </select>

                            <button class="btn btn-primary d-flex align-items-center gap-2" style="height: 40px;"
                                data-bs-toggle="modal" data-bs-target="#modalAdicionarTarefa" title="Adicionar Tarefa">
                                <i class="bi bi-plus-circle fs-4"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botões de visualização -->
            <div class="d-none d-md-flex justify-content-end mb-3 mt-3">
                <button class="btn btn-outline-secondary me-2 btn-visualizacao"
                    onclick="mudarVisualizacao('grade', this)"
                    title="Visualizar em grade">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                </button>
                <button class="btn btn-outline-secondary btn-visualizacao"
                    onclick="mudarVisualizacao('lista', this)"
                    title="Visualizar em lista">
                    <i class="bi bi-list-ul"></i>
                </button>
            </div>

            <!-- Lista de tarefas -->
            <div id="containerTarefas" class="row modo-grade"
                style="max-height: calc(100vh - 150px); overflow-y: auto; padding-right: 2px;">
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <div class="col">
                        <div class="tarefa-lista d-flex justify-content-between align-items-center py-3 px-2">
                            <div class="info-bloco">
                                <h6 class="mb-1 fw-semibold">Tarefa <?= $i ?> - Troca de peça</h6>
                                <div class="small text-muted responsavel-linha">
                                    <span>Criticidade: <strong>Média</strong></span>
                                    <span class="responsavel">Responsável: João</span>
                                </div>
                                <div class="small mt-1">
                                    Status: <span class="badge bg-warning text-dark">Em andamento</span>
                                </div>
                            </div>

                            <div class="acoes-bloco d-flex gap-2">
                                <button class="btn btn-sm btn-outline-success" title="Concluir">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" title="Pausar">
                                    <i class="bi bi-pause-fill"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Excluir">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <hr class="my-0">
                    </div>
                <?php endfor; ?>
            </div>
        </main>

    </div>

    <!-- Modal Nova Tarefa -->
    <div class="modal fade" id="modalAdicionarTarefa" tabindex="-1" aria-labelledby="modalNovaTarefaLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNovaTarefaLabel">Nova Tarefa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Descrição da Tarefa</label>
                        <input type="text" class="form-control" id="titulo" required>
                    </div>
                    <div class="mb-3">
                        <label for="responsavel" class="form-label">Responsável</label>
                        <select class="form-select" id="responsavel" required>
                            <option selected>Você mesmo</option>
                            <option>João - Operador</option>
                            <option>José - Mecânico</option>
                            <option>Luiz - Mecânico</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="criticidade" class="form-label">Criticidade</label>
                        <select class="form-select" id="criticidade" required>
                            <option>Baixa</option>
                            <option>Média</option>
                            <option>Alta</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Criar Tarefa</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../js/js_menu.js"></script>
    <script src="../js/js_tarefas.js"></script>
</body>

</html>