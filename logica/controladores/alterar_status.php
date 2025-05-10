<?php
session_start();
require_once '../../conexao/conexao.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['erro' => 'Método não permitido']);
    exit;
}

$id_tarefa = $_POST['id_tarefa'] ?? null;
$novo_status = $_POST['novo_status'] ?? null;
$usuario_id = $_SESSION['usuario_id'] ?? null;

$validos = ['Pendente', 'Em andamento', 'Concluída'];

if (!$id_tarefa || !$novo_status || !in_array($novo_status, $validos)) {
    http_response_code(400);
    echo json_encode(['erro' => 'Dados inválidos']);
    exit;
}

$sql = "UPDATE tarefas SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $novo_status, $id_tarefa);
$sucesso = $stmt->execute();

if ($sucesso) {
    echo json_encode(['sucesso' => true]);
} else {
    http_response_code(500);
    echo json_encode(['erro' => 'Falha ao atualizar status']);
}
