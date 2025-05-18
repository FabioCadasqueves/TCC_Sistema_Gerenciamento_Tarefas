<?php
session_start();
require_once '../../conexao/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../telas/tarefas.php?erro=metodo');
    exit;
}

$tarefa_id = $_POST['tarefa_id'] ?? null;
$titulo = trim($_POST['titulo_tarefa'] ?? '');
$criticidade = $_POST['criticidade'] ?? '';
$responsavel_id = $_POST['responsavel_id'] ?? null;

// Quebra tipo e id
list($atribuido_para_tipo, $atribuido_para) = explode('_', $responsavel_id);

$valido = $tarefa_id && $titulo && $responsavel_id && in_array($criticidade, ['Baixa', 'Média', 'Alta']);

if (!$valido) {
    header('Location: ../../telas/tarefas.php?erro=dados_invalidos');
    exit;
}

// Se quiser garantir que o tipo está correto (opcional, mas recomendado)
if (!in_array($atribuido_para_tipo, ['admin', 'funcionario'])) {
    header('Location: ../../telas/tarefas.php?erro=tipo_invalido');
    exit;
}

$sql = "UPDATE tarefas SET descricao = ?, atribuido_para = ?, atribuido_para_tipo = ?, criticidade = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sissi", $titulo, $atribuido_para, $atribuido_para_tipo, $criticidade, $tarefa_id);

if ($stmt->execute()) {
    header('Location: ../../telas/tarefas.php?edicao=sucesso');
} else {
    header('Location: ../../telas/tarefas.php?erro=edicao_falhou');
}
exit;
