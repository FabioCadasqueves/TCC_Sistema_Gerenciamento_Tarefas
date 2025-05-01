<?php
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "sistema_tarefas";

// Criar conexão
$conn = new mysqli($servidor, $usuario, $senha, $banco);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
?>