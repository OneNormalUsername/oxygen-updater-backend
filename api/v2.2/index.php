<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title></title>
</head>
<body>
<?php
    include '../shared/database.php';

    $database = connectToDatabase();

    $query = $database->query("SELECT status FROM server_status");
    $result = $query->fetch(PDO::FETCH_ASSOC);

    echo "API version: 2.2 <br/>";
    echo "API status: ".$result["status"]." <br/>";
?>

</body>
</html>
