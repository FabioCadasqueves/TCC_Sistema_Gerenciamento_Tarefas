<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../conexao/conexao.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

if (
    $_SESSION['tipo_usuario'] === 'admin' &&
    (!isset($_GET['usuario_id']) || empty($_GET['usuario_id']))
) {
    header('Location: tarefas.php?usuario_id=todos');
    exit;
}

$admin_id = $_SESSION['admin_id'];
$usuario_id = $_SESSION['usuario_id'];
$tipo_usuario = $_SESSION['tipo_usuario'];
$paginaAtual = 'tarefas';


$filtro_usuario_id = $_GET['usuario_id']
    ?? (
        $_SESSION['tipo_usuario'] === 'admin'
        ? 'admin_' . $_SESSION['usuario_id']
        : 'funcionario_' . $_SESSION['usuario_id']
    );

if ($filtro_usuario_id === 'todos') {
    $filtro_tipo = 'todos';
    $filtro_id = null;
} else {
    list($filtro_tipo, $filtro_id) = explode('_', $filtro_usuario_id);
}


// Consulta tarefas filtradas, ordenadas por criticidade personalizada
$statusFiltro = $_GET['status'] ?? 'todos';

// Define o trecho do WHERE para o status
if ($statusFiltro === 'todos') {
    $statusWhere = "";  // Não filtra nada
} else {
    $statusWhere = "AND t.status = ?";
}


$ordenacaoCustomizada = "
    CASE t.status
        WHEN 'Em andamento' THEN 0
        WHEN 'Pendente' THEN 1
        WHEN 'Concluída' THEN 2
        ELSE 3
    END,
    CASE t.criticidade
        WHEN 'Alta' THEN 1
        WHEN 'Média' THEN 2
        WHEN 'Baixa' THEN 3
        ELSE 4
    END,
    t.criado_em DESC
";



$statusFiltro = $_GET['status'] ?? 'todos';

if ($statusFiltro === 'todos') {
    $statusWhere = "";  // Não filtra status
} else {
    $statusWhere = "AND t.status = ?";
}

// ADMIN visualizando todos
if ($filtro_tipo === 'todos' && $tipo_usuario === 'admin') {
    $sql = "SELECT t.*, 
                u.nome AS usuario_nome, 
                a.nome AS admin_nome
            FROM tarefas t
            LEFT JOIN usuarios u ON u.id = t.atribuido_para AND t.atribuido_para_tipo = 'funcionario'
            LEFT JOIN admins a  ON a.id = t.atribuido_para AND t.atribuido_para_tipo = 'admin'
            WHERE (
                (t.atribuido_para IN (SELECT id FROM usuarios WHERE admin_id = ?) AND t.atribuido_para_tipo = 'funcionario')
                OR (t.atribuido_para = ? AND t.atribuido_para_tipo = 'admin')
            )
            AND t.aprovada = 'Sim'
            $statusWhere
            ORDER BY $ordenacaoCustomizada";
    if ($statusFiltro === 'todos') {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $admin_id, $admin_id);
    } else {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $admin_id, $admin_id, $statusFiltro);
    }
    $stmt->execute();
    $resultado = $stmt->get_result();
} else {
    $sql = "SELECT t.*, 
                u.nome AS usuario_nome, 
                a.nome AS admin_nome
            FROM tarefas t
            LEFT JOIN usuarios u ON u.id = t.atribuido_para AND t.atribuido_para_tipo = 'funcionario'
            LEFT JOIN admins a  ON a.id = t.atribuido_para AND t.atribuido_para_tipo = 'admin'
            WHERE t.atribuido_para = ? 
              AND t.atribuido_para_tipo = ?
              AND t.aprovada = 'Sim'
              $statusWhere
            ORDER BY $ordenacaoCustomizada";
    if ($statusFiltro === 'todos') {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $filtro_id, $filtro_tipo);
    } else {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $filtro_id, $filtro_tipo, $statusFiltro);
    }
    $stmt->execute();
    $resultado = $stmt->get_result();
}





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
    <link rel="stylesheet" href="../css/estilo_titulo_paginas.css">
    <?php include '../componentes/bootstrap_refs.php'; ?>
</head>

