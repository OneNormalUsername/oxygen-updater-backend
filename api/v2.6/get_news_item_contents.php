<?php
include '../shared/database.php';

// Connect to the database
$database = connectToDatabase();

// Obtain all required request parameters.
$newsItemId = $_GET["news_item_id"];
// Escape the language parameter as its directly written to the HTML
$language = substr(strtolower($_GET["language"]), 0, 2);
$theme = $_GET["theme"]; // Light, Dark or "" ("" only on app 2.7.6 and older)

// Input validation
if ($language !== 'nl' && $language !== 'en') {
    die();
}

if ($theme !== 'Light' && $theme !== 'Dark' && $theme !== '') {
    die();
}

// Execute the query
if ($language === 'nl') {
    $query = $database->prepare("SELECT dutch_text as content, dutch_title as title, dutch_subtitle as description FROM news_item WHERE id = :id AND published = TRUE");
} else {
    $query = $database->prepare("SELECT english_text as content, english_title as title, english_subtitle as description FROM news_item WHERE id = :id AND published = TRUE");
}

$query->bindParam(":id", $newsItemId);
$query->execute();

// Return the output as HTML
header('Content-type: text/html');
$style = "<style>
            body {
              font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", \"Roboto\", \"Helvetica Neue\", Arial, sans-serif;
            }

            blockquote {
              border-left: 2px solid rgba(0,0,0,.12);
              margin-left: 1.5rem;
              padding-left: 1rem;
            }
          </style>";
$result = $query->fetch(PDO::FETCH_ASSOC);
$title = $result['title'];
$description = $result['description'];
$contents = $result['content'];

if ($theme === 'Dark') {
    $style = "<style>
                body {
                  font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", \"Roboto\", \"Helvetica Neue\", Arial, sans-serif;
                  background-color: #121212;
                  color: white;
                }

                blockquote {
                  border-left: 2px solid rgba(255,255,255,.12);
                  margin-left: 1.5rem;
                  padding-left: 1rem;
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
        <!DOCTYPE html>
        <html lang=\"" . $language . "\" dir=\"ltr\">
          <head>
            ". $style . "
            <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
            <meta name='Description' content='". $description . "'>
            <title>" . $title . "</title>
          </head>
          <body>
          " . $contents . "
          </body>
        </html>
    ";

// Disconnect from the database
$database = null;
