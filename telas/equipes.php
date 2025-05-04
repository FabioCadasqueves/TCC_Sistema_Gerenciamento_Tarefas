<!DOCTYPE html>
<?php $paginaAtual = 'equipes'; ?>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipes - SIGTO</title>

    <link rel="stylesheet" href="../css/estilo_menu.css">
    <?php include '../componentes/bootstrap_refs.php'; ?>
</head>

<div class="modal fade" id="modalNovaEquipe" tabindex="-1" aria-labelledby="modalNovaEquipeLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNovaEquipeLabel">Criar Nova Equipe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="nomeEquipe" class="form-label">Nome da Equipe</label>
                    <input type="text" class="form-control" id="nomeEquipe" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Selecione os Membros</label>

                    <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                        <strong class="d-block mb-2">Operadores</strong>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="João - Operador" id="opJoao">
                            <label class="form-check-label" for="opJoao">João</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="Ana - Operadora" id="opAna">
                            <label class="form-check-label" for="opAna">Ana</label>
                        </div>

                        <strong class="d-block mt-3 mb-2">Mecânicos</strong>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="Maria - Mecânica" id="mecMaria">
                            <label class="form-check-label" for="mecMaria">Maria</label>
                        </div>

                        <strong class="d-block mt-3 mb-2">Supervisores</strong>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="Lucas - Supervisor" id="supLucas">
                            <label class="form-check-label" for="supLucas">Lucas</label>
                        </div>
                    </div>

                    <div class="form-text">Marque os funcionários que farão parte da equipe.</div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Salvar Equipe</button>
            </div>
        </form>
    </div>
</div>





<body>

    <?php include '../componentes/botao_menu.php'; ?>

    <div class="d-md-flex">
        <?php include '../componentes/menu.php'; ?>

        <main class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Equipes</h1>
                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNovaEquipe">+ Nova Equipe</a>

            </div>

            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <div class="col">
                        <div class="card shadow-sm h-100">
                            <div class="card-body">
                                <h5 class="card-title">Equipe <?= $i ?></h5>
                                <p class="card-text mb-1">Membros: <?= rand(2, 8) ?></p>
                                <p class="card-text text-success">Status: Ativa</p>
                                <div class="position-absolute top-0 end-0 mt-2 me-2 d-flex gap-2">
                                    <a href="#" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalVerEquipe" title="Visualizar">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="#" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditarEquipe" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="#" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalExcluirEquipe" title="Excluir">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </main>
    </div>

    <div class="modal fade" id="modalVerEquipe" tabindex="-1" aria-labelledby="modalVerEquipeLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalVerEquipeLabel">Equipe Alfa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        <li class="list-group-item">João - Operador</li>
                        <li class="list-group-item">Maria - Mecânica</li>
                        <li class="list-group-item">Lucas - Supervisor</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditarEquipe" tabindex="-1" aria-labelledby="modalEditarEquipeLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarEquipeLabel">Editar Equipe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editarNomeEquipe" class="form-label">Nome da Equipe</label>
                        <input type="text" class="form-control" id="editarNomeEquipe" value="Equipe Alfa" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Editar Membros da Equipe</label>
                        <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            <strong class="d-block mb-2">Operadores</strong>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="João - Operador" id="editarJoao" checked>
                                <label class="form-check-label" for="editarJoao">João</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Ana - Operadora" id="editarAna">
                                <label class="form-check-label" for="editarAna">Ana</label>
                            </div>

                            <strong class="d-block mt-3 mb-2">Mecânicos</strong>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Maria - Mecânica" id="editarMaria" checked>
                                <label class="form-check-label" for="editarMaria">Maria</label>
                            </div>

                            <strong class="d-block mt-3 mb-2">Supervisores</strong>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="Lucas - Supervisor" id="editarLucas">
                                <label class="form-check-label" for="editarLucas">Lucas</label>
                            </div>
                        </div>
                        <div class="form-text">Desmarque para remover ou marque para adicionar membros.</div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EXCLUIR EQUIPE -->
    <div class="modal fade" id="modalExcluirEquipe" tabindex="-1" aria-labelledby="modalExcluirEquipeLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalExcluirEquipeLabel">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <p>Tem certeza que deseja excluir a equipe <strong>Alfa</strong>?</p>
                    <p class="text-danger small">Essa ação não poderá ser desfeita.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger">Excluir</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/js_menu.js"></script>
</body>

</html>