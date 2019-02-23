<?php
include '../shared/database.php';

// Connect to the database
$database = connectToDatabase();

// Obtain all required request parameters.
$deviceId = $_GET["device_id"];
$updateMethodId = $_GET["update_method_id"];

// Execute the query
$query = $database->prepare("SELECT id, english_message, dutch_message, device_id, update_method_id, priority FROM server_message WHERE (device_id IS NULL OR device_id = :device_id) AND (update_method_id IS NULL OR server_message.update_method_id = :update_method_id) AND enabled = TRUE ORDER BY sequence");
$query->bindParam(":device_id", $deviceId);
$query->bindParam(":update_method_id", $updateMethodId);
$query->execute();

// Return the output as JSON
header('Content-type: application/json');
echo (json_encode($query->fetchAll(PDO::FETCH_ASSOC)));

// Disconnect from the database
$database = null;
