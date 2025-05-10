<?php
session_start();
require_once '../../conexao/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descricao = trim($_POST['titulo_tarefa']);
    $criticidade = $_POST['criticidade'];
    $responsavel_id = $_POST['responsavel_id'];
    $justificativa_funcionario = $_POST['comentario_gestor'] ?? null;

    $criado_por = $_SESSION['usuario_id'];
    $admin_id = $_SESSION['admin_id'];
    $tipo_usuario = $_SESSION['tipo_usuario'];

    // Regras para definir se tarefa requer aprovação
    $aprovada = 'Sim';

    if (
        $tipo_usuario === 'funcionario' &&
        $criticidade === 'Alta' &&
        $responsavel_id != $criado_por
    ) {
        $aprovada = 'Pendente';
    }

    $sql = "INSERT INTO tarefas (descricao, criticidade, status, criado_por, atribuido_para, equipe_id, aprovada, justificativa_funcionario)
            VALUES (?, ?, 'Pendente', ?, ?, NULL, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiiss", $descricao, $criticidade, $criado_por, $responsavel_id, $aprovada, $justificativa_funcionario);

    if ($stmt->execute()) {
        header('Location:../../telas/tarefas.php?cadastro=sucesso');
        exit;
    } else {
        echo "Erro ao criar tarefa: " . $stmt->error;
    }
}

/*function buscarEquipeId($conn, $usuario_id)
{
    $sql = "SELECT me.equipe_id FROM membros_equipes me
            INNER JOIN usuarios u ON u.id = me.usuario_id
            WHERE u.id = ? AND me.ativo = 1 LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $stmt->bind_result($equipe_id);
    $stmt->fetch();
    $stmt->close();

    return $equipe_id ?? null;
}*/
