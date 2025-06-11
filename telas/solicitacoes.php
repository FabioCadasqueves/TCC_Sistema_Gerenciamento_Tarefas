<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: login.php');
    exit;
}
$paginaAtual = 'solicitacoes';
?>

<style>
    .list-group-item {
        transition: box-shadow 0.2s ease;
    }

    .list-group-item:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
    }
</style>


<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitações - SIGTO</title>
    <link rel="stylesheet" href="../css/estilo_menu.css">
    <?php include '../componentes/bootstrap_refs.php'; ?>
    <link rel="stylesheet" href="../css/estilo_funcionario.css">
    <link rel="stylesheet" href="../css/estilo_titulo_paginas.css">
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

        <main class="container py-4" style="max-width: 1200px;">
            <div class="d-flex justify-content-between align-items-center mb-4" style="padding-bottom: 15px">
                <h1 class="titulo-pagina">Solicitações de Tarefas Críticas</h1>
            </div>

            <div style="max-height: 100vh; overflow-y: auto;" class="custom-scrollbar">
                <ul class="list-group">
                    <?php
                    require_once '../conexao/conexao.php';

                    $admin_id = $_SESSION['admin_id'];

                    $sql = "SELECT t.id, t.descricao, t.criticidade, t.justificativa_funcionario,
                                t.atribuido_para,
                                u.nome AS solicitante_nome, u.funcao AS solicitante_funcao,
                                r.nome AS responsavel_nome, r.funcao AS responsavel_funcao
                            FROM tarefas t
                            INNER JOIN usuarios u ON u.id = t.criado_por
                            INNER JOIN usuarios r ON r.id = t.atribuido_para
                            WHERE u.admin_id = ? AND t.criticidade = 'Alta' 
                            AND (t.aprovada IS NULL OR t.aprovada = 'Pendente')
                            ORDER BY t.criado_em DESC
                            ";


                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $admin_id);
                    $stmt->execute();
                    $resultado = $stmt->get_result();
                    ?>

                    <ul class="list-group">
                        <?php while ($tarefa = $resultado->fetch_assoc()): ?>
                            <li class="list-group-item bg-white rounded-3 border shadow-sm mb-1 px-4 py-3">
                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-semibold text-primary">
                                            <i class="bi bi-exclamation-triangle-fill me-1 text-danger"></i>
                                            Solicitação - <?= htmlspecialchars($tarefa['descricao']) ?>
                                        </h6>
                                        <div class="small text-muted">
                                            <div><i class="bi bi-person-circle me-1"></i>Solicitante: <?= htmlspecialchars($tarefa['solicitante_nome']) ?> - <?= $tarefa['solicitante_funcao'] ?></div>
                                            <div><i class="bi bi-box-arrow-in-right me-1"></i>Sugerido para: <?= htmlspecialchars($tarefa['responsavel_nome']) ?> - <?= $tarefa['responsavel_funcao'] ?></div>
                                        </div>
                                    </div>
                                    <div class="mt-2 mt-md-0">
                                        <button class="btn btn-sm btn-primary avaliar-btn"
                                            data-id="<?= $tarefa['id'] ?>"
                                            data-descricao="<?= htmlspecialchars($tarefa['descricao'], ENT_QUOTES, 'UTF-8') ?>"
                                            data-criticidade="<?= htmlspecialchars($tarefa['criticidade'], ENT_QUOTES, 'UTF-8') ?>"
                                            data-responsavel-id="<?= htmlspecialchars($tarefa['atribuido_para'], ENT_QUOTES, 'UTF-8') ?>"
                                            data-solicitante="<?= htmlspecialchars($tarefa['solicitante_nome'], ENT_QUOTES, 'UTF-8') ?>"
                                            data-justificativa="<?= htmlspecialchars($tarefa['justificativa_funcionario'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                            <i class="bi bi-search me-1"></i> Avaliar
                                        </button>

                                    </div>
                                </div>
                            </li>

                        <?php endwhile; ?>
                    </ul>

                </ul>
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

    <!-- MODAL AVALIAR SOLICITACAO -->

    <div class="modal fade" id="modalAvaliarSolicitacao" tabindex="-1" aria-labelledby="modalAvaliarSolicitacaoLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content" method="POST" action="../logica/controladores/aprovar_tarefa.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAvaliarSolicitacaoLabel">Avaliar Solicitação</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <div class="modal-body">
                    <!-- Campo oculto para ID da tarefa -->
                    <input type="hidden" name="id_tarefa" id="idTarefaAvaliacao">

                    <div class="mb-3">
                        <label class="form-label">Solicitante</label>
                        <input type="text" class="form-control" id="nomeSolicitante" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="tituloTarefa" class="form-label">Título da Tarefa</label>
                        <input type="text" class="form-control" id="tituloTarefa" name="titulo" required>
                    </div>

                    <div class="mb-3">
                        <label for="criticidade" class="form-label">Criticidade</label>
                        <select class="form-select" id="criticidade" name="criticidade" required>
                            <option value="Baixa">Baixa</option>
                            <option value="Média">Média</option>
                            <option value="Alta">Alta</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="responsavel" class="form-label">Atribuir para</label>
                        <select class="form-select"
                            id="responsavel_id"
                            name="responsavel_id"
                            required
                            data-user-logado="<?= $usuario_logado_id ?>"
                            data-tipo-usuario="<?= $_SESSION['tipo_usuario'] ?>">
                            <option value="">Selecione o responsável</option>
                            <!--<option value="<?= $usuario_logado_id ?>">Eu mesmo</option>-->
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
                        <label class="form-label">Justificativa do Funcionário</label>
                        <textarea class="form-control" id="justificativaFuncionario" name="justificativa_funcionario" readonly rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="justificativaGestor" class="form-label">Comentário do Gestor (opcional)</label>
                        <textarea class="form-control" name="justificativa_gestor" id="justificativaGestor" rows="2" placeholder="Deixe um comentário ou justificativa."></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Aprovar Tarefa</button>
                </div>
            </form>
        </div>
    </div>


    <script src="../js/js_menu.js"></script>
    <script src="../js/js_solicitacoes.js"></script>
</body>

</html>