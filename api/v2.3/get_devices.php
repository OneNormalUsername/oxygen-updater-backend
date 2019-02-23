<?php
include '../shared/database.php';

// Connect to the database
$database = connectToDatabase();

// Execute the query
$query = $database->query("SELECT id, name, product_names FROM device WHERE enabled = TRUE ORDER BY name");

// Return the output as JSON
header('Content-type: application/json');
echo (json_encode($query->fetchAll(PDO::FETCH_ASSOC)));

// Disconnect from the database
$database = null;
