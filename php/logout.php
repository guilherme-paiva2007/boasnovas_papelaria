<?php #open
session_start();
session_unset();
session_destroy();

include 'script.php';

echo logout_json();