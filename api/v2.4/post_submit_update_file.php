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
$isEuBuild = $json['isEuBuild'];

// Check if a file name has been set. If not, throw a HTTP 400 Bad Request error.
if (empty($filename)) {
    http_response_code(400);
    echo json_encode(array("success" => false, "error_message" => "Bad Request"));
    die();
}

// If the filename is invalid (i.e. not a ZIP file or doesn't contain "Oxygen"), this response
// is sent to the app to indicate that the file they submitted is invalid and thus not needed.
$invalidFileResponse = json_encode(array("success" => false, "error_message" => "E_FILE_INVALID"));

if (strpos($filename, '.zip') === FALSE) {
    echo $invalidFileResponse;
    // Additional processing (sending webhooks or inserting in DB) shouldn't be done because it's not a ZIP
    die();
}

$responseAlreadySent = false;

if (strpos($filename, 'Oxygen') === FALSE) {
    // If we reach this point it's a guarantee that it's a ZIP file, but it doesn't contain "Oxygen".
    // So, send the invalid file response...
    echo $invalidFileResponse;
    // ...and make sure another response isn't sent (other `echo` calls are wrapped with a simple if-false check)
    $responseAlreadySent = true;
    // ... but don't `die()` here, because even though this file doesn't contain "Oxygen", it still is a ZIP.
    // We need to be aware of any filename changes OnePlus may have implemented (e.g. 8-series Open Beta 11
    // on 17th June 2021 switched to ota-manual-sg.allawnofs.com where files are named as hyphened GUIDs).
}

// remove temporary suffixes from the filename. These may be added when the file is not fully downloaded on the user's phone at submission time.
$filename = str_replace(['~', '.tmp', '.tar', '.jar', '.gz', '.crdownload', '(1)', '(2)', ' '],'',$filename);

// The filename is part of the Download URL. One would say, that by checking against all stored download URLs, we can see if we already have the submitted file.
// However, as we only store download URLs of the latest builds, it is impossible to see if an older-submitted update file has already been added to the app.
// Therefore, check using a possible list of OTA version numbers if the submitted file is already in the app's database.
// If so, the submission will not be shown to the contributors and administrators of the app.
$alreadyExistingOtaVersion = null;
$possibleOtaVersionNumbers = guessOTAVersionFromFilename($filename);

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
}

// Check if this file name has been submitted before
$query = $database->prepare("SELECT COUNT(*) as count FROM submitted_update_file where `name` = :filename");
$query->bindParam(':filename', $filename);
$query->execute();
$timesSubmittedBefore = $query->fetch(PDO::FETCH_ASSOC)['count'];

if ($timesSubmittedBefore == 0) {
    // If it has never been submitted before, create a new entry for it.
    $query = $database->prepare("INSERT INTO submitted_update_file(`name`, ota_version_number, times_submitted) VALUES (:filename, :ota_version_number, 1)");

    // If the never-submitted-before file is a valid OxygenOS file, notify the #contributors Discord channel.
    // We also do this for already-matched files, because otherwise files submitted after the update was initially added (e.g. older incremental packages) are missed.
    include '../shared/webhook.php';

    // Message author and action URL not available on GitHub.
    $authorName = getenv('SUBMITTED_UPDATE_FILE_WEBHOOK_AUTHOR_NAME');
    $messageActionUrl = getenv('SUBMITTED_UPDATE_FILE_WEBHOOK_ACTION_URL');

    $webhookField1 = make_webhook_field(
        'Probably EU Build?',
        $isEuBuild ? 'Yes' : 'No',
        true
    );

    $prefix = $isEuBuild ? 'This was submitted from a device that ran an EU build.' : '';

    $webhookEmbed = make_webhook_embed(
        make_webhook_author(),
        'New OTA filename submitted',
        $messageActionUrl,
        "$prefix
```yaml
$filename
```",
        make_webhook_footer($authorName),
        'https://cdn1.iconfinder.com/data/icons/finance-and-taxation/64/submit-document-file-send-512.png',
        '4caf50',
        $webhookField1
    );

    // webhook URL not available on GitHub to prevent abuse
    $webhookUrl = getenv('SUBMITTED_UPDATE_FILE_WEBHOOK_URL');
    make_webhook_call(
        $webhookUrl,
        null,
        $webhookEmbed
    );
} else {
    // Else, increment the submit count of the existing entry.
    $query = $database->prepare("UPDATE submitted_update_file SET times_submitted = times_submitted + 1, ota_version_number = :ota_version_number where `name` = :filename");
}

$query->bindParam(':filename', $filename);
$query->bindParam(':ota_version_number', $alreadyExistingOtaVersion); // null when not exists
$query->execute();

$success = $query->rowCount() > 0 && $alreadyExistingOtaVersion == null;
$errorMessage = $query->rowCount() === 0 ? 'Error storing submitted update file' : ($alreadyExistingOtaVersion != null ? 'E_FILE_ALREADY_IN_DB' : null);

// Disconnect from the database
unset($database);

if (!$responseAlreadySent) {
    echo json_encode(array("success" => $success, "error_message" => $errorMessage));
}
