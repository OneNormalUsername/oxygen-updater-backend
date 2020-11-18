<?php
include '../shared/database.php';

// Obtain all optional request parameters.
$filter = $_GET["filter"];

// Connect to the database
$database = connectToDatabase();

// Execute the query
if ($filter == "all") {
	$query = $database->query("SELECT id, name, product_names, enabled FROM device ORDER BY name");
} else {
	$query = $database->prepare("SELECT id, name, product_names, enabled FROM device WHERE enabled = :enabled ORDER BY name");
	$boolean = $filter == "disabled" ? FALSE : TRUE;
	$query->bindParam(':enabled', $boolean);
	$query->execute();
}

// Return the output as JSON
header('Content-type: application/json');
echo (json_encode($query->fetchAll(PDO::FETCH_ASSOC)));

// Disconnect from the database
$database = null;
