<?php
session_start();
require_once '../../conexao/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descricao = trim($_POST['titulo_tarefa']);
    $criticidade = $_POST['criticidade'];
    $responsavel_id = $_POST['responsavel_id'];
    $justificativa_funcionario = $_POST['comentario_gestor'] ?? null;

    // Recebe tipo e ID juntos, separados por "_"
    list($atribuido_para_tipo, $atribuido_para) = explode('_', $_POST['responsavel_id']);
    $criado_por = $_SESSION['usuario_id'];
    $criado_por_tipo = $_SESSION['tipo_usuario'];

    // ----------- VALIDAÇÃO DE CRITICIDADE ALTA -----------
    // Por padrão, aprovada
    $aprovada = 'Sim';

    // Se for alta criticidade
    if ($criticidade === 'Alta') {
        // Se funcionário e tentando atribuir para outro, então deve ser "Pendente"
        if (
            $criado_por_tipo === 'funcionario' &&
            $atribuido_para != $criado_por
        ) {
            $aprovada = 'Pendente';
            // Aqui pode adicionar lógica de notificação, registro, etc, se quiser.
        }
    }
    // ----------- FIM DA VALIDAÇÃO -----------

    $sql = "INSERT INTO tarefas (
                descricao, criticidade, status, criado_por, criado_por_tipo,
                atribuido_para, atribuido_para_tipo, equipe_id, aprovada, justificativa_funcionario
            ) VALUES (?, ?, 'Pendente', ?, ?, ?, ?, NULL, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssisssss",
        $descricao,                // s
        $criticidade,              // s
        $criado_por,               // i
        $criado_por_tipo,          // s
        $atribuido_para,           // i
        $atribuido_para_tipo,      // s
        $aprovada,                 // s
        $justificativa_funcionario // s
    );

    if ($stmt->execute()) {
        header('Location:../../telas/tarefas.php?cadastro=sucesso');
        exit;
    } else {
        echo "Erro ao criar tarefa: " . $stmt->error;
    }
}
