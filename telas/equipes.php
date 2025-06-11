<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../conexao/conexao.php';

// Bloqueia acesso se não for admin
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Buscar usuários do banco
$usuarios = [];
$admin_id = $_SESSION['admin_id'];

$sql = "SELECT u.id, u.nome, u.funcao,
               me.equipe_id AS equipe_atual
        FROM usuarios u
        LEFT JOIN (
            SELECT usuario_id, equipe_id
            FROM membros_equipes
            WHERE ativo = 1
        ) me ON me.usuario_id = u.id
        WHERE u.ativo = 1 AND u.admin_id = ?
        ORDER BY u.funcao, u.nome";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$resultado = $stmt->get_result();

$usuarios = [];
while ($linha = $resultado->fetch_assoc()) {
    $usuarios[] = $linha;
}

// Separar por cargo
$grupos = [
    'Operador' => [],
    'Mecânico' => []
];

foreach ($usuarios as $usuario) {
    if (isset($grupos[$usuario['funcao']])) {
        $grupos[$usuario['funcao']][] = $usuario;
    }
}

$paginaAtual = 'equipes';
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipes - SIGTO</title>
    <link rel="stylesheet" href="../css/estilo_menu.css">
    <?php include '../componentes/bootstrap_refs.php'; ?>
    <link rel="stylesheet" href="../css/estilo_fixar_header.css">
    <link rel="stylesheet" href="../css/estilo_titulo_paginas.css">
</head>

<body>

    <?php include '../componentes/botao_menu.php'; ?>

    <div class="d-md-flex">
        <?php include '../componentes/menu.php'; ?>

        <main class="container py-4" style="max-width: 1200px;">
            <?php if (isset($_GET['cadastro']) && $_GET['cadastro'] === 'sucesso'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    Equipe criada com sucesso!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
                </div>
            <?php endif; ?>
            <div class="d-flex justify-content-between align-items-center mb-4" style="padding-bottom: 15px">
                <h1 class="titulo-pagina">Equipes</h1>
                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNovaEquipe">+ Nova Equipe</a>
            </div>

            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" style="max-height: calc(100vh - 150px); overflow-y: auto; padding-right: 8px;">
                <?php
                $admin_id = $_SESSION['admin_id'];

                $sql = "SELECT e.id, e.nome, e.criado_em, COUNT(em.usuario_id) AS total_membros
                        FROM equipes e
                        LEFT JOIN membros_equipes em ON em.equipe_id = e.id AND em.ativo = 1
                        WHERE e.admin_id = ? AND e.ativo = 1
                        GROUP BY e.id, e.nome, e.criado_em
                        ORDER BY e.nome";

                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $admin_id);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($equipe = $result->fetch_assoc()):
                ?>
                    <div class="col">
                        <div class="card border-0 shadow-sm h-100" style="background: #f8f9fc; border-left: 5px solid #0d6efd; transition: all 0.2s ease-in-out;">
                            <div class="card-body py-3">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="bi bi-people-fill fs-4"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-0"><?= htmlspecialchars($equipe['nome']) ?></h5>
                                        <small class="text-muted"><?= intval($equipe['total_membros']) ?> membro<?= intval($equipe['total_membros']) !== 1 ? 's' : '' ?></small>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="#" class="btn btn-outline-secondary btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalVerEquipe"
                                        data-id="<?= $equipe['id'] ?>"
                                        title="Visualizar equipe">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="#" class="btn btn-outline-warning btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditarEquipe"
                                        data-id="<?= $equipe['id'] ?>"
                                        title="Editar equipe">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="#" class="btn btn-outline-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalExcluirEquipe"
                                        data-id="<?= $equipe['id'] ?>"
                                        data-nome="<?= htmlspecialchars($equipe['nome']) ?>"
                                        title="Excluir equipe">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>




                <?php endwhile; ?>
            </div>

        </main>
    </div>

    <div class="modal fade" id="modalNovaEquipe" tabindex="-1" aria-labelledby="modalNovaEquipeLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form class="modal-content" action="../logica/controladores/cadastrar_equipe.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNovaEquipeLabel">Criar Nova Equipe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nomeEquipe" class="form-label">Nome da Equipe</label>
                        <input type="text" class="form-control" id="nomeEquipe" name="nomeEquipe" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Selecione os Membros</label>

                        <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            <?php
                            $nomes_plurais = [
                                'Operador' => 'Operadores',
                                'Mecânico' => 'Mecânicos'
                            ];
                            ?>
                            <?php foreach ($grupos as $cargo => $lista): ?>
                                <strong class="d-block mb-2 mt-2"><?= $nomes_plurais[$cargo] ?? $cargo ?></strong>
                                <?php foreach ($lista as $usuario): ?>
                                    <?php
                                    $usuario_id = $usuario['id'];
                                    $usuario_nome = htmlspecialchars($usuario['nome']);
                                    $usuario_equipe = $usuario['equipe_atual'] ?? null;
                                    $desabilitado = !empty($usuario_equipe);
                                    ?>
                                    <div class="form-check">
                                        <input class="form-check-input"
                                            type="checkbox"
                                            name="membros[]"
                                            value="<?= $usuario_id ?>"
                                            id="usuario<?= $usuario_id ?>"
                                            <?= $desabilitado ? 'disabled' : '' ?>>
                                        <label class="form-check-label" for="usuario<?= $usuario_id ?>">
                                            <?= $usuario_nome ?>
                                            <?= $desabilitado ? '<span class="text-muted">(já em uma equipe)</span>' : '' ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endforeach; ?>

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
            <form class="modal-content" action="../logica/controladores/editar_equipe.php" method="POST">
                <input type="hidden" name="id_equipe" id="editarIdEquipe">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditarEquipeLabel">Editar Equipe</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editarNomeEquipe" class="form-label">Nome da Equipe</label>
                        <input type="text" class="form-control" id="editarNomeEquipe" name="nome" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Editar Membros da Equipe</label>
                        <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            <?php
                            $nomes_plurais = [
                                'Operador' => 'Operadores',
                                'Mecânico' => 'Mecânicos'
                            ];
                            ?>
                            <?php foreach ($grupos as $cargo => $lista): ?>
                                <strong class="d-block mb-2 mt-2"><?= $nomes_plurais[$cargo] ?? $cargo ?></strong>
                                <?php foreach ($lista as $usuario): ?>
                                    <?php
                                    $usuario_id = $usuario['id'];
                                    $usuario_nome = htmlspecialchars($usuario['nome']);
                                    $usuario_equipe = $usuario['equipe_atual'] ?? '';
                                    ?>
                                    <div class="form-check">
                                        <input class="form-check-input"
                                            type="checkbox"
                                            name="membros[]"
                                            value="<?= $usuario_id ?>"
                                            id="editarUsuario<?= $usuario_id ?>"
                                            data-equipe="<?= $usuario_equipe ?>">
                                        <label class="form-check-label" for="editarUsuario<?= $usuario_id ?>">
                                            <?= $usuario_nome ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endforeach; ?>

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
                <form action="../logica/controladores/excluir_equipe.php" method="POST">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="modalExcluirEquipeLabel">Confirmar Exclusão</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="excluirIdEquipe">
                        <p>Tem certeza que deseja excluir a equipe <strong id="excluirNomeEquipe">NOME</strong>?</p>
                        <p class="text-danger small">Essa ação não poderá ser desfeita.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script src="../js/js_menu.js"></script>
    <script src="../js/js_equipes.js"></script>
</body>

</html>