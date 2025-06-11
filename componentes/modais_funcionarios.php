<!-- Modal Novo Funcionário -->
<div class="modal fade" id="modalNovoFuncionario" tabindex="-1" aria-labelledby="modalNovoFuncionarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" action="../logica/controladores/cadastrar_funcionario.php" method="POST">
            <div class="modal-header">
                <h5 class="modal-title" id="modalNovoFuncionarioLabel">Cadastrar Novo Funcionário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="nomeFuncionario" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="nomeFuncionario" name="nome" required>
                </div>
                <div class="mb-3">
                    <label for="funcaoFuncionario" class="form-label">Função</label>
                    <select class="form-select" id="funcaoFuncionario" name="funcao" required>
                        <option></option>
                        <option>Operador</option>
                        <option>Mecânico</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="pinFuncionario" class="form-label">PIN de Acesso</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="pinFuncionario" name="pin" maxlength="6" readonly>
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
        <form class="modal-content" action="../logica/controladores/editar_funcionario.php" method="POST">
            <input type="hidden" name="id" id="editarIdFuncionario">
            <div class="modal-header">
                <h5 class="modal-title" id="modalEditarFuncionarioLabel">Editar Funcionário</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="editarNomeFuncionario" class="form-label">Nome</label>
                    <input type="text" class="form-control" name="nome" id="editarNomeFuncionario" required>
                </div>
                <div class="mb-3">
                    <label for="editarFuncaoFuncionario" class="form-label">Função</label>
                    <select class="form-select" name="funcao" id="editarFuncaoFuncionario" required>
                        <option>Operador</option>
                        <option>Mecânico</option>
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
        <form action="../logica/controladores/excluir_funcionario.php" method="POST" class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="modalConfirmarExclusaoLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                Tem certeza que deseja excluir este funcionário?
                <input type="hidden" name="id" id="excluirIdFuncionario">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-danger">Excluir</button>
            </div>
        </form>
    </div>
</div>