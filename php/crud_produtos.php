<?php
header('Content-Type: application/json');

session_start();

include 'connection.php';

if (!checkLevelInSession(2)) {
    echo error_json(4, 'Permissão insuficiente para manipulação.');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    echo error_json(1, 'Método de requisição incorreto.');
    exit();
}

if (!isset($_POST['action'])) {
    echo error_json(2, 'Método de manipulação não fornecido.');
    exit();
}

switch ($_POST['action']) {
    case 'create':
        $missing_params = [];
        if (!isset($_POST['nome'])) array_push($missing_params, 'nome');
        if (!isset($_POST['preco'])) array_push($missing_params, 'preço');
        // if (!isset($_POST['descricao'])) array_push($missing_params, 'descrição');
        if (!isset($_POST['estoque'])) array_push($missing_params, 'estoque');

        if (count($missing_params) > 0) {
            echo error_json(5, 'Parâmetros insuficiente para criação.', "Parâmetros faltando: " . join(', ', $missing_params));
            exit();
        }

        $nome = $_POST['nome'];
        $preco = $_POST['preco'];
        $descricao = isset($_POST['descricao']) ? $_POST['descricao'] : null;
        $estoque = $_POST['estoque'];

        $empty_params = [];
        if ($nome === "") array_push($empty_params, 'nome');
        if ($preco === "") array_push($empty_params, 'preço');
        // if ($descricao === "") array_push($empty_params, 'descrição');
        if ($estoque === "") array_push($empty_params, 'estoque');

        if (count($empty_params) > 0) {
            echo error_json(6, 'Parâmetros vazios encontrados na criação.', 'Parâmetros vazios: ' . join(', ', $empty_params));
            exit();
        }

        $non_numeric_params = [];
        if (!is_numeric($preco)) array_push($non_numeric_params, 'preço');
        if (!is_numeric($estoque)) array_push($non_numeric_params, 'estoque');
        $estoque = (int) $estoque;

        if (count($non_numeric_params) > 0) {
            echo error_json(6, 'Parâmetros com valores não numéricos para criação.', 'Parâmetros ' . join(', ', $non_numeric_params) . ' deveriam ser numéricos');
            exit();
        }

        $query = "INSERT INTO produtos (nome, preco, descricao, estoque) VALUES (?, ?, ?, ?)";

        $statement = $connection->prepare($query);

        if ($descricao === null) {
            $statement->bind_param('sdii', $nome, $preco, $descricao, $estoque);
        } else {
            $statement->bind_param('sdsi', $nome, $preco, $descricao, $estoque);
        }
        
        $statement->execute();

        if ($statement->error) {            
            echo error_json(7, 'Erro na execução da criação.', $statement->error);
            exit();
        }
        
        echo crud_json('create', [
            'nome' => $nome,
            'preco' => (float) $preco,
            'descricao' => $descricao,
            'estoque' => (int) $estoque
        ], 'created');
        exit();
    
    case 'delete':
        if (!isset($_POST['id'])) {
            echo error_json(5, 'ID não fornecido para exclusão.');
            exit();
        }
        $id = $_POST['id'];

        if ($id === "") {
            echo error_json(6, 'ID fornecido está vazio.');
            exit();
        }
        if (!is_numeric($id)) {
            echo error_json(6, 'ID fornecido não é numérico.', 'ID fornecido: ' . $id);
            exit();
        }

        $id = (int) $id;

        $query = "DELETE FROM produtos WHERE id = ?";

        $statement = $connection->prepare($query);
        $statement->bind_param('i', $id);
        $statement->execute();

        if ($statement->error) {
            echo error_json(7, 'Erro na execução da exclusão.', $statement->error);
            exit();
        }

        echo crud_json('delete', $id, 'deleted');
        exit();
    
    case 'update':
        if (!isset($_POST['id'])) {
            echo error_json(5, 'ID não fornecido para edição.');
            exit();
        }

        $id = $_POST['id'];

        if ($id === "") {
            echo error_json(6, 'ID fornecido está vazio.');
            exit();
        }
        if (!is_numeric($id)) {
            echo error_json(6, 'ID fornecido não é numérico.', 'ID fornecido: ' . $id);
            exit();
        }

        $id = (int) $id;

        $editing_params = [];
        $empty_params = [];
        $non_numeric_params = [];

        $param_types = [];

        if (isset($_POST['nome'])) {
            $nome = $_POST['nome'];
            if ($nome === "") {
                array_push($empty_params, 'nome');
            } else {
                $editing_params['nome'] = $nome;
                array_push($param_types, 's');
            }
        }
        if (isset($_POST['preco'])) {
            $preco = $_POST['preco'];
            if ($preco === "") {
                array_push($empty_params, 'preço');
            } else if (!is_numeric($preco)) {
                array_push($non_numeric_params, 'preço');
            } else {
                $editing_params['preco'] = $preco;
                array_push($param_types, 'd');
            }
        }
        if (isset($_POST['descricao'])) {
            $descricao = $_POST['descricao'];
            $editing_params['descricao'] = $descricao;
            array_push($param_types, 's');
        }
        if (isset($_POST['estoque'])) {
            $estoque = $_POST['estoque'];
            if ($estoque === "") {
                array_push($empty_params, 'estoque');
            } else if (!is_numeric($estoque)) {
                array_push($non_numeric_params, 'estoque');
            } else {
                $estoque = (int) $estoque;
                $editing_params['estoque'] = $estoque;
                array_push($param_types, 'i');
            }
        }

        if (count($empty_params) > 0) {
            echo error_json(6, 'Parâmetros vazios encontrados para edição.', 'Parâmetros vazios: ' . join(', ', $empty_params));
            exit();
        }
        if (count($non_numeric_params) > 0 ) {
            echo error_json(6, 'Parâmetros não numéricos encontrados para edição.', 'Parâmetros não numéricos: ' . join(', ', $non_numeric_params));
            exit();
        }
        if (count($editing_params) === 0) {
            echo error_json(5, 'Parâmetros insuficientes para edição.');
            exit();
        }

        $setting = [];
        foreach ($editing_params as $key => $value) {
            array_push($setting, $key . ' = ?');
        }
        $setting = join(', ', $setting);
        $params = array_values($editing_params);
        array_push($params, $id);
        $param_types = join('', $param_types) . 'i';

        $query = "UPDATE produtos SET $setting WHERE id = ?";

        $statement = $connection->prepare($query);
        $statement->bind_param($param_types, ...$params);
        $statement->execute();

        if ($statement->error) {
            echo error_json(7, 'Erro na execução da edição', $statement->error);
            exit();
        }

        echo crud_json('update', [
            ...$editing_params, 'id' => $id
        ], 'edited');
        exit();

    default:
        echo error_json(3, 'Método de manipulação desconhecido.');
        exit();
}

# erros
# 1-> metodo de requisição nao post
# 2-> método de manipulação nao fornecido
# 3-> método de manipulação desconhecido
# 4-> falta de permissão
# 5-> parametros insuficientes
# 6-> parametros incorretos
# 7-> erro na execução da manipulação