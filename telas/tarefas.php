<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../conexao/conexao.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$admin_id = $_SESSION['admin_id'];
$usuario_id = $_SESSION['usuario_id'];
$tipo_usuario = $_SESSION['tipo_usuario'];
$paginaAtual = 'tarefas';

$filtro_usuario_id = $_GET['usuario_id'] ?? 'meus';

// Consulta tarefas filtradas, ordenadas por criticidade personalizada
$ordenacaoCriticidade = "
    CASE t.criticidade
        WHEN 'Alta' THEN 1
        WHEN 'Média' THEN 2
        WHEN 'Baixa' THEN 3
    END ASC";

if ($filtro_usuario_id === 'meus') {
    $sql = "SELECT t.*, u.nome AS responsavel_nome
            FROM tarefas t
            INNER JOIN usuarios u ON u.id = t.atribuido_para
            WHERE t.atribuido_para = ?
            ORDER BY $ordenacaoCriticidade, t.criado_em DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
} else {
    $sql = "SELECT t.*, u.nome AS responsavel_nome
            FROM tarefas t
            INNER JOIN usuarios u ON u.id = t.atribuido_para
            WHERE t.atribuido_para = ?
            ORDER BY $ordenacaoCriticidade, t.criado_em DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $filtro_usuario_id);
}

$stmt->execute();
$resultado = $stmt->get_result();

// Lista de usuários para o select
$sql = "SELECT id, nome, funcao FROM usuarios 
        WHERE admin_id = ? AND ativo = 1
        ORDER BY funcao, nome";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$resultadoUsuarios = $stmt->get_result();

$usuariosPorFuncao = [];
while ($usuario = $resultadoUsuarios->fetch_assoc()) {
    if ($usuario['id'] == $usuario_id) continue;
    $funcao = $usuario['funcao'];
    if (!isset($usuariosPorFuncao[$funcao])) {
        $usuariosPorFuncao[$funcao] = [];
    }
    $usuariosPorFuncao[$funcao][] = $usuario;
}
?>

