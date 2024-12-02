<?php # GET
header('Content-Type: application/json');

include 'connection.php';

$search = '';

if (isset($_GET['search'])) {
    $search = $_GET['search'];
}

$searchlike = $search . '%';

$query = "SELECT * FROM promocoes WHERE titulo LIKE ?";

$statement = $connection->prepare($query);
$statement->bind_param('s', $searchlike);
$statement->execute();

if ($statement->error) {
    echo error_json(1, "Erro na execução da requisição.", $statement->error);
    exit();
}

$result = $statement->get_result();

echo promotions_json($result);