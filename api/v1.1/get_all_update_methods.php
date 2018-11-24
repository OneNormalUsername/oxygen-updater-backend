<?php
include 'Repository/DatabaseConnector.php';

// Obtain all required request parameters.
// Set the return type to JSON.
header('Content-type: application/json');

// Connect to the database
$databaseConnector = new DatabaseConnector();
$database = $databaseConnector->connectToDb();

// Fetch all update methods that are enabled for this device.
$result = $database->query("SELECT um.id, um.english_name, um.dutch_name, um.recommended_for_non_rooted_device AS recommended FROM update_method um WHERE um.enabled = TRUE");

// Return the output as JSON
echo (json_encode($result->fetchAll(PDO::FETCH_ASSOC)));

// Disconnect from the database
$database = null;
