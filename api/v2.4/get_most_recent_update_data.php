<?php
include '../shared/database.php';
include '../shared/filename.php';

// Obtain all required request parameters.
$device_id = $_GET["device_id"];
$update_method_id = $_GET["update_method_id"];

header('Content-type: application/json');

if($device_id != null && $update_method_id != null && $device_id != "" && $update_method_id != "") {

    // Connect to the database
    $database = connectToDatabase();

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

        // HOTFIX: On app versions <= 2.7.3, limit download size to 2047 MB (2147483647 bytes) as the app would otherwise have an integer overflow.
        // This was detected by the OnePlus 7 Pro having a full update of 2067 MB.
        // The fix is only applied to versions 2.7.2 and 2.7.3, as other versions should rarely be in use on the OnePlus 7 Pro
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'Oxygen_updater_2.7.3') !== FALSE || strpos($_SERVER['HTTP_USER_AGENT'], 'Oxygen_updater_2.7.2') !== FALSE) {
            $downloadSizeNumeric = intval($result['download_size']);
            if ($downloadSizeNumeric > 2147483647) {
                error_log('Reduced download size of update data with OTA version ' . $result['ota_version_number'] . ' to 2047 MB to avoid an integer overflow');
                $result['description'] .= '
               
##Download size
The download size of this file is ' . ($result['download_size'] / 1048576) . ' MB. Unfortunately, the app cannot currently display values larger than 2047 MB. Please ignore the download size shown below.';
                $result['download_size'] = '2147483647';
            }
        }

        echo(json_encode($result));
    }

    // Disconnect from the database
    $database = null;
}
else {
    echo(json_encode(array("error" => "No device ID and / or update method ID supplied.", "update_information_available" => false, "system_is_up_to_date" => false)));
}
