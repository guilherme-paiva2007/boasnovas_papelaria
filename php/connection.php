<?php
$dir_level = 1;

if (!isset($setup)) include './setup.php';

$connection_config = $localconfig['connection'];

$connection = new mysqli(
    $connection_config['hostname'],
    $connection_config['username'],
    $connection_config['password'],
    $connection_config['database']
);