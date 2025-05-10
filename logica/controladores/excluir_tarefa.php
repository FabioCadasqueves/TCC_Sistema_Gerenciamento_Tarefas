<?php
session_start();
require_once '../../conexao/conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_tarefa'])) {
    $id = intval($_POST['id_tarefa']);

    $stmt = $conn->prepare("DELETE FROM tarefas WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["sucesso" => true]);
    } else {
        echo json_encode(["sucesso" => false, "erro" => "Erro ao excluir."]);
    }

    exit;
}
echo json_encode(["sucesso" => false, "erro" => "Requisição inválida."]);
