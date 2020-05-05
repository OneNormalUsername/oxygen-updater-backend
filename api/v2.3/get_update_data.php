<?php
include '../shared/database.php';
include '../shared/filename.php';

// Obtain all required request parameters.
$device_id = $_GET["device_id"];
$update_method_id = $_GET["update_method_id"];
$parent_version_number = $_GET["parent_version_number"];

// Set the return type to JSON.
header('Content-type: application/json');

// Check if all required query parameters are set.
if($device_id != null && $update_method_id != null && $device_id != "" && $update_method_id != "" && $parent_version_number != null && $parent_version_number != "") {

    // Connect to the database
    $database = connectToDatabase();

    // Log the used operating system version (only when using the app).
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'Oxygen_updater_') !== FALSE) {
        $previouslyLoggedOperatingSystemVersionQuery = $database->prepare("SELECT * FROM used_operating_system_version where operating_system_version = :operating_system_version order by id limit 1");
        $previouslyLoggedOperatingSystemVersionQuery->bindParam(':operating_system_version', $parent_version_number);
        $previouslyLoggedOperatingSystemVersionQuery->execute();

        // If it has never been logged before, create a new log entry.
        if ($previouslyLoggedOperatingSystemVersionQuery->rowCount() == 0) {
            $logOperatingSystemVersionQuery = $database->prepare("INSERT INTO used_operating_system_version(operating_system_version, times_logged) VALUES (:operating_system_version, 1)");
            $logOperatingSystemVersionQuery->bindParam(':operating_system_version', $parent_version_number);
            $logOperatingSystemVersionQuery->execute();

            // Test if the newly-created entry is considered "missing" (see vw_missing_update_data). If so, send a message to the #contributors channel on Discord
            $missingViewQuery = $database->prepare("SELECT * FROM vw_missing_update_data where version_number = :operating_system_version");
            $missingViewQuery->bindParam(':operating_system_version', $parent_version_number);
            $missingViewQuery->execute();

            // If a row exists, the version is "missing" and the message must be sent.
            if ($missingViewQuery->rowCount() > 0) {
                include '../shared/webhook.php';

                // Message author and action URL not available on GitHub.
                $authorName = getenv('MISSING_UPDATE_VERSIONS_WEBHOOK_AUTHOR_NAME');
                $messageActionUrl = getenv('MISSING_UPDATE_VERSIONS_WEBHOOK_ACTION_URL');
                $webhookEmbed = make_webhook_embed(
                    make_webhook_author(),
                    'New OTA version spotted',
                    $messageActionUrl,
            "```properties
$parent_version_number
```",
                    make_webhook_footer($authorName),
                    'https://cdn4.iconfinder.com/data/icons/digital-design-bluetone-set-2/91/Digital__Design_72-512.png'
                );

                // webhook URL not available on GitHub to prevent abuse
                $webhookUrl = getenv('MISSING_UPDATE_VERSIONS_WEBHOOK_URL');
                make_webhook_call(
                    $webhookUrl,
                    null,
                    $webhookEmbed
                );
            }

        } else {
            // Else, increment the usage count of the existing log entry.
            $incrementOperatingSystemVersionUsageQuery = $database->prepare("UPDATE used_operating_system_version SET times_logged = times_logged + 1 where operating_system_version = :operating_system_version");
            $incrementOperatingSystemVersionUsageQuery->bindParam(':operating_system_version', $parent_version_number);
            $incrementOperatingSystemVersionUsageQuery->execute();
        }
    }

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
// If the device or update method IDs are not supplied, throw an error message.
else {
    echo(json_encode(array("error" => "No device ID and / or update method ID and /or parent version number supplied.", "update_information_available" => false, "system_is_up_to_date" => false)));
}
