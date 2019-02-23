<?php
include '../shared/database.php';

//Get the JSON from the request body
$json = json_decode(file_get_contents('php://input'), true);

// Replace invalid user-selected values with null database values
$deviceId = $json["device_id"];
$updateMethodId = $json["update_method_id"];

if ($deviceId === '-1' || $deviceId === -1) {
    $deviceId = null;
}

if ($updateMethodId === '-1' || $updateMethodId === -1) {
    $updateMethodId = null;
}

// Set the return type to JSON.
header('Content-type: application/json');

//Establish a database connection
$database = connectToDatabase();
$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Store the log entry in the database.
$query = $database->prepare("INSERT INTO app_log(event_type, device_is_supported, device_id, update_method_id, device_name, operating_system_version, error_message, app_version, event_date) VALUES (:event_type, :device_is_supported, :device_id, :update_method_id, :device_name, :operating_system_version, :error_message, :app_version, :event_date)");
$query->bindValue(':event_type', $json["event_type"]);
$query->bindValue(':device_is_supported',  $json["device_is_supported"] == '' ? 0 : $json["device_is_supported"]);
$query->bindParam(':device_id', $deviceId);
$query->bindParam(':update_method_id', $updateMethodId);
$query->bindValue(':device_name', $json["device_name"]);
$query->bindValue(':operating_system_version', $json["operating_system_version"]);
$query->bindValue(':error_message', $json["error_message"]);
$query->bindValue(':app_version', $json["app_version"]);
$query->bindValue(':event_date', $json["event_date"]);

$query->execute();

// Disconnect from the database.
$database = null;

// Return a success message if the log entry has been inserted successfully.
if ($query->rowCount() > 0) {
    echo(json_encode(array("success" => true)));
} else {
    echo(json_encode(array("success" => false, "errorMessage" => "Unable to store log entry into the database.")));
}

