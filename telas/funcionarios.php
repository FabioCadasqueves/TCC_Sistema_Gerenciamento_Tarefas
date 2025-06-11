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

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funcionários - SIGTO</title>
    <link rel="stylesheet" href="../css/estilo_menu.css">
    <?php include '../componentes/bootstrap_refs.php'; ?>
    <link rel="stylesheet" href="../css/estilo_titulo_paginas.css">
    <link rel="stylesheet" href="../css/estilo_funcionario.css">
</head>

<body>
    <?php include '../componentes/botao_menu.php'; ?>

    <div class="d-md-flex">
        <?php include '../componentes/menu.php'; ?>

        <main class="container py-4" style="max-width: 1200px;">
            <?php if (
                isset($_GET['cadastro']) ||
                isset($_GET['edicao']) ||
                isset($_GET['exclusao']) ||
                isset($_GET['reativacao'])
            ): ?>
                <div id="alert-overlay" class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php if (isset($_GET['cadastro']) && $_GET['cadastro'] === 'sucesso'): ?>
                        Funcionário cadastrado com sucesso!
                    <?php elseif (isset($_GET['edicao']) && $_GET['edicao'] === 'sucesso'): ?>
                        Funcionário atualizado com sucesso!
                    <?php elseif (isset($_GET['exclusao']) && $_GET['exclusao'] === 'sucesso'): ?>
                        Funcionário excluído com sucesso!
                    <?php elseif (isset($_GET['reativacao']) && $_GET['reativacao'] === 'sucesso'): ?>
                        Funcionário reativado com sucesso!
                    <?php endif; ?>
                </div>
            <?php endif; ?>


            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="titulo-pagina">
                    Funcionários
                </h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNovoFuncionario">
                    + Novo Funcionário
                </button>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0">
                    <h5 class="card-title mb-0 fw-semibold" style="font-size: 1.1rem;">
                        <i class="bi bi-person-lines-fill me-2 text-secondary"></i>Lista de Funcionários
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Nome</th>
                                    <th class="d-none d-md-table-cell">Função</th>
                                    <th class="d-none d-md-table-cell">Equipe</th>
                                    <th>Status</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $admin_id = $_SESSION['admin_id'];
                                $sql = "SELECT u.id, u.nome, u.funcao, u.pin, u.ativo, MAX(e.nome) AS equipe
                                        FROM usuarios u
                                        LEFT JOIN membros_equipes me ON me.usuario_id = u.id
                                        LEFT JOIN equipes e ON e.id = me.equipe_id
                                        WHERE u.admin_id = ?
                                        GROUP BY u.id, u.nome, u.funcao, u.pin
                                        ORDER BY u.ativo DESC, u.nome ASC";


                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("i", $admin_id);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                while ($row = $result->fetch_assoc()):
                                    $avatarUrl = "https://api.dicebear.com/7.x/adventurer/svg?seed=" . urlencode($row['nome']);
                                ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex justify-content-center align-items-center rounded-circle bg-secondary text-white" style="width: 32px; height: 32px;">
                                                <i class="bi bi-person-fill"></i>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($row['nome']) ?></td>
                                        <td class="d-none d-md-table-cell"><?= htmlspecialchars($row['funcao']) ?></td>
                                        <td class="d-none d-md-table-cell"><?= htmlspecialchars($row['equipe'] ?? 'Nenhuma') ?></td>
                                        <td>
                                            <div>
                                                <?php
                                                $statusAtivo = $row['ativo'] ?? 0; // será 1 (ativo) ou 0 (inativo)
                                                ?>
                                                <span class="badge px-2 py-1 d-inline-flex align-items-center gap-1 <?= $statusAtivo ? 'bg-success' : 'bg-secondary' ?>" style="font-size: 0.85rem; min-width: 70px; justify-content: center;">
                                                    <i class="bi <?= $statusAtivo ? 'bi-check-circle-fill' : 'bi-x-circle-fill' ?>" style="font-size: 0.85rem;"></i>
                                                    <?= $statusAtivo ? 'Ativo' : 'Inativo' ?>
                                                </span>
                                            </div>

                                        </td>
                                        <td class="text-end">
                                            <?php if ($row['ativo']): ?>
                                                <!-- Botões para funcionário ativo -->
                                                <button class="btn btn-sm btn-light border-warning text-warning rounded-circle btn-editar-funcionario"
                                                    title="Editar"
                                                    data-id="<?= $row['id'] ?>"
                                                    data-nome="<?= htmlspecialchars($row['nome']) ?>"
                                                    data-funcao="<?= $row['funcao'] ?>"
                                                    data-equipe="<?= $row['equipe'] ?>"
                                                    data-pin="<?= $row['pin'] ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalEditarFuncionario">
                                                    <i class="bi bi-pencil-fill"></i>
                                                </button>
                                                <button class="btn btn-sm btn-light border-danger text-danger rounded-circle btn-excluir-funcionario"
                                                    title="Excluir"
                                                    data-id="<?= $row['id'] ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalConfirmarExclusao">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            <?php else: ?>
                                                <!-- Botão para funcionário inativo -->
                                                <form method="post" action="../logica/controladores/reativar_funcionario.php" style="display: inline;">
                                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3" title="Reativar funcionário">
                                                        <i class="bi bi-arrow-clockwise me-1"></i>Reativar
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>

                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php include '../componentes/modais_funcionarios.php'; ?>
    <script src="../js/js_menu.js"></script>
    <script src="../js/js_funcionarios.js"></script>
</body>

</html>