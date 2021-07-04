<?php

require_once 'base.php';

// HTML Purifier prevents XSS attacks.
$purifier = initHtmlPurifier();

$db = connectToDatabase();

// Matches stable OOS update version numbers
$stableRegex = '/(\d+\.\d+(\.\d+)*)$/';

// Matches Open Beta OOS update version numbers
$betaRegex = '/(open|beta)_(\w+)$/';  // \w, because sometimes a date string is appended to the version number

/*
 * Determine current OS versions in the database.
 */

$currentVersionsInDatabase = array();

$devices = $db->query("select id, name from device where enabled = true order by name")->fetchAll(PDO::FETCH_ASSOC);

foreach($devices as $device) {
    $updateMethodsForDeviceQuery = $db->prepare("select um.id, um.english_name from update_method um join device_update_method dum on dum.update_method_id = um.id where dum.device_id = :device_id order by um.english_name");
    $updateMethodsForDeviceQuery->bindValue(':device_id', $device['id']);
    $updateMethodsForDeviceQuery->execute();
    $updateMethods = $updateMethodsForDeviceQuery->fetchAll(PDO::FETCH_ASSOC);

    foreach($updateMethods as $updateMethod) {
        $mostRecentUpdateDataQuery = $db->prepare('select id, version_number, description from update_data where device_id = :device_id and update_method_id = :update_method_id and is_latest_version = true limit 1');
        $mostRecentUpdateDataQuery->bindValue(':device_id', $device['id']);
        $mostRecentUpdateDataQuery->bindValue(':update_method_id', $updateMethod['id']);
        $mostRecentUpdateDataQuery->execute();

        $mostRecentUpdateData = $mostRecentUpdateDataQuery->fetch(PDO::FETCH_ASSOC);

        if (empty($mostRecentUpdateData)) {
            continue;
        }

        $firstLineOfUpdateDescription = strtok($mostRecentUpdateData['description'], "\n");
        $firstLineOfUpdateDescriptionLowercase = strtolower($firstLineOfUpdateDescription);

        $versionNumber = 'Oxygen OS System Update';

        // If not formattable, return version_number prefixed with "V."
        if (strpos($firstLineOfUpdateDescription, '#') === FALSE && !empty($mostRecentUpdateData['version_number'])) {
            $versionNumber = 'V. ' . $mostRecentUpdateData['version_number'];
        } else {
            $regexMatches = array();
            if (preg_match($betaRegex, $firstLineOfUpdateDescriptionLowercase, $regexMatches) === 1) {
                $versionNumber = 'Open Beta ' . $regexMatches[2];
            } else if (preg_match($stableRegex, $firstLineOfUpdateDescriptionLowercase, $regexMatches) === 1) {
                $versionNumber = $regexMatches[0];
            } else {
                $versionNumber = str_replace('#', '', $firstLineOfUpdateDescription);
            }
        }

        $deviceAndUpdateMethodName = $device['name'] . ' - ' . $updateMethod['english_name'];
        $currentVersionsInDatabase[$deviceAndUpdateMethodName] = $versionNumber;
    }
}

$db = null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Oxygen Updater - OxygenOS versions in the app</title>

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="180x180" href="/img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/img/favicon/android-chrome-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <link rel="mask-icon" href="/img/favicon/safari-pinned-tab.svg" color="#f50514">
    <meta name="msapplication-TileColor" content="#f50514">
    <meta name="msapplication-TileImage" content="/img/favicon/mstile-144x144.png">
    <meta name="theme-color" content="#f50514">

    <!-- Custom font -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Lato" >

    <!-- Style sheets -->
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
          crossorigin="anonymous">

    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/home.css">

    <style>
        td {
            padding-left: 15px; padding-right: 15px;
        }
    </style>
</head>

<body>

<!-- Latest OS versions widget -->
<div class="row" style="margin-top: 25px;">
    <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h2 style="font-weight: bold;" class="panel-title">Latest OxygenOS versions available in the app</h2>
            </div>
            <div class="panel-body" style="padding: 0">
                <table style="margin-bottom: 0; display: table; padding-left: 15px; padding-right: 15px;" class="table table-condensed table-striped">
                    <thead>
                    <tr style="font-weight: bold;">
                        <td style="padding-left: 15px;" width="60%">Device / Update method</td>
                        <td style="padding-right: 15px;" width="40%">OxygenOS version</td>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach($currentVersionsInDatabase as $item => $version) {
                        echo '
                    <tr>
                        <td style="padding-left: 15px;">' . $purifier->purify($item) . '</td>
                        <td style="padding-right: 15px;">' . $purifier->purify($version) . '</td>
                    </tr>
                    ';
                    }

                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>

</html>
