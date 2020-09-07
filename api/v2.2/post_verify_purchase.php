<?php
include '../shared/database.php';

header('Content-Type: application/json');

// If this class is not accessed over POST or if it is requested from a browser, prevent executing the script.
if($_SERVER['REQUEST_METHOD'] !== 'POST' || strpos($_SERVER['HTTP_USER_AGENT'], 'Oxygen_updater_') === FALSE) {
    http_response_code(403);
    echo json_encode(array("success" => false, "error_message" => "Access Denied"));
    die();
}

//Get the JSON from the request body
$json = json_decode(file_get_contents('php://input'), true);

//Get required request parameters from the JSON body.
$orderId = $json['orderId'];
$packageName = $json['packageName'];
$productId = $json['productId'];
$purchaseType = $json['purchaseType'];
$itemType = $json['itemType'];
$purchaseState = $json['purchaseState'];
$developerPayload = $json['developerPayload'];
$purchaseToken = array_key_exists('token', $json) ? $json['token'] : $json['purchaseToken'];
$purchaseSignature = array_key_exists('purchaseSignature', $json) ? $json['purchaseSignature'] : '';
$purchaseTime = intval($json['purchaseTime']);
$amount = array_key_exists('amount', $json) ? $json['amount'] : '_____';
$autoRenewing = array_key_exists('autoRenewing', $json) ? boolval($json['autoRenewing']) : false;

// Purchases MUST contain an order id containing "GPA". Otherwise the purchase is not valid (and won't show up in Play Console either).
if(strpos($orderId, 'GPA') === FALSE) {
    $purchaseState = 3; // Mark the invalid purchase as being cancelled. I still want to save it though, so I can see how many invalid / hacked purchases are being made.
}


//Establish a database connection
$database = connectToDatabase();
$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Store the purchase details in the database.
$query = $database->prepare("INSERT INTO purchase(order_id, package_name, product_id, purchase_type, item_type, purchase_state, developer_payload, purchase_token, purchase_signature, purchase_time, auto_renewing, amount) VALUES (:order_id, :package_name, :product_id, :purchase_type, :item_type, :purchase_state, :developer_payload, :purchase_token, :purchase_signature, :purchase_time, :auto_renewing, :amount)");
$query->bindParam(':order_id', $orderId);
$query->bindParam(':package_name', $packageName);
$query->bindParam(':product_id', $productId);
$query->bindParam(':purchase_type', $purchaseType);
$query->bindParam(':item_type', $itemType);
$query->bindParam(':purchase_state', $purchaseState);
$query->bindParam(':developer_payload', $developerPayload);
$query->bindParam(':purchase_token', $purchaseToken);
$query->bindParam(':purchase_signature', $purchaseSignature);
$query->bindParam(':purchase_time', $purchaseTime, PDO::PARAM_INT);
$query->bindParam(':auto_renewing', $autoRenewing, PDO::PARAM_BOOL);
$query->bindParam(':amount', $amount);

try {
    $query->execute();
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array("success" => false, "error_message" => "Unable to store purchase details into the database. Error message: " . $e->getMessage()));
    die();
}

// If the purchase is not valid, don't grant the ad-free permission to the user. Server validation has failed...
if(strpos($orderId, 'GPA') === FALSE) {
    echo json_encode(array("success" => false, "error_message" => "Purchase is not a valid Google Play Apps purchase (GPA)."));
    die();
}

// Return a success message if the purchase details have been inserted successfully.
if ($query->rowCount() > 0) {
    echo(json_encode(array("success" => true))); // If the purchase needs to be validated in the future, add the outcome of the validation here...
} else {
    http_response_code(500);
    echo(json_encode(array("success" => false, "error_message" => "Unable to store purchase details into the database.")));
}

// Disconnect from the database.
$database = null;
