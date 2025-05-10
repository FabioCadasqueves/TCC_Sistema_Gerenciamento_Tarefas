<?php
require_once '../../conexao/conexao.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['erro' => 'ID não fornecido']);
    exit;
}

$sql = "SELECT id, descricao, atribuido_para, criticidade FROM tarefas WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $tarefa = $result->fetch_assoc();
    echo json_encode($tarefa);
} else {
    echo json_encode(['erro' => 'Tarefa não encontrada']);
}
