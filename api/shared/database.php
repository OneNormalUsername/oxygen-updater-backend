<?php

/**
 * Connect to the database using the credentials stored in the operating environment
 * @return PDO Database connection
 */
function connectToDatabase() {
    $username = getenv("DATABASE_USER");
    $password = getenv("DATABASE_PASS");
    $server_address = getenv("DATABASE_HOST");
    $database_name = getenv("DATABASE_NAME");
    $database = new PDO('mysql:host='.$server_address.';dbname='.$database_name.'',$username, $password);
    $database->query('SET CHARACTER SET utf8');
    return $database;
}

