<?php
include '../shared/DatabaseConnector.php';

// Always compare log dates using Dutch time zone
date_default_timezone_set('Europe/Amsterdam');

// Set the return type to JSON.
header('Content-type: application/json');

// If this class is not accessed over POST or if it is requested from a browser, prevent executing the script.
if($_SERVER['REQUEST_METHOD'] !== 'POST' || strpos($_SERVER['HTTP_USER_AGENT'], 'Oxygen_updater_') === FALSE) {
    http_response_code(403);
    echo json_encode(array("success" => false, "error_message" => "Access Denied"));
    die();
}

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
$eventTimeStamp = strtotime($eventDate);
$currentTimeStamp = time();

// If log date is past today (wrong time set on phone) or older than a full day ago (also likely wrong time set on phone), set it to current timestamp.
if ($eventTimeStamp > $currentTimeStamp || $eventTimeStamp < ($currentTimeStamp - 86400)) {
	$eventDate = strftime('%Y-%m-%dT%H:%I:%S.000', $currentTimeStamp);
}

if ($deviceId === '-1' || $deviceId === -1) {
    $deviceId = null;
}

if ($updateMethodId === '-1' || $updateMethodId === -1) {
    $updateMethodId = null;
}

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

