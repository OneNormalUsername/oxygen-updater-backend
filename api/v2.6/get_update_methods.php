<?php
include '../shared/database.php';

// Obtain all required request parameters.
$device_id = $_GET["device_id"];

// Set the return type to JSON.
header('Content-type: application/json');

// Check if all required query parameters are set.
if($device_id != null && $device_id != "") {

    // Connect to the database
    $database = connectToDatabase();

    // Fetch all update methods that are enabled for this device.
    $query = $database->prepare("SELECT um.id, um.english_name, um.dutch_name, um.recommended_for_non_rooted_device, um.recommended_for_rooted_device, um.supports_rooted_device FROM update_method um JOIN device_update_method du ON um.id = du.update_method_id WHERE du.device_id = :device_id AND um.enabled = TRUE ORDER BY um.english_name ASC");
    $query->bindParam(':device_id', $device_id);
    $query->execute();
    
    // Return the output as JSON
    echo (json_encode($query->fetchAll(PDO::FETCH_ASSOC)));

    // Disconnect from the database
    $database = null;
}
else {
    // If the device ID is not supplied, throw an error message.
    http_response_code(500);
    header('Content-type: application/json');
    echo (json_encode(array("error" => "No device ID specified.")));
}
