<?php
include '../shared/database.php';

// Connect to the database
$database = connectToDatabase();

// Obtain all required request parameters.
$deviceId = $_GET["device_id"];
$updateMethodId = $_GET["update_method_id"];

// Execute the query
$query = $database->prepare("SELECT news_item.id, dutch_title, english_title, dutch_subtitle, english_subtitle, image_url FROM news_item LEFT JOIN news_item_device nid on news_item.id = nid.news_item_id LEFT JOIN news_item_update_method nium on news_item.id = nium.news_item_id WHERE published = TRUE AND (nid.device_id IS NULL OR nid.device_id = :device_id) AND (nium.update_method_id IS NULL OR nium.update_method_id = :update_method_id) ORDER BY ID DESC LIMIT 20");
$query->bindParam(":device_id", $deviceId);
$query->bindParam(":update_method_id", $updateMethodId);
$query->execute();

// Return the output as JSON
header('Content-type: application/json');
echo (json_encode($query->fetchAll(PDO::FETCH_ASSOC)));

// Disconnect from the database
$database = null;
