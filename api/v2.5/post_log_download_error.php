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

$url = $json['url'];
$filename = $json['filename'];
$version = $json['version'];
$otaVersion = $json['otaVersion'];
$httpCode = $json['httpCode'];
$httpMessage = $json['httpMessage'];
$appVersion = $json['appVersion'];
$deviceName = $json['deviceName'];

// Check if appVersion has been set. If not, throw a HTTP 400 Bad Request error.
if (empty($appVersion)) {
    http_response_code(400);
    echo json_encode(array("success" => false, "error_message" => "Bad Request"));
    die();
} else {
    // handle custom buildTypes (e.g. debug, localDebug, etc.)
    $appVersion = explode("-", $appVersion)[0];
}

if (empty($url)) {
    $url = "empty";
}

$alreadyExistingOtaVersion = null;

$database = connectToDatabase();

// Check if this URL has been submitted before
$query = $database->prepare("SELECT COUNT(*) as count FROM download_error WHERE `url` = :url AND `http_code` = :http_code");
$query->bindParam(':url', $url);
$query->bindParam(':http_code', $httpCode);
$query->execute();
$timesSeenBefore = $query->fetch(PDO::FETCH_ASSOC)['count'];

if ($timesSeenBefore == 0) {
    // If it has never been submitted before, create a new entry for it.
    $query = $database->prepare("INSERT INTO download_error(`url`, http_code, ota_version_number, times_seen) VALUES (:url, :http_code, :ota_version_number, 1)");

    // If the never-submitted-before file is a valid OxygenOS file, notify the #contributors Discord channel.
    // We also do this for already-matched files, because otherwise files submitted after the update was initially added (e.g. older incremental packages) are missed.
    include '../shared/webhook.php';

    // Message author and action URL not available on GitHub.
    $authorName = getenv('DOWNLOAD_ERROR_WEBHOOK_AUTHOR_NAME');
    $messageActionUrl = getenv('DOWNLOAD_ERROR_WEBHOOK_ACTION_URL');

    $webhookField1 = make_webhook_field(
        'HTTP Error',
        "[$httpCode $httpMessage](https://httpstatuses.com/$httpCode)",
        true
    );
    $webhookField2 = make_webhook_field(
        'App Version',
        "[$appVersion](https://github.com/oxygen-updater/oxygen-updater/releases/tag/oxygen-updater-$appVersion)",
        true
    );
    $webhookField3 = make_webhook_field(
        'Download URL',
        "$url"
    );

    $webhookEmbed = make_webhook_embed(
        make_webhook_author(),
        'New download error spotted',
        null,
        "Check the URL and fix update data on [admin portal]($messageActionUrl) if required. Relevant metadata:
```yaml
     device: $deviceName
    version: $version
ota-version: $otaVersion
   filename: $filename
```",
        make_webhook_footer($authorName),
        null,
        'f44336',
        $webhookField1, $webhookField2,
        $webhookField3
    );

    // webhook URL not available on GitHub to prevent abuse
    $webhookUrl = getenv('DOWNLOAD_ERROR_WEBHOOK_URL');
    make_webhook_call(
        $webhookUrl,
        'Spotted a new download error. This could probably mean the download link is invalid - meaning either someone made a mistake while adding update data, or OnePlus pulled/removed the file.',
        $webhookEmbed
    );
} else {
    // Else, increment the submit count of the existing entry.
    $query = $database->prepare("UPDATE download_error SET times_seen = times_seen + 1, ota_version_number = :ota_version_number WHERE `url` = :url AND http_code = :http_code");
}

$query->bindParam(':url', $url);
$query->bindParam(':http_code', $httpCode);
$query->bindParam(':ota_version_number', $otaVersion);
$query->execute();

$success = $query->rowCount() > 0;
$errorMessage = $query->rowCount() === 0 ? 'Error storing download error' : null;

// Disconnect from the database
unset($database);

echo json_encode(array("success" => $success, "error_message" => $errorMessage));
