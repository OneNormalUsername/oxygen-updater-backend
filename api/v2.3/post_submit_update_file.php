<?php
include '../shared/database.php';
include '../shared/filename.php';

// Set the return type to JSON.
header('Content-type: application/json');

// If this class is not accessed over POST or if it is requested from a browser, prevent executing the script.
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || strpos($_SERVER['HTTP_USER_AGENT'], 'Oxygen_updater_') === FALSE) {
    http_response_code(403);
    echo json_encode(array("success" => false, "error_message" => "Access Denied"));
    die();
}

// Get the JSON from the request body
$json = json_decode(file_get_contents('php://input'), true);

$filename = $json['filename'];

// Check if a file name has been set. If not, throw a HTTP 400 Bad Request error.
if (empty($filename)) {
    http_response_code(400);
    echo json_encode(array("success" => false, "error_message" => "Bad Request"));
    die();
}

// remove ".tmp" from the filename. This is added when the file was not fully downloaded on the user's phone.
$filename = str_replace('.tmp', '', $filename);

// The filename is part of the Download URL. One would say, that by checking against all stored download URLs, we can see if we already have the submitted file.
// However, as we only store download URLs of the latest builds, it is impossible to see if an older-submitted update file has already been added to the app.
// Therefore, check using a possible list of OTA version numbers if the submitted file is already in the app's database.
// If so, the submission will not be shown to the contributors and administrators of the app.
$possibleOtaVersionNumbers = guessOTAVersionFromFilename($filename);

$alreadyExistingOtaVersion = null;

$database = connectToDatabase();

if (!empty($possibleOtaVersionNumbers)) {
    $allVersionNumbers = $database
        ->query('SELECT DISTINCT(parent_version_number) FROM update_data UNION SELECT DISTINCT(ota_version_number) FROM update_data')
        ->fetchAll(PDO::FETCH_COLUMN);

    foreach ($possibleOtaVersionNumbers as $possibleOtaVersionNumber) {
        if (in_array($possibleOtaVersionNumber, $allVersionNumbers)) {
            $alreadyExistingOtaVersion = $possibleOtaVersionNumber;
            break;
        }
    }

    unset($allVersionNumbers);
}

unset($possibleOtaVersionNumbers);

// Check if this file name has been submitted before
$query = $database->prepare("SELECT COUNT(*) as count FROM submitted_update_file where `name` = :filename");
$query->bindParam(':filename', $filename);
$query->execute();
$timesSubmittedBefore = $query->fetch(PDO::FETCH_ASSOC)['count'];
unset($query);
if ($timesSubmittedBefore == 0) {
    // If it has never been submitted before, create a new entry for it.
    $query = $database->prepare("INSERT INTO submitted_update_file(`name`, ota_version_number, times_submitted) VALUES (:filename, :ota_version_number, 1)");

} else {
    // Else, increment the submit count of the existing entry.
    $query = $database->prepare("UPDATE submitted_update_file SET times_submitted = times_submitted + 1, ota_version_number = :ota_version_number where `name` = :filename");
}

$query->bindParam(':filename', $filename);
$query->bindParam(':ota_version_number', $alreadyExistingOtaVersion); // null when not exists
$query->execute();

unset($json);
unset($filename);
unset($previouslySubmittedUpdateFileQuery);
unset($alreadyExistingOtaVersion);
unset($database);
unset($timesSubmittedBefore);

echo json_encode(array("success" => $query->rowCount() > 0, "error_message" => $query->rowCount() === 0 ? 'Error storing submitted update file' : null));

unset($query);