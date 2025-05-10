<?php
session_start();
require_once '../../conexao/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../telas/tarefas.php?erro=metodo');
    exit;
}

$tarefa_id = $_POST['tarefa_id'] ?? null;
$titulo = trim($_POST['titulo_tarefa'] ?? '');
$responsavel_id = $_POST['responsavel_id'] ?? null;
$criticidade = $_POST['criticidade'] ?? '';

$valido = $tarefa_id && $titulo && $responsavel_id && in_array($criticidade, ['Baixa', 'Média', 'Alta']);

if (!$valido) {
    header('Location: ../../telas/tarefas.php?erro=dados_invalidos');
    exit;
}

$sql = "UPDATE tarefas SET descricao = ?, atribuido_para = ?, criticidade = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sisi", $titulo, $responsavel_id, $criticidade, $tarefa_id);

if ($stmt->execute()) {
    header('Location: ../../telas/tarefas.php?edicao=sucesso');
} else {
    header('Location: ../../telas/tarefas.php?erro=edicao_falhou');
}
exit;
