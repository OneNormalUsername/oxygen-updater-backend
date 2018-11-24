<?php
include 'Repository/DatabaseConnector.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['HTTP_USER_AGENT'], 'Oxygen_updater_') !== false) {

    // Connect to the database
    $database = (new DatabaseConnector())->connectToDb();

    //Get the news item ID from the request body
    $json = json_decode(file_get_contents('php://input'), true);
    $newsItemId = $json["news_item_id"];

    // Get old times read value from db.
    $oldTimesReadQuery = $database->prepare("SELECT times_read FROM news_item WHERE id = :id");
    $oldTimesReadQuery->bindParam(":id", $newsItemId);
    $oldTimesReadQuery->execute();

    // Increment old times read value by 1
    $oldTimesRead = intval($oldTimesReadQuery->fetch(PDO::FETCH_ASSOC)["times_read"]);
    $newTimesRead = $oldTimesRead + 1;

    // Store new value in the db.
    $newTimesReadQuery = $database->prepare("UPDATE news_item SET times_read = :new_times_read WHERE id = :id");
    $newTimesReadQuery->bindParam(":id", $newsItemId);
    $newTimesReadQuery->bindParam(":new_times_read", $newTimesRead);
    $newTimesReadQuery->execute();

    // Disconnect form the database;
    $database = null;

    // Return a success message if the news item has been updated successfully.
    if ($newTimesReadQuery->rowCount() > 0) {
        echo(json_encode(array("success" => true)));
    } else {
        echo(json_encode(array("success" => false, "errorMessage" => "Unable to mark news item as read: News item with ID " . $newsItemId . " not present in the database.")));
    }
}
