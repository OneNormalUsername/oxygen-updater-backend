<?php
include '../shared/database.php';

// Connect to the database
$database = connectToDatabase();

// Obtain all required request parameters.
$newsItemId = $_GET["news_item_id"];
$language = $_GET["language"];

// Execute the query
if (strtolower($language) === 'nl') {
  $query = $database->prepare("SELECT dutch_text as content FROM news_item WHERE id = :id AND published = TRUE");
} else {
  $query = $database->prepare("SELECT english_text as content FROM news_item WHERE id = :id AND published = TRUE");
}

$query->bindParam(":id", $newsItemId);
$query->execute();


// Return the output as HTML
header('Content-type: text/html');
echo ($query->fetch(PDO::FETCH_ASSOC)['content']);

// Disconnect from the database
$database = null;
