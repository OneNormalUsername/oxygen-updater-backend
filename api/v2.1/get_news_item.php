<?php
include "Repository/DatabaseConnector.php";

// Connect to the database
$databaseConnector = new DatabaseConnector();
$database = $databaseConnector->connectToDb();

// Obtain all required request parameters.
$newsItemId = $_GET["news_item_id"];

// Execute the query
$query = $database->prepare("SELECT id, dutch_title, english_title, dutch_subtitle, english_subtitle, image_url, dutch_text, english_text, date_published, date_last_edited, author_name FROM news_item WHERE id = :id AND published = TRUE");
$query->bindParam(":id", $newsItemId);
$query->execute();

// Return the output as JSON
header('Content-type: application/json');
echo (json_encode($query->fetch(PDO::FETCH_ASSOC)));

// Disconnect from the database
$database = null;