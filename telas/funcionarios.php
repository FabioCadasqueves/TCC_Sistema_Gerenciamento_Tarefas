<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../conexao/conexao.php';

// Bloqueia o acesso para quem não for admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$paginaAtual = 'funcionarios';
?>
<!DOCTYPE html>
<html lang="pt-br">


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funcionários - SIGTO</title>

    <link rel="stylesheet" href="../css/estilo_menu.css">
    <?php include '../componentes/bootstrap_refs.php'; ?>
    <link rel="stylesheet" href="../css/estilo_funcionario.css">
</head>

<body>
    <?php include '../componentes/botao_menu.php'; ?>

    <div class="d-md-flex">
        <?php include '../componentes/menu.php'; ?>

        <main class="container-fluid px-3" style="position: relative;">
            <?php if (isset($_GET['cadastro']) || isset($_GET['edicao']) || isset($_GET['exclusao'])): ?>
                <div id="alert-overlay" class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php if (isset($_GET['cadastro']) && $_GET['cadastro'] === 'sucesso'): ?>
                        Funcionário cadastrado com sucesso!
                    <?php elseif (isset($_GET['edicao']) && $_GET['edicao'] === 'sucesso'): ?>
                        Funcionário atualizado com sucesso!
                    <?php elseif (isset($_GET['exclusao']) && $_GET['exclusao'] === 'sucesso'): ?>
                        Funcionário excluído com sucesso!
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="d-flex justify-content-between align-items-center mb-4" style="padding-bottom: 15px">
                <h1 class="h3">
                    <i></i>Funcionários
                </h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNovoFuncionario">
                    + Novo Funcionário
                </button>
            </div>

            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" style="max-height: calc(100vh - 150px); overflow-y: auto; padding-right: 8px;">
                <?php
                $admin_id = $_SESSION['admin_id'];
                $sql = "SELECT u.id, u.nome, u.funcao, u.pin, MAX(e.nome) AS equipe
                        FROM usuarios u 
                        LEFT JOIN membros_equipes me ON me.usuario_id = u.id 
                        LEFT JOIN equipes e ON e.id = me.equipe_id 
                        WHERE u.admin_id = ? AND u.ativo = 1
                        GROUP BY u.id, u.nome, u.funcao, u.pin";


                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $admin_id);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()):
                ?>
                    <div class="col">
                        <div class="card shadow-sm h-100 position-relative">
                            <div class="position-absolute top-0 end-0 mt-2 me-2 d-flex gap-1">
                                <button
                                    class="btn btn-sm btn-outline-warning btn-editar-funcionario"
                                    data-id="<?= $row['id'] ?>"
                                    data-nome="<?= htmlspecialchars($row['nome']) ?>"
                                    data-funcao="<?= $row['funcao'] ?>"
                                    data-equipe="<?= $row['equipe'] ?>"
                                    data-pin="<?= $row['pin'] ?>"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditarFuncionario">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button
                                    class="btn btn-sm btn-outline-danger btn-excluir-funcionario"
                                    data-id="<?= $row['id'] ?>"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalConfirmarExclusao">
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
                                <h5 class="card-title mb-1"><?= htmlspecialchars($row['nome']) ?></h5>
                                <p class="mb-1 text-muted"><strong>Função:</strong> <?= htmlspecialchars($row['funcao']) ?></p>
                                <p class="mb-1 text-muted"><strong>Equipe:</strong> <?= htmlspecialchars($row['equipe'] ?? 'Nenhuma') ?></p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </main>
    </div>

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
                            <option>Operador</option>
                            <option>Mecânico</option>
                        </select>
                    </div>
                    <!--<div class="mb-3">
                        <label for="equipeFuncionario" class="form-label">Equipe</label>
                        <select class="form-select" id="equipeFuncionario" name="equipe">
                            <option value="0">Nenhuma</option>
                            <option>Equipe A</option>
                            <option>Equipe B</option>
                            <option>Equipe C</option>
                            <option>Equipe Empilhadeira</option>
                        </select>
                    </div>-->
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
                            <option selected>Operador</option>
                            <option>Mecânico</option>
                        </select>
                    </div>
                    <!--<div class="mb-3">
                        <label for="editarEquipeFuncionario" class="form-label">Equipe</label>
                        <select class="form-select" id="editarEquipeFuncionario">
                            <option>Equipe A</option>
                            <option selected>Equipe B</option>
                            <option>Equipe C</option>
                            <option>Equipe Empilhadeira</option>
                        </select>
                    </div>-->
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


    <script src="../js/js_menu.js"></script>
    <script src="../js/js_funcionarios.js"></script>
</body>

</html>