<?php # GET
header('Content-Type: application/json');

include 'connection.php';
session_start();

if (isset($_GET['id']) || isset($_SESSION['id'])) {
    if (isset($_SESSION['id'])) {
        $id = $_SESSION['id'];
    }
    
    if (isset($_GET['id'])) {
        if (!checkLevelInSession(2)) {
            echo error_json(4, 'Permissão insuficiente.');
        } else {
            $id = $_GET['id'];
        }
    }

    if (!is_numeric($id)) {
        echo error_json(2, 'ID não numérico.');
        exit();
    }

    $query = "SELECT `id`, `nome`, `email`, `endereco`, `telefone`, `cep`, `nivel` FROM usuarios WHERE id = ? LIMIT 1";

    $statement = $connection->prepare($query);
    $statement->bind_param('i', $id);
    $statement->execute();

    if ($statement->error) {
        echo error_json(5, 'Erro na execução da consulta.', $statement->error);
        exit();
    }

    $result = $statement->get_result();

    if ($result->num_rows === 0) {
        echo error_json(3, 'Usuário não encontrado', 'ID procurado: ' . $id);
        exit();
    }

    echo profile_json($result);
} else {
    echo error_json(1, 'ID de usuário não fornecido.', 'Inicialize uma sessão de conta ou forneça um ID.');
    exit();
}

# códigos de erro
# 1-> id nao fornecido
# 2-> id nao numerico
# 3-> usuario nao encontrado
# 4-> permissao insuficiente
# 5-> erro sql