<!DOCTYPE html>
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
            <?php if (isset($_GET['cadastro']) && $_GET['cadastro'] === 'sucesso'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    ✅ Tarefa criada com sucesso!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php endif; ?>

            <div class="sticky-top bg-white border-bottom" style="z-index: 1020;">
                <div class="container-fluid px-0 py-3">
                    <div class="d-flex justify-content-between align-items-center flex-column flex-md-row gap-3">
                        <div class="w-100 w-md-auto">
                            <h1 class="h4 mb-0">Tarefas</h1>
                        </div>
                        <div class="d-flex gap-2 w-100 w-md-auto align-items-center">
                            <select class="form-select" id="filtroResponsavel">
                                <option value="meus" <?= ($filtro_usuario_id === 'meus') ? 'selected' : '' ?>>Minhas tarefas</option>
                                <?php foreach ($usuariosPorFuncao as $funcao => $usuarios): ?>
                                    <optgroup label="<?= htmlspecialchars($funcao) ?>">
                                        <?php foreach ($usuarios as $u): ?>
                                            <option value="<?= $u['id'] ?>" <?= ($filtro_usuario_id == $u['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($u['nome']) ?> - <?= htmlspecialchars($u['funcao']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>

                            <button class="btn btn-primary d-flex align-items-center gap-2" style="height: 40px;"
                                data-bs-toggle="modal" data-bs-target="#modalAdicionarTarefa" title="Adicionar Tarefa">
                                <i class="bi bi-plus-circle fs-4"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-none d-md-flex justify-content-end mb-3 mt-3">
                <button class="btn btn-outline-secondary me-2 btn-visualizacao" onclick="mudarVisualizacao('grade', this)" title="Visualizar em grade">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                </button>
                <button class="btn btn-outline-secondary btn-visualizacao" onclick="mudarVisualizacao('lista', this)" title="Visualizar em lista">
                    <i class="bi bi-list-ul"></i>
                </button>
            </div>

            <div id="containerTarefas" class="row modo-grade" style="max-height: calc(100vh - 150px); overflow-y: auto; padding-right: 2px;">
                <?php while ($tarefa = $resultado->fetch_assoc()): ?>
                    <div class="col">
                        <div class="tarefa-lista d-flex justify-content-between align-items-center py-3 px-2">
                            <div class="info-bloco">
                                <h6 class="mb-1 fw-semibold"><?= htmlspecialchars($tarefa['descricao']) ?></h6>
                                <div class="small text-muted responsavel-linha">
                                    <span>Criticidade: <strong><?= $tarefa['criticidade'] ?></strong></span>
                                    <span class="responsavel">Responsável: <?= $tarefa['responsavel_nome'] ?></span>
                                </div>
                                <div class="small mt-1">
                                    Status: <span class="badge bg-secondary"><?= $tarefa['status'] ?></span>
                                </div>
                            </div>

                            <div class="acoes-bloco d-flex gap-2">
                                <?php if ($tarefa['status'] === 'Pendente'): ?>
                                    <!-- Botão Iniciar -->
                                    <button class="btn btn-sm btn-outline-primary iniciar-tarefa" data-id="<?= $tarefa['id'] ?>" title="Iniciar">
                                        <i class="bi bi-play-fill"></i>
                                    </button>
                                <?php elseif ($tarefa['status'] === 'Em andamento'): ?>
                                    <!-- Botão Concluir -->
                                    <button class="btn btn-sm btn-outline-success concluir-tarefa" data-id="<?= $tarefa['id'] ?>" title="Concluir">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                <?php endif; ?>

                                <?php if ($tarefa['status'] !== 'Concluída'): ?>
                                    <!-- Botão Editar -->
                                    <button class="btn btn-sm btn-outline-secondary editar-tarefa" data-id="<?= $tarefa['id'] ?>" title="Editar">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <!-- Botão Excluir -->
                                    <button class="btn btn-sm btn-outline-danger excluir-tarefa" data-id="<?= $tarefa['id'] ?>" title="Excluir">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>

                        </div>
                        <hr class="my-0">
                    </div>
                <?php endwhile; ?>
            </div>
        </main>
    </div>

    <?php
    $usuario_logado_id = $_SESSION['usuario_id'];

    $sql = "SELECT id, nome, funcao FROM usuarios 
        WHERE admin_id = ? AND ativo = 1
        ORDER BY funcao, nome";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $ultimoCargo = '';
    ?>

    <!-- Modal Nova Tarefa -->
    <div class="modal fade" id="modalAdicionarTarefa" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="../logica/controladores/adicionar_tarefa.php">
                <div class="modal-header">
                    <h5 class="modal-title">Nova Tarefa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Descrição da Tarefa</label>
                        <input type="text" class="form-control" name="titulo_tarefa" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Responsável</label>
                        <select class="form-select"
                            id="responsavel_id"
                            name="responsavel_id"
                            required
                            data-user-logado="<?= $usuario_logado_id ?>"
                            data-tipo-usuario="<?= $_SESSION['tipo_usuario'] ?>">
                            <option value="">Selecione o responsável</option>
                            <option value="<?= $usuario_logado_id ?>">Eu mesmo</option>
                            <?php while ($usuario = mysqli_fetch_assoc($resultado)): ?>
                                <?php if ($usuario['id'] == $usuario_logado_id) continue; ?>
                                <?php if ($ultimoCargo !== $usuario['funcao']): ?>
                                    <?php
                                    if ($ultimoCargo !== '') echo '</optgroup>';
                                    $ultimoCargo = $usuario['funcao'];
                                    ?>
                                    <optgroup label="<?= htmlspecialchars($usuario['funcao']) ?>">
                                    <?php endif; ?>
                                    <option value="<?= $usuario['id'] ?>"><?= htmlspecialchars($usuario['nome']) ?></option>
                                <?php endwhile; ?>
                                <?php if ($ultimoCargo !== '') echo '</optgroup>'; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Criticidade</label>
                        <select class="form-select" id="criticidade" name="criticidade" required>
                            <option value="Baixa">Baixa</option>
                            <option value="Média">Média</option>
                            <option value="Alta">Alta</option>
                        </select>
                    </div>

                    <div class="alert alert-warning d-none" id="alertaCriticidadeAlta">
                        <strong>Atenção:</strong> Tarefas de alta criticidade exigem aprovação do gestor. Ao confirmar, será enviada uma solicitação para aprovação.
                    </div>

                    <div class="mb-3 d-none" id="divComentarioGestor">
                        <label class="form-label">Comentário ao Gestor (opcional)</label>
                        <textarea class="form-control" name="comentario_gestor" rows="2" placeholder="Justifique brevemente a alta criticidade (opcional)."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="btnCriarTarefa">Criar Tarefa</button>
                </div>
            </form>
        </div>
    </div>


    <?php
    $usuario_logado_id = $_SESSION['usuario_id'];

    $sql = "SELECT id, nome, funcao FROM usuarios 
        WHERE admin_id = ? AND ativo = 1
        ORDER BY funcao, nome";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $ultimoCargo = '';
    ?>

    <!-- Modal Editar Tarefa -->
    <div class="modal fade" id="modalEditarTarefa" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="../logica/controladores/editar_tarefa.php">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Tarefa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="tarefa_id" id="editar_tarefa_id">

                    <div class="mb-3">
                        <label class="form-label">Descrição da Tarefa</label>
                        <input type="text" class="form-control" name="titulo_tarefa" id="editar_titulo_tarefa" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Responsável</label>
                        <select class="form-select" name="responsavel_id" id="editar_responsavel_id" required>
                            <option value="">Selecione o responsável</option>
                            <option value="<?= $usuario_logado_id ?>">Eu mesmo</option>
                            <?php while ($usuario = mysqli_fetch_assoc($resultado)): ?>
                                <?php if ($usuario['id'] == $usuario_logado_id) continue; ?>
                                <?php if ($ultimoCargo !== $usuario['funcao']): ?>
                                    <?php
                                    if ($ultimoCargo !== '') echo '</optgroup>';
                                    $ultimoCargo = $usuario['funcao'];
                                    ?>
                                    <optgroup label="<?= htmlspecialchars($usuario['funcao']) ?>">
                                    <?php endif; ?>
                                    <option value="<?= $usuario['id'] ?>"><?= htmlspecialchars($usuario['nome']) ?></option>
                                <?php endwhile; ?>
                                <?php if ($ultimoCargo !== '') echo '</optgroup>'; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Criticidade</label>
                        <select class="form-select" name="criticidade" id="editar_criticidade" required>
                            <option value="Baixa">Baixa</option>
                            <option value="Média">Média</option>
                            <option value="Alta">Alta</option>
                        </select>
                    </div>

                    <div class="alert alert-warning d-none" id="editar_alertaCriticidadeAlta">
                        <strong>Atenção:</strong> Tarefas de alta criticidade só podem ser atribuídas a outro funcionário com aprovação do gestor.
                    </div>

                    <div class="mb-3 d-none" id="editar_divComentarioGestor">
                        <label class="form-label">Comentário ao Gestor (opcional)</label>
                        <textarea class="form-control" name="comentario_gestor" id="editar_comentario_gestor" rows="2" placeholder="Justifique brevemente a alta criticidade (opcional)."></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>
            </form>
        </div>
    </div>


    <script src="../js/js_menu.js"></script>
    <script src="../js/js_tarefas.js"></script>
</body>

</html>