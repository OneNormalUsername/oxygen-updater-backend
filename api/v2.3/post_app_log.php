<?php

// Set the return type to JSON.
header('Content-type: application/json');

// If this class is not accessed over POST or if it is requested from a browser, prevent executing the script.
if($_SERVER['REQUEST_METHOD'] !== 'POST' || strpos($_SERVER['HTTP_USER_AGENT'], 'Oxygen_updater_') === FALSE) {
    http_response_code(403);
    echo json_encode(array("success" => false, "error_message" => "Access Denied"));
    die();
}

echo(json_encode(array("success" => true)));
