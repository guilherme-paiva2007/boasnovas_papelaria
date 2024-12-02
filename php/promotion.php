<?php # GET
header('Content-Type: application/json');

include 'connection.php';

if (!isset($_GET['id'])) {
    echo error_json(1, "ID não fornecido.");
    exit();
}

$id = $_GET['id'];

if (!is_numeric($id)) {
    echo error_json(2, "ID fornecido não é numérico.");
    exit();
}

$query = "SELECT * FROM promocoes WHERE id = ? LIMIT 1";

$statement = $connection->prepare($query);
$statement->bind_param('i', $id);
$statement->execute();

if ($statement->error) {
    echo error_json(4, "Erro na execução da requisição.", $statement->error);
    exit();
}

$result = $statement->get_result();

if ($result->num_rows === 0) {
    echo error_json(3, "Promoção não encontrada.");
    exit();
}

$promotion = $result->fetch_assoc();

$query = "SELECT * FROM itens WHERE ligacao = 'promocao' AND id_ligacao = ?";

$statement = $connection->prepare($query);
$statement->bind_param('i', $id);
$statement->execute();

if ($statement->error) {
    echo error_json(4, "Erro na execução da requisição.", $statement->error);
    exit();
}

$result = $statement->get_result();

$products = [];

while ($product = $result->fetch_assoc()) {
    array_push($products, $product);
}

echo promotion_json($promotion, $products);
exit();

# 1-> parametro faltando
# 2-> parametro incorreto
# 3-> nao encontrado
# 4-> erro sql