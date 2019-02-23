<?php
include '../shared/database.php';

// Connect to the database
$database = connectToDatabase();

// Fetch all update methods that are enabled for this device.
$query = $database->prepare("SELECT um.id, um.english_name, um.dutch_name, um.recommended_for_rooted_device, um.recommended_for_non_rooted_device FROM update_method um WHERE um.enabled = TRUE");
$query->execute();

// Return the output as JSON
header('Content-type: application/json');
echo (json_encode($query->fetchAll(PDO::FETCH_ASSOC)));

// Disconnect from the database
$database = null;
