<!DOCTYPE html>
<?php $paginaAtual = 'funcionarios'; ?>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funcionários - SIGTO</title>

    <link rel="stylesheet" href="../css/estilo_menu.css">
    <?php include '../componentes/bootstrap_refs.php'; ?>
</head>

<body>
    <?php include '../componentes/botao_menu.php'; ?>

    <div class="d-md-flex">
        <?php include '../componentes/menu.php'; ?>

        <main class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">
                    <i></i>Funcionários
                </h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNovoFuncionario">
                    + Novo Funcionário
                </button>
            </div>

            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <div class="col">
                        <div class="card shadow-sm h-100 position-relative">
                            <div class="position-absolute top-0 end-0 mt-2 me-2 d-flex gap-1">
                                <button class="btn btn-sm btn-outline-warning" title="Editar" data-bs-toggle="modal" data-bs-target="#modalEditarFuncionario">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" title="Excluir" data-bs-toggle="modal" data-bs-target="#modalConfirmarExclusao">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            <div class="card-body text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor"
                                    class="bi bi-person-circle text-secondary mb-3" viewBox="0 0 16 16">
                                    <path d="M11 10a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                                    <path fill-rule="evenodd"
                                        d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37c.69-.863 1.84-1.37 3.093-1.37h4.75c1.253 0 2.403.507 3.093 1.37A7 7 0 0 0 8 1z" />
                                </svg>
                                <h5 class="card-title mb-1">Funcionário <?= $i ?></h5>
                                <p class="mb-1 text-muted"><strong>Função:</strong> Operador</p>
                                <p class="mb-1 text-muted"><strong>Equipe:</strong> Equipe A</p>
                            </div>


                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </main>
    </div>

    <!-- Modal Novo Funcionário -->
    <div class="modal fade" id="modalNovoFuncionario" tabindex="-1" aria-labelledby="modalNovoFuncionarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNovoFuncionarioLabel">Cadastrar Novo Funcionário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nomeFuncionario" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nomeFuncionario" required>
                    </div>
                    <div class="mb-3">
                        <label for="funcaoFuncionario" class="form-label">Função</label>
                        <select class="form-select" id="funcaoFuncionario" required>
                            <option>Operador</option>
                            <option>Mecânico</option>
                            <option>Supervisor</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="equipeFuncionario" class="form-label">Equipe</label>
                        <select class="form-select" id="equipeFuncionario">
                            <option>Nenhuma</option>
                            <option>Equipe A</option>
                            <option>Equipe B</option>
                            <option>Equipe C</option>
                            <option>Equipe Empilhadeira</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="pinFuncionario" class="form-label">PIN de Acesso</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="pinFuncionario" readonly>
                            <button class="btn btn-outline-secondary" type="button" id="copiarPin" title="Copiar PIN">
                                <i class="bi bi-clipboard"></i>
                            </button>

                        </div>
                        <small id="mensagemCopiado" class="text-success ms-2 d-none">PIN copiado!</small>
                        <div class="form-text">Gerado automaticamente. Compartilhe com o funcionário.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Editar Funcionário -->
    <div class="modal fade" id="modalEditarFuncionario" tabindex="-1" aria-labelledby="modalEditarFuncionarioLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarFuncionarioLabel">Editar Funcionário</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editarNomeFuncionario" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="editarNomeFuncionario" value="Funcionário 1" required>
                    </div>
                    <div class="mb-3">
                        <label for="editarFuncaoFuncionario" class="form-label">Função</label>
                        <select class="form-select" id="editarFuncaoFuncionario" required>
                            <option selected>Operador</option>
                            <option>Mecânico</option>
                            <option>Supervisor</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="editarEquipeFuncionario" class="form-label">Equipe</label>
                        <select class="form-select" id="editarEquipeFuncionario">
                            <option>Equipe A</option>
                            <option selected>Equipe B</option>
                            <option>Equipe C</option>
                            <option>Equipe Empilhadeira</option>
                        </select>
                    </div>
                    <div class="mb-3 position-relative">
                        <label for="editarPinFuncionario" class="form-label">PIN de Acesso</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="editarPinFuncionario" value="1234" maxlength="6" readonly>
                            <button class="btn btn-outline-secondary" type="button" id="btnMostrarPin">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-outline-secondary" type="button" id="btnCopiarPinEditar">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                        <div class="form-text">Este PIN é usado para o acesso do funcionário e não pode ser alterado.</div>
                        <div id="feedbackPin" class="form-text text-success d-none">PIN copiado!</div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Confirmar Exclusão -->
    <div class="modal fade" id="modalConfirmarExclusao" tabindex="-1" aria-labelledby="modalConfirmarExclusaoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalConfirmarExclusaoLabel">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    Tem certeza que deseja excluir este funcionário?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger">Excluir</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/js_menu.js"></script>
    <script src="../js/js_pin_funcionarios.js"></script>
</body>

</html>