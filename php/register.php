<?php # POST !
header('Content-Type: application/json');

include 'connection.php';
session_start();

if (isset($_SESSION['nivel'])) {
    if (levelToNumber($_SESSION['nivel']) === 1) {
        echo error_json(5, 'Permissões insuficientes para registro de usuário.', 'Usuários com sessão iniciada não podem criar novas contas.');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    echo error_json(1, 'Método de requisição incorreto.');
    exit();
}

$missing_params = [];
if (!isset($_POST['nome'])) array_push($missing_params, 'nome');
if (!isset($_POST['email'])) array_push($missing_params, 'email');
if (!isset($_POST['senha'])) array_push($missing_params, 'senha');
if (count($missing_params) > 0) {
    echo error_json(2, 'Parâmetros insuficiente encontrados.', 'Parâmetros faltando: ' . join(', ', $missing_params));
    exit();
}

$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = $_POST['senha'];

$empty_params = [];
if ($nome === "") array_push($empty_params, 'nome');
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

$verf_query = "SELECT * FROM usuarios WHERE email = ? LIMIT 1";

$verf_statement = $connection->prepare($verf_query);
$verf_statement->bind_param('s', $email);
$verf_statement->execute();

if ($verf_statement->error) {
    echo error_json(6, 'Erro na execução da consulta.', $verf_statement->error);
    exit();
}

$verf_result = $verf_statement->get_result();

if ($verf_result->num_rows > 0) {
    echo error_json(4, 'Usuário já existente.', 'Email procurado: ' . $email);
    exit();
}

$senha = password_hash($senha, PASSWORD_DEFAULT);

$query = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";

$statement = $connection->prepare($query);
$statement->bind_param('sss', $nome, $email, $senha);
$statement->execute();

if ($statement->error) {
    echo error_json(6, 'Erro na execução da manipulação.', $statement->error);
    exit();
}

$query = "SELECT * FROM usuarios WHERE email = ? LIMIT 1";

$statement = $connection->prepare($query);
$statement->bind_param('s', $email);
$statement->execute();

if ($statement->error) {
    echo error_json(6, 'Erro na execução da consulta.', $statement->error);
    exit();
}

$result = $statement->get_result();

$usuario = $result->fetch_assoc();
unset($usuario['senha']);

$_SESSION['id'] = $usuario['id'];
$_SESSION['nome'] = $usuario['nome'];
$_SESSION['email'] = $usuario['email'];
if (isset($usuario['endereco'])) $_SESSION['endereco'] = $usuario['endereco'];
if (isset($usuario['telefone'])) $_SESSION['telefone'] = $usuario['telefone'];
if (isset($usuario['cep'])) $_SESSION['cep'] = $usuario['cep'];
$_SESSION['nivel'] = $usuario['nivel'];

echo register_json($usuario);
exit();

# 1-> metodo de requisição nao post
# 2-> parametros insuficientes
# 3-> parametros incorretos
# 4-> usuario existente
# 5-> permissoes insuficientes
# 6-> erro na execução da manipulação