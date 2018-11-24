<?php
include "Repository/DatabaseConnector.php";

// Connect to the database
$databaseConnector = new DatabaseConnector();
$database = $databaseConnector->connectToDb();

// Obtain all required request parameters.
$deviceId = $_GET["device_id"];
$updateMethodId = $_GET["update_method_id"];

// Execute the query
$query = $database->prepare("SELECT id, dutch_title, english_title, dutch_subtitle, english_subtitle, image_url FROM news_item WHERE (device_id IS NULL OR device_id = :device_id) AND (update_method_id IS NULL OR update_method_id = :update_method_id) AND published = TRUE ORDER BY ID DESC LIMIT 20");
$query->bindParam(":device_id", $deviceId);
$query->bindParam(":update_method_id", $updateMethodId);
$query->execute();

// Return the output as JSON
header('Content-type: application/json');
echo (json_encode($query->fetchAll(PDO::FETCH_ASSOC)));

// Disconnect from the database
$database = null;