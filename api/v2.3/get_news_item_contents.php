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
                  background-color: #121212;
                  color: white;
                }
                
                a {
                  color: #64b5f6;
                }
                
                /* If clicked on 'black' text color explicitly in news editor then it will add it to inline style.
                   Override this as well to prevent unreadable text in dark mode
                  */
                [style*=\"color: #000000\"] {
                  color: white !important;
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
