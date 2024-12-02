<?php #GET
header('Content-Type: application/json');

#acoes
# acessar lista (automatico)
# criar carrinho (automatico, se não houver carrinho)
# adicionar item
# remover item
# limpar carrinho

include 'connection.php';
session_start();

if (isset($_GET['id']) || isset($_SESSION['id'])) {
    if (isset($_SESSION['id'])) {
        $id = $_SESSION['id'];
    }

    if (isset($_GET['id'])) {
        if (checkLevelInSession(2)) {
            $id = $_GET['id'];
        } else {
            echo error_json(4, 'Permissão insuficiente para manipulação.');
            exit();
        }
    }

    if (!is_numeric($id)) {
        echo error_json(3, 'ID não numérico.', 'ID procurado: ' . $id);
        exit();
    }

    $id = (int) $id;

    $query = "SELECT * FROM carrinhos WHERE id_usuario = ? LIMIT 1";

    $statement = $connection->prepare($query);
    $statement->bind_param('i', $id);
    $statement->execute();

    if ($statement->error) {
        echo error_json(5, 'Erro na execução da requisição.', $statement->error);
        exit();
    }

    $result = $statement->get_result();

    if ($result->num_rows === 0) {
        $query = "INSERT INTO carrinhos (id_usuario) VALUES (?)";

        $statement = $connection->prepare($query);
        $statement->bind_param('i', $id);
        $statement->execute();

        if ($statement->error) {
            echo error_json(5, 'Erro na execução da requisição.', $statement->error);
            exit();
        }

        $query = "SELECT * FROM carrinhos WHERE id_usuario = ? LIMIT 1";

        $statement = $connection->prepare($query);
        $statement->bind_param('i', $id);
        $statement->execute();

        if ($statement->error) {
            echo error_json(5, 'Erro na execução da requisição.', $statement->error);
            exit();
        }

        $result = $statement->get_result();
    }

    $cart = $result->fetch_assoc();

    if (!isset($_GET['action'])) {
        $action = 'read';
    } else {
        $action = $_GET['action'];
    }

    $items = new itemsList($cart['id'], 'carrinho', $connection);

    switch ($action) {
        case 'read':
            echo cart_json($cart, $items->get_items_json());
            exit();
        case 'add':
            if (!isset($_GET['produto'])) {
                echo error_json(1, 'Produto não fornecido.');
                exit();
            }
            if (!isset($_GET['qnt'])) {
                echo error_json(1, 'Quantidade não fornecida.');
                exit();
            }

            $produto = $_GET['produto'];
            $qnt = $_GET['qnt'];

            if (!is_numeric($produto)) {
                echo error_json(3, 'Produto não numérico.', 'Produto procurado: ' . $produto);
                exit();
            }
            if (!is_numeric($qnt)) {
                echo error_json(3, 'Quantidade não numérica.', 'Quantidade procurada: ' . $qnt);
                exit();
            }

            $produto = (int) $produto;
            $qnt = (int) $qnt;

            echo $items->add_item($produto, $qnt);
            exit();
        case 'remove':
            if (!isset($_GET['item'])) {
                echo error_json(1, 'ID de item não fornecido para exclusão.');
                exit();
            }

            $item = $_GET['item'];

            if (!is_numeric($item)) {
                echo error_json(3, 'ID de item para exclusãonão numérico.', 'Item procurado: ' . $item);
                exit();
            }

            $item = (int) $item;

            echo $items->remove_item($item);
            exit();
        case 'clear':
            echo $items->clear_items();
            exit();
        case 'edit':
            $missing_params = [];
            if (!isset($_GET['item'])) {
                array_push($missing_params, 'item');
                exit();
            }
            if (!isset($_GET['qnt'])) {
                array_push($missing_params, 'quantidade');
                exit();
            }

            if (count($missing_params) > 0) {
                echo error_json(2, 'Parâmetros faltando.', 'Parâmetros faltando: ' . join(', ', $missing_params));
                exit();
            }

            $non_numeric_params = [];
            if (!is_numeric($_GET['item'])) {
                array_push($non_numeric_params, 'item');
                exit();
            }
            if (!is_numeric($_GET['qnt'])) {
                array_push($non_numeric_params, 'quantidade');
                exit();
            }

            if (count($non_numeric_params) > 0) {
                echo error_json(3, 'Parâmetros não numéricos.', 'Parâmetros não numéricos: ' . join(', ', $non_numeric_params));
                exit();
            }

            $item =  (int) $_GET['item'];
            $qnt = (int) $_GET['qnt'];

            echo $items->edit_item($item, $qnt);
        default:
            echo error_json(4, 'Ação desconhecida.', 'Ação procurada: ' . $_GET['action']);
            exit();
    }

} else {
    echo error_json(1, 'Nenhum ID fornecido.');
    exit();
}

# 1-> parametros faltando
# 2-> parametros insuficientes
# 3-> parametros incorretos
# 4-> ação desconhecida
# ?-> erro sql