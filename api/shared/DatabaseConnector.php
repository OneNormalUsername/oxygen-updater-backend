<?php

class DatabaseConnector {
    public function connectToDb() {
        $username = "root";
        $password = "test1234";
        $server_address = "mariadb";
        $database_name = "oxygen_updater";
        $database = new PDO('mysql:host='.$server_address.';dbname='.$database_name.'',$username, $password);
        $database->query('SET CHARACTER SET utf8');
        return $database;
    }
}

