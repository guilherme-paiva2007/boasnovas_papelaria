<?php #include
$scripts = true;

$dirbase = "boasnovas_papelaria";

/**
 * Create a link based on the base directory where the project is.
 * @param string $destination
 * @return string
 */
function createLink($destination) {
    global $dirbase;
    $base = $dirbase . '/';

    return '/' . $base . $destination;
}

/**
 * Create a link based on the current location
 * @param string $destination
 * @param int $backLevel
 * @return string
 */
function createRefLink($destination, $backLevel = 0) {
    if ($backLevel < 0) $backLevel *= -1;

    $dots = "";
    for ($i = 0; $i < $backLevel; $i++) {
        $dots = $dots . ".";
    }

    return "$dots" . "./" . "$destination";
}

function levelToNumber($level = '') {
    switch ($level) {
        case 'admin':
            return 2;
        case 'cliente':
            return 1;
        default:
            return 0;
    }
}

/**
 * Redirection by the access level
 * @param integer $level
 * @param integer $levelRequired
 * @return bool
 */
function levelAllowed($level, $levelRequired) {
    if (gettype($level) !== "integer") $level = levelToNumber($level);
    if (gettype($levelRequired) !== "integer") $levelRequired = levelToNumber($levelRequired);

    if ($level < $levelRequired) return false;
    return true;
}

function checkLevelInSession($levelRequired) {
    if (gettype($levelRequired) !== "integer") $levelRequired = levelToNumber($levelRequired);

    if (isset($_SESSION['nivel'])) { #se necessário, alterar a chave de acordo com o banco de dados
        $level = $_SESSION['nivel'];
    } else {
        $level = 0;
    }

    return levelAllowed($level, $levelRequired);
}

class itemsList {
    public $id_ligacao;
    public $tipo_ligacao;
    public $itens = [];
    private $connection;

    /**
     * Inicializa uma lista de produtos
     * @param int $id
     * @param string $tipo
     * @param mysqli $connection
     */
    function __construct($id, $tipo, $connection) {
        $this->id_ligacao = $id;
        $this->tipo_ligacao = $tipo;
        $this->connection = $connection;

        $query = "SELECT produto, quantidade, id FROM itens WHERE id_ligacao = ? AND ligacao = ?";

        $statement = $connection->prepare($query);
        $statement->bind_param('is', $id, $tipo);
        $statement->execute();

        if ($statement->error) {
            echo error_json(-1, 'Erro na execução da consulta.', $statement->error);
            exit();
        }

        $result = $statement->get_result();

        while ($row = $result->fetch_assoc()) {
            array_push($itens, $row);
        }
    }

    function get_items_json() {
        return json_encode($this->itens, JSON_UNESCAPED_UNICODE);
    }

    function edit_item($id, $qnt) {
        foreach ($this->itens as $item_search) {
            if ((int) $item_search['id'] === (int) $id) {
                $item = $item_search;
                break;
            }
        }

        if (isset($item)) {
            $query = "UPDATE itens SET quantidade = ? WHERE id = ?";

            $statement = $this->connection->prepare($query);
            $param_qnt = (int) $qnt;
            $param_id = (int) $item['id'];
            $statement->bind_param('ii', $param_qnt, $param_id);
            $statement->execute();

            return crud_json('update', [
                'id' => $item['id'],
                'produto' => $item['produto'],
                'quantidade' => $param_qnt
            ], 'item');
        } else {
            return error_json(-1, 'Item não encontrado.', 'ID procurado: ' . $id);
        }
    }

    function add_item($produto, $qnt) {
        $produto = (int) $produto;
        $qnt = (int) $qnt;

        $query = "INSERT INTO itens (id_ligacao, ligacao, produto, quantidade) VALUES (?, ?, ?, ?)";
        $statement = $this->connection->prepare($query);
        $statement->bind_param('isii', $this->id_ligacao, $this->tipo_ligacao, $produto, $qnt);
        $statement->execute();

        if ($statement->error) {
            return error_json(-1, 'Erro na execução da inserção.', $statement->error);
        }

        return crud_json('create', [
            'id_ligacao' => $this->id_ligacao,
            'ligacao' => $this->tipo_ligacao,
            'produto' => $produto,
            'quantidade' => $qnt
        ], 'item');
    }

    function remove_item($id) {
        $id = (int) $id;

        $query = "DELETE FROM itens WHERE id = ?";
        $statement = $this->connection->prepare($query);
        $statement->bind_param('i', $id);
        $statement->execute();

        if ($statement->error) {
            return error_json(-1, 'Erro na execução da exclusão.', $statement->error);
        }

        return crud_json('delete', $id, 'item');
    }

    function clear_items() {
        $query = "DELETE FROM itens WHERE id_ligacao = ? AND ligacao = ?";
        $statement = $this->connection->prepare($query);
        $statement->bind_param('is', $this->id_ligacao, $this->tipo_ligacao);
        $statement->execute();

        if ($statement->error) {
            return error_json(-1, 'Erro na execução da exclusão.', $statement->error);
        }

        return crud_json('delete', $this->id_ligacao, 'connection_id');
    }
}

function error_json($code, $info = '', $comment = '') {
    return json_encode([
        'status' => 'error',
        'error' => $code,
        'message' => $info,
        'comment' => $comment
    ], JSON_UNESCAPED_UNICODE);
}

/**
 * JSON resultado de uma pesquisa
 * @param mysqli_result $result
 * @return string
 */
function search_json($result) {
    $response = [
        'status' => 'okay',
        'length' => $result->num_rows,
        'results' => []
    ];

    while ($row = $result->fetch_assoc()) {
        array_push($response['results'], $row);
    }

    return json_encode($response, JSON_UNESCAPED_UNICODE);
}

/**
 * JSON resultado de uma pesquisa de promoções
 * @param mysqli_result $result
 * @return string
 */
function promotions_json($result) {
    $response = [
        'status' => 'okay',
        'length' => $result->num_rows,
        'results' => []
    ];

    while ($row = $result->fetch_assoc()) {
        array_push($response['results'], $row);
    }

    return json_encode($response, JSON_UNESCAPED_UNICODE);
}

/**
 * JSON resultado de um usuário
 * @param mysqli_result $result
 * @return string
 */
function profile_json($result) {
    $response = [
        'status' => 'okay',
        'user' => $result->fetch_assoc()
    ];

    return json_encode($response, JSON_UNESCAPED_UNICODE);
}

function promotion_json($promotion, $products) {
    $response = [
        'status' => 'okay',
        'promotion' => $promotion,
        'products' => $products
    ];

    return json_encode($response, JSON_UNESCAPED_UNICODE);
}

function cart_json($cart, $products) {
    $response = [
        'status' => 'okay',
        'cart' => $cart,
        'products' => $products
    ];

    return json_encode($response, JSON_UNESCAPED_UNICODE);
}

function crud_json($action, $value, $key) {
    $response = [
        'status' => 'okay',
        'action' => $action,
    ];
    $response[$key] = $value;

    return json_encode($response, JSON_UNESCAPED_UNICODE);
}

function login_json($usuario) {
    $response = [
        'status' => 'okay',
        'user' => $usuario
    ];

    return json_encode($response, JSON_UNESCAPED_UNICODE);
}

function register_json($usuario) {
    $response = [
        'status' => 'okay',
        'user' => $usuario
    ];

    return json_encode($response, JSON_UNESCAPED_UNICODE);
}

function logout_json() {
    return json_encode([
        'status' => 'okay',
        'message' => 'Sessão encerrada.'
    ], JSON_UNESCAPED_UNICODE);
}
