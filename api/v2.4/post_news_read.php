<?php
include '../shared/database.php';

// Set the return type to JSON.
header('Content-type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['HTTP_USER_AGENT'], 'Oxygen_updater_') !== false) {

    // Connect to the database
    $database = connectToDatabase();

    //Get the news item ID from the request body
    $json = json_decode(file_get_contents('php://input'), true);
    $newsItemId = $json["news_item_id"];

    // Update the the amount of times read in the db.
    $updateTimesReadQuery = $database->prepare("UPDATE news_item SET times_read = times_read + 1 WHERE id = :id");
    $updateTimesReadQuery->bindParam(":id", $newsItemId);
    $updateTimesReadQuery->execute();

    // Disconnect form the database;
    $database = null;

    // Return a success message if the news item has been updated successfully.
    if ($updateTimesReadQuery->rowCount() > 0) {
        echo(json_encode(array("success" => true)));
    } else {
        echo(json_encode(array("success" => false, "errorMessage" => "Unable to mark news item as read due to a database error.")));
    }
} else {
    echo json_encode(array("success" => false, "error_message" => "Access Denied"));
}