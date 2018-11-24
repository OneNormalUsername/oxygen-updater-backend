<?php
include '../shared/DatabaseConnector.php';

// Connect to the database
$databaseConnector = new DatabaseConnector();
$database = $databaseConnector->connectToDb();

// Execute the query
$query = $database->query("SELECT status, latest_app_version, automatic_installation_enabled FROM server_status");
$result = $query->fetch(PDO::FETCH_ASSOC);

// Ban usage of App 2.3.3 as it is an unstable version.
if (strpos($_SERVER['HTTP_USER_AGENT'], 'Oxygen_updater_2.3.3') !== FALSE) {
    $result['status'] = 'OUTDATED';
}

// Return the output as JSON
header('Content-type: application/json');
echo (json_encode($result));

// Disconnect from the database
$database = null;