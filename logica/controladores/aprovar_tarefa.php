<?php
require_once '../../conexao/conexao.php';
session_start();

// Garantia básica
if (!isset($_POST['id_tarefa'])) {
    die("ID da tarefa não informado.");
}

// Coleta segura dos dados
$id_tarefa = intval($_POST['id_tarefa']);
$titulo = trim($_POST['titulo']);
$criticidade = trim($_POST['criticidade']);
$responsavel_id = intval($_POST['responsavel_id']);
$justificativa_gestor = trim($_POST['justificativa_gestor'] ?? null);

// Atualiza a tarefa com os dados aprovados
$sql = "UPDATE tarefas SET 
            descricao = ?, 
            criticidade = ?, 
            atribuido_para = ?, 
            justificativa_gestor = ?, 
            aprovada = 'Sim'
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssisi", $titulo, $criticidade, $responsavel_id, $justificativa_gestor, $id_tarefa);

if ($stmt->execute()) {
    header("Location: ../../telas/solicitacoes.php?avaliacao=sucesso");
    exit;
} else {
    echo "Erro ao aprovar a tarefa: " . $stmt->error;
}
