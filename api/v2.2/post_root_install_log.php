<?php
include '../shared/DatabaseConnector.php';
include 'model/UpdateInstallation.php';

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

try {
    $installation = new UpdateInstallation($json);
} catch (InvalidArgumentException $e) {
    error_log('Error storing update installation log: ' . $e->getMessage() . '. Input data: ' . $json);
    http_response_code(400);
    echo json_encode(array("success" => false, "error_message" => $e->getMessage()));
    die();
}

$database = (new DatabaseConnector())->connectToDb();

switch ($installation->getStatus()) {
    case "STARTED":
        $appVersion = substr($_SERVER['HTTP_USER_AGENT'], strlen('Oxygen_updater_'));
        $query = $database->prepare("INSERT INTO update_installation(installation_id, device_id, update_method_id, status, start_date, last_updated_date, start_os_version, destination_os_version, current_os_version, app_version) VALUES (:installation_id, :device_id, :update_method_id, 'STARTED', :start_date, :last_updated_date, :start_os_version, :destination_os_version, :current_os_version, :app_version)");
        $query->bindValue(':installation_id', $installation->getInstallationId());
        $query->bindValue(':device_id', $installation->getDeviceId());
        $query->bindValue(':update_method_id', $installation->getUpdateMethodId());
        $query->bindValue(':start_date', $installation->getStartDate());
        $query->bindValue(':last_updated_date', $installation->getStartDate()); // Is always the same as last updated date as we just started.
        $query->bindValue(':start_os_version', $installation->getStartOsVersion());
        $query->bindValue(':destination_os_version', $installation->getDestinationOsVersion());
        $query->bindValue(':current_os_version', $installation->getStartOsVersion()); // Is always same as current as we just started.
        $query->bindParam(':app_version', $appVersion);
        $query->execute();
        break;
    case "FINISHED":
        $query = $database->prepare("UPDATE update_installation SET status = 'FINISHED', last_updated_date = :last_updated_date, current_os_version = :current_os_version WHERE installation_id = :installation_id");
        $query->bindValue(':installation_id', $installation->getInstallationId());
        $query->bindValue(':last_updated_date', $installation->getLastUpdatedDate());
        $query->bindValue(':current_os_version', $installation->getCurrentOsVersion());
        $query->execute();
        break;
    case "FAILED":
        $query = $database->prepare("UPDATE update_installation SET status = 'FAILED', last_updated_date = :last_updated_date, current_os_version = :current_os_version, failure_reason = :failure_reason WHERE installation_id = :installation_id");
        $query->bindValue(':installation_id', $installation->getInstallationId());
        $query->bindValue(':last_updated_date', $installation->getLastUpdatedDate());
        $query->bindValue(':current_os_version', $installation->getCurrentOsVersion());
        $query->bindValue(':failure_reason', $installation->getFailureReason());
        $query->execute();
        break;
    default:
        error_log("Error storing update installation log: Invalid installation status [" . $installation->getStatus() . "]. Input data: " . $json);
        http_response_code(400);
        echo json_encode(array("success" => false, "error_message" => "Invalid installation status [" . $installation->getStatus()) . "]");
        die();
        break;
}

$json = null;
$database = null;
echo json_encode(array("success" => true, "error_message" => null));