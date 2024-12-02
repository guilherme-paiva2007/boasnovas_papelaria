<?php  # POST
header('Content-Type: application/json');

include 'connection.php';
session_start();

#precisa puxar todas as informações do usuário para a sessão, exceto, obvio, a senha

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    echo error_json(1, 'Método de requisição incorreto.', 'Método utilizado: ' . $_SERVER['REQUEST_METHOD']);
    exit();
}

$missing_params = [];
if (!isset($_POST['email'])) array_push($missing_params, 'email');
if (!isset($_POST['senha'])) array_push($missing_params, 'senha');
if (count($missing_params) > 0) {
    echo error_json(2, 'Parâmetros insuficiente encontrados.', 'Parâmetros faltando: ' . join(', ', $missing_params));
    exit();
}

$email = $_POST['email'];
$senha = $_POST['senha'];

$empty_params = [];
if ($email === "") array_push($empty_params, 'email');
if ($senha === "") array_push($empty_params, 'senha');
if (count($empty_params) > 0) {
    echo error_json(3, 'Parâmetros vazios encontrados.', 'Parâmetros vazios: ' . join(', ', $empty_params));
    exit();
}

$invalid_params = [];
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) array_push($invalid_params, 'email');
if (count($invalid_params) > 0) {
    echo error_json(3, 'Parâmetros incorretos encontrados.', 'Parâmetros incorretos: ' . join(', ', $invalid_params));
    exit();
}

$invalid_length_params = [];
if (strlen($senha) < 8 || strlen($senha) > 64) array_push($invalid_length_params, 'senha');
if (count($invalid_length_params) > 0) {
    echo error_json(3, 'Parâmetros com tamanho incorreto encontrados.', 'Parâmetros incorretos: ' . join(', ', $invalid_length_params));
    exit();
}

$query = "SELECT * FROM usuarios WHERE email = ? LIMIT 1";

$statement = $connection->prepare($query);
$statement->bind_param('s', $email);
$statement->execute();

if ($statement->error) {
    echo error_json(6, 'Erro na execução da consulta', $statement->error);
    exit();
}

$result = $statement->get_result();

if ($result->num_rows === 0) {
    echo error_json(4, 'Usuário não encontrado.', 'Email procurado: ' . $email);
    exit();
}

$usuario = $result->fetch_assoc();

if (password_verify($senha, $usuario['senha'])) {
    $_SESSION['id'] = $usuario['id'];
    $_SESSION['nome'] = $usuario['nome'];
    $_SESSION['email'] = $usuario['email'];
    if (isset($usuario['endereco'])) $_SESSION['endereco'] = $usuario['endereco'];
    if (isset($usuario['telefone'])) $_SESSION['telefone'] = $usuario['telefone'];
    if (isset($usuario['cep'])) $_SESSION['cep'] = $usuario['cep'];
    $_SESSION['nivel'] = $usuario['nivel'];
    unset($usuario['senha']);
    echo login_json($usuario);
    exit();
} else {
    echo error_json(5, 'Senha incorreta.');
    exit();
}

# 1-> metodo de requisição nao post
# 2-> parametros insuficientes
# 3-> parametros incorretos
# 4-> usuario nao encontrado
# 5-> senha incorreta
# 6-> erro na execução da manipulação