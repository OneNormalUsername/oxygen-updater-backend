<?php
include '../shared/database.php';

// Connect to the database
$database = connectToDatabase();

// Obtain all required request parameters.
$newsItemId = $_GET["news_item_id"];
$language = $_GET["language"];
$theme = $_GET["theme"]; // Light, Dark or "" ("" only on app 2.7.6 and older)

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
$style = "";
$contents = $query->fetch(PDO::FETCH_ASSOC)['content'];

if ($theme === 'Dark') {
    $style = "<style>
                body {
                  background-color: black;
                  color: white;
                }
              </style>";
}

echo "
        <html lang=" . $language . ">
          <head>
            ". $style . "
          </head>
          <body>
          " . $contents . "
          </body>
        </html>
    ";

// Disconnect from the database
$database = null;
