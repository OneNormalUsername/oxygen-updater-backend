<?php
include 'Repository/DatabaseConnector.php';

//Get the JSON from the request body
$json = json_decode(file_get_contents('php://input'), true);

//Get required request parameters from the JSON body.
$eventType = $json["event_type"];
$deviceIsSupported = $json["device_is_supported"] == '' ? 0 : $json["device_is_supported"];
$deviceId = $json["device_id"];
$updateMethodId = $json["update_method_id"];
$deviceName = $json["device_name"];
$operatingSystemVersion = $json["operating_system_version"];
$errorMessage = $json["error_message"];
$appVersion = $json["app_version"];
$eventDate = $json["event_date"];

if ($deviceId === '-1' || $deviceId === -1) {
    $deviceId = null;
}

if ($updateMethodId === '-1' || $updateMethodId === -1) {
    $updateMethodId = null;
}

// Set the return type to JSON.
header('Content-type: application/json');

//Establish a database connection
$databaseConnector = new DatabaseConnector();
$database = $databaseConnector->connectToDb();
$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Store the log entry in the database.
$query = $database->prepare("INSERT INTO app_log(event_type, device_is_supported, device_id, update_method_id, device_name, operating_system_version, error_message, app_version, event_date) VALUES (:event_type, :device_is_supported, :device_id, :update_method_id, :device_name, :operating_system_version, :error_message, :app_version, :event_date)");
$query->bindParam(':event_type', $eventType);
$query->bindParam(':device_is_supported', $deviceIsSupported);
$query->bindParam(':device_id', $deviceId);
$query->bindParam(':update_method_id', $updateMethodId);
$query->bindParam(':device_name', $deviceName);
$query->bindParam(':operating_system_version', $operatingSystemVersion);
$query->bindParam(':error_message', $errorMessage);
$query->bindParam(':app_version', $appVersion);
$query->bindParam(':event_date', $eventDate);

$query->execute();

// Disconnect from the database.
$database = null;

// Return a success message if the log entry has been inserted successfully.
if ($query->rowCount() > 0) {
    echo(json_encode(array("success" => true)));
} else {
    echo(json_encode(array("success" => false, "error_message" => "Unable to store log entry into the database.")));
}

