<?php #GET
header('Content-Type: application/json');

include 'connection.php';

if (isset($_GET['search'])) {
    $search = $_GET['search'];

    if ($search === "") {
        echo error_json(2, 'Requisição de pesquisa vazia.');
        exit();
    }

    $searchlike = $search . "%";

    $query = "SELECT * FROM produtos WHERE nome LIKE ?";
    $statement = $connection->prepare($query);
    $statement->bind_param('s', $searchlike);
    $statement->execute();

    if ($statement->error) {
        echo error_json(3, 'Erro na execução da consulta.', $statement->error);
        exit();
    }

    $result = $statement->get_result();

    echo search_json($result);
    
} else {
    echo error_json(1, 'Faltando requisição de pesquisa.');
    exit();
}

# search errors:
# 1-> sem requisição de pesquisa
# 2-> pesquisa vazia
# 3-> erro sql