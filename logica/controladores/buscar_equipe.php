<?php
require_once '../../conexao/conexao.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['erro' => 'ID não fornecido']);
    exit;
}

$equipe_id = intval($_GET['id']);

// Buscar nome da equipe
$sqlEquipe = "SELECT nome FROM equipes WHERE id = ?";
$stmtEquipe = $conn->prepare($sqlEquipe);
$stmtEquipe->bind_param("i", $equipe_id);
$stmtEquipe->execute();
$resultEquipe = $stmtEquipe->get_result();
$equipe = $resultEquipe->fetch_assoc();

// Buscar membros ativos
$sqlMembros = "SELECT usuario_id FROM membros_equipes WHERE equipe_id = ? AND ativo = 1";
$stmtMembros = $conn->prepare($sqlMembros);
$stmtMembros->bind_param("i", $equipe_id);
$stmtMembros->execute();
$resultMembros = $stmtMembros->get_result();

$membros = [];
while ($row = $resultMembros->fetch_assoc()) {
    $membros[] = $row['usuario_id'];
}

// Retornar como JSON
echo json_encode([
    'nome' => $equipe['nome'] ?? '',
    'membros' => $membros
]);
