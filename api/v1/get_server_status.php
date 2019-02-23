<?php
include '../shared/database.php';

// Connect to the database
$database = connectToDatabase();

// Execute the query
$query = $database->query("SELECT status, latest_app_version FROM server_status");

// Return the output as JSON
header('Content-type: application/json');
echo (json_encode($query->fetch(PDO::FETCH_ASSOC)));

// Disconnect from the database
$database = null;