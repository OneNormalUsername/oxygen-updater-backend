<?php

/**
 * Connect to the database using the credentials stored in the operating environment
 * @return PDO Database connection
 */
function connectToDatabase() {
    $databaseUsername = getenv("DATABASE_USER");
    $databasePassword = getenv("DATABASE_PASS");
    $databaseHost = getenv("DATABASE_HOST");
    $databaseName = getenv("DATABASE_NAME");
    $databasePort = getenv('DATABASE_PORT');
    $database = new PDO('mysql:host=' . $databaseHost . ';port=' . $databasePort . ';dbname=' . $databaseName . '', $databaseUsername, $databasePassword);
    $database->query('SET CHARACTER SET utf8');
    return $database;
}
