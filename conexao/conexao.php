<?php
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "sigto_db";

// Criar conexão
$conn = new mysqli($servidor, $usuario, $senha, $banco);

// Verificar conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
