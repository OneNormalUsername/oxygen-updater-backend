<?php
include 'Repository/DatabaseConnector.php';

// Obtain all required request parameters.
// Set the return type to JSON.
header('Content-type: application/json');

// Connect to the database
$databaseConnector = new DatabaseConnector();
$database = $databaseConnector->connectToDb();

// Fetch all update methods that are enabled for this device.
$query = $database->prepare("SELECT um.id, um.english_name, um.dutch_name, um.recommended_for_rooted_device, um.recommended_for_non_rooted_device FROM update_method um WHERE um.enabled = TRUE");
$query->execute();

// Return the output as JSON
echo (json_encode($query->fetchAll(PDO::FETCH_ASSOC)));

// Disconnect from the database
$database = null;
