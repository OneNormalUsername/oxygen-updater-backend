<?php
include 'Repository/DatabaseConnector.php';
include 'filename.php';

// Obtain all required request parameters.
$device_id = $_GET["device_id"];
$update_method_id = $_GET["update_method_id"];

header('Content-type: application/json');

if($device_id != null && $update_method_id != null && $device_id != "" && $update_method_id != "") {

    // Connect to the database
    $databaseConnector = new DatabaseConnector();
    $database = $databaseConnector->connectToDb();

    $query = $database->prepare("SELECT * FROM update_data WHERE device_id = :device_id AND update_method_id = :update_method_id AND is_latest_version = TRUE ORDER BY id DESC LIMIT 1");
    $query->bindParam(':device_id', $device_id);
    $query->bindParam(':update_method_id', $update_method_id);
    $query->execute();

    if($query->rowCount() == 0) {
        echo json_encode(array("error" => "unable to find most recent update data", "update_information_available" => false, "system_is_up_to_date" => false));
    } else {
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $result["update_information_available"] = true;
        $result["system_is_up_to_date"] = true;
        $result['filename'] = getFilename($result['download_url']);
        
        echo(json_encode($result));
    }

    // Disconnect from the database
    $database = null;
}
else {
    echo(json_encode(array("error" => "No device ID and / or update method ID supplied.", "update_information_available" => false, "system_is_up_to_date" => false)));
}
