<?php
include 'Repository/DatabaseConnector.php';
include 'filename.php';

// Obtain all required request parameters.
$device_id = $_GET["device_id"];
$update_method_id = $_GET["update_method_id"];
$parent_version_number = $_GET["parent_version_number"];

// Set the return type to JSON.
header('Content-type: application/json');

// Check if all required query parameters are set.
if($device_id != null && $update_method_id != null && $device_id != "" && $update_method_id != "" && $parent_version_number != null && $parent_version_number != "") {

    // Connect to the database
    $databaseConnector = new DatabaseConnector();
    $database = $databaseConnector->connectToDb();

    // Test if the update method uses the new Incremental (parent) system or not.
    $updateMethodQuery = $database->prepare("SELECT * FROM update_method WHERE id = :update_method_id");
    $updateMethodQuery->bindParam(':update_method_id', $update_method_id);
    $updateMethodQuery->execute();

    $updateMethod = $updateMethodQuery->fetch(PDO::FETCH_ASSOC);

    // Find update data
    $query = $database->prepare("SELECT * FROM update_data WHERE device_id = :device_id AND update_method_id = :update_method_id AND parent_version_number = :parent_version_number");
    $query->bindParam(':device_id', $device_id);
    $query->bindParam(':update_method_id', $update_method_id);
    $query->bindParam(':parent_version_number', $parent_version_number);
    $query->execute();

    // If there are no results, the system is up to date or no update information has been found. Else, return the result.
    if($query->rowCount() == 0) {
        $totalCountQuery = $database->prepare("SELECT COUNT(*) FROM update_data WHERE device_id = :device_id AND update_method_id = :update_method_id");
        $totalCountQuery->bindParam(':device_id', $device_id);
        $totalCountQuery->bindParam(':update_method_id', $update_method_id);
        $totalCountQuery->execute();

        $totalCountResult = $totalCountQuery->fetch(PDO:: FETCH_COLUMN);

        echo json_encode(array("information" => "unable to find a more recent build", "update_information_available" => $totalCountResult[0] != 0, "system_is_up_to_date" => true));
    } else {
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $result["update_information_available"] = true;
        $result["system_is_up_to_date"] = false;
        $result['filename'] = getFilename($result['download_url']);

        echo(json_encode($result));
    }

    // Disconnect from the database
    $database = null;
}
// If the device or update method IDs are not supplied, throw an error message.
else {
    echo(json_encode(array("error" => "No device ID and / or update method ID and /or parent version number supplied.", "update_information_available" => false, "system_is_up_to_date" => false)));
}