<body>
    <?php include '../componentes/botao_menu.php'; ?>

    <div class="d-md-flex">
        <?php include '../componentes/menu.php'; ?>

        <main class="container py-4" style="max-width: 1200px;">
            <?php if (isset($_GET['cadastro']) && $_GET['cadastro'] === 'sucesso'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Tarefa criada com sucesso!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php endif; ?>

            <div class="sticky-top bg-white border-bottom" style="z-index: 1020;">
                <div class="container-fluid px-0 py-1">
                    <div class="d-flex justify-content-between align-items-center flex-column flex-md-row gap-3">
                        <div class="w-100 w-md-auto">
                            <h1 class="titulo-pagina">Tarefas</h1>
                        </div>
                        <div class="d-flex gap-2 w-50 w-md-auto align-items-center">
                            <select class="form-select" id="filtroResponsavel">
                                <?php if ($tipo_usuario === 'admin'): ?>
                                    <option value="todos" <?= ($filtro_usuario_id == 'todos') ? 'selected' : '' ?>>Todos</option>
                                <?php endif; ?>
                                <option value="<?= $tipo_usuario === 'admin' ? 'admin' : 'funcionario' ?>_<?= $usuario_id ?>" <?= ($filtro_usuario_id == ($tipo_usuario === 'admin' ? 'admin' : 'funcionario') . '_' . $usuario_id) ? 'selected' : '' ?>>Minhas tarefas</option>
                                <?php foreach ($usuariosPorFuncao as $funcao => $usuarios): ?>
                                    <optgroup label="<?= htmlspecialchars($funcao) ?>">
                                        <?php foreach ($usuarios as $u): ?>
                                            <?php if ($u['id'] == $_SESSION['usuario_id']) continue; ?>
                                            <option value="funcionario_<?= $u['id'] ?>" <?= ($filtro_usuario_id == 'funcionario_' . $u['id']) ? 'selected' : '' ?>><?= htmlspecialchars($u['nome']) ?></option>
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

            <div class="d-none d-md-flex justify-content-end mb-3 mt-3 align-items-center gap-2">

                <select class="form-select" id="filtroStatus" style="width: 160px;">
                    <option value="todos" <?= (($_GET['status'] ?? 'todos') === 'todos') ? 'selected' : '' ?>>Todos</option>
                    <option value="Pendente" <?= (($_GET['status'] ?? 'todos') === 'Pendente') ? 'selected' : '' ?>>Pendentes</option>
                    <option value="Em andamento" <?= (($_GET['status'] ?? 'todos') === 'Em andamento') ? 'selected' : '' ?>>Em andamento</option>
                    <option value="Concluída" <?= (($_GET['status'] ?? 'todos') === 'Concluída') ? 'selected' : '' ?>>Concluídas</option>
                </select>
                <button class="btn btn-outline-secondary  btn-visualizacao" onclick="mudarVisualizacao('grade', this)" title="Visualizar em grade">
                    <i class="bi bi-grid-3x3-gap-fill"></i>
                </button>
                <button class="btn btn-outline-secondary btn-visualizacao" onclick="mudarVisualizacao('lista', this)" title="Visualizar em lista">
                    <i class="bi bi-list-ul"></i>
                </button>
            </div>

            <div id="containerTarefas" class="row modo-grade" style="max-height: calc(100vh - 150px); overflow-y: auto; padding-right: 2px;">
                <?php while ($tarefa = $resultado->fetch_assoc()): ?>

                    <div class="tarefa-col">
                        <?php
                        if (!function_exists('removerAcentos')) {
                            function removerAcentos($texto)
                            {
                                return preg_replace(
                                    ['/[áàãâä]/u', '/[éèêë]/u', '/[íìîï]/u', '/[óòõôö]/u', '/[úùûü]/u', '/[ç]/u'],
                                    ['a', 'e', 'i', 'o', 'u', 'c'],
                                    strtolower($texto)
                                );
                            }
                        }
                        ?>

                        <div class="tarefa-lista card-tarefa <?= removerAcentos($tarefa['criticidade']) ?>" data-tarefa>


                            <!-- Criticidade -->
                            <span class="badge mb-2 <?= match ($tarefa['criticidade']) {
                                                        'Alta' => 'bg-danger',
                                                        'Média' => 'bg-warning text-dark',
                                                        'Baixa' => 'bg-success',
                                                        default => 'bg-secondary'
                                                    } ?>">
                                <?= $tarefa['criticidade'] ?>
                            </span>

                            <!-- Título -->
                            <h6 class="fw-bold"><?= htmlspecialchars($tarefa['descricao']) ?></h6>

                            <!-- Status e responsável -->
                            <div class="linha-badges ">
                                <?php
                                $statusClass = match ($tarefa['status']) {
                                    'Em andamento' => 'bg-primary-subtle text-primary',
                                    'Pendente'     => 'bg-danger-subtle text-danger',
                                    'Concluída'    => 'bg-success-subtle text-success',
                                    default        => 'bg-secondary-subtle text-secondary'
                                };
                                ?>
                            </div>

                            <!-- Ações + status + nome em uma única linha -->
                            <div class="acoes-bloco-container d-flex align-items-center ms-auto gap-4">
                                <span class="badge status-badge <?= $statusClass ?>"><?= $tarefa['status'] ?></span>
                                <span class="responsavel"><?= htmlspecialchars($tarefa['usuario_nome'] ?? $tarefa['admin_nome'] ?? 'Não encontrado') ?></span>

                                <div class="acoes-bloco d-flex gap-2">
                                    <?php if ($tarefa['status'] === 'Pendente'): ?>
                                        <button class="btn-acao btn-azul iniciar-tarefa" data-id="<?= $tarefa['id'] ?>" title="Iniciar">
                                            <i class="bi bi-play-fill"></i>
                                        </button>
                                    <?php elseif ($tarefa['status'] === 'Em andamento'): ?>
                                        <button class="btn-acao btn-verde concluir-tarefa" data-id="<?= $tarefa['id'] ?>" title="Concluir">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($tarefa['status'] !== 'Concluída'): ?>
                                        <button class="btn-acao btn-cinza editar-tarefa" data-id="<?= $tarefa['id'] ?>" title="Editar">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn-acao btn-vermelho excluir-tarefa" data-id="<?= $tarefa['id'] ?>" title="Excluir">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>


                        </div>
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
    $resultadoUsuariosForm = $stmt->get_result();
    $ultimoCargo = '';
    ?>

    <!-- Modal Nova Tarefa -->
    <div class="modal fade" id="modalAdicionarTarefa" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="../logica/controladores/adicionar_tarefa.php">

                <input type="hidden" name="tipo_usuario_logado" value="<?= $_SESSION['tipo_usuario'] ?>">

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
                            data-user-logado="<?= $_SESSION['usuario_id'] ?>"
                            data-tipo-usuario="<?= $_SESSION['tipo_usuario'] ?>">
                            <option value="">Selecione o responsável</option>
                            <option value="<?= $_SESSION['tipo_usuario'] ?>_<?= $_SESSION['usuario_id'] ?>">Eu mesmo</option>
                            <?php while ($usuario = mysqli_fetch_assoc($resultadoUsuariosForm)): ?>
                                <?php if ($usuario['id'] == $_SESSION['usuario_id']) continue; ?>
                                <?php if ($ultimoCargo !== $usuario['funcao']): ?>
                                    <?php
                                    if ($ultimoCargo !== '') echo '</optgroup>';
                                    $ultimoCargo = $usuario['funcao'];
                                    ?>
                                    <optgroup label="<?= htmlspecialchars($usuario['funcao']) ?>">
                                    <?php endif; ?>
                                    <option value="funcionario_<?= $usuario['id'] ?>"><?= htmlspecialchars($usuario['nome']) ?></option>
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
    // Consulta nova só para o formulário de editar tarefa
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $resultadoUsuariosEdit = $stmt->get_result();
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
                        <select class="form-select"
                            name="responsavel_id"
                            id="editar_responsavel_id"
                            required
                            data-user-logado="<?= $_SESSION['usuario_id'] ?>"
                            data-tipo-usuario="<?= $_SESSION['tipo_usuario'] ?>">
                            <option value="">Selecione o responsável</option>
                            <option value="<?= $_SESSION['tipo_usuario'] ?>_<?= $_SESSION['usuario_id'] ?>">Eu mesmo</option>
                            <?php while ($usuario = mysqli_fetch_assoc($resultadoUsuariosEdit)): ?>
                                <?php if ($usuario['id'] == $_SESSION['usuario_id']) continue; ?>
                                <?php if ($ultimoCargo !== $usuario['funcao']): ?>
                                    <?php
                                    if ($ultimoCargo !== '') echo '</optgroup>';
                                    $ultimoCargo = $usuario['funcao'];
                                    ?>
                                    <optgroup label="<?= htmlspecialchars($usuario['funcao']) ?>">
                                    <?php endif; ?>
                                    <option value="funcionario_<?= $usuario['id'] ?>"><?= htmlspecialchars($usuario['nome']) ?></option>
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
                        <strong>Atenção:</strong> Tarefas de alta criticidade exigem aprovação do gestor. Ao confirmar, será enviada uma solicitação para aprovação.
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