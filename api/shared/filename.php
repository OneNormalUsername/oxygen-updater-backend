<?php

/**
 * Returns the file name out of an URL. This is the part after the last slash, without any query parameters.
 * @param $downloadUrl string URL to get the filename for
 * @return string File name to use for this URL
 */
function getFilename($downloadUrl) {
    // Get everything after the last slash of the URL.
    $filenameParts = array();
    preg_match('([^/]+$)', $downloadUrl, $filenameParts);
    $filename = $filenameParts[0];

    // If the last part after the slash has query parameters after the filename (like on file hosting sites), remove them.
    if (strpos($filename, '?') !== FALSE) {
        $filenameParts = explode('?', $filename);
        $filename = $filenameParts[0];
    }

    return $filename;
}

function guessOTAVersionFromFilename($filename) {
    // OP5T full: OnePlus5TOxygen_43_OTA_047_all_1902221932_d08e3bef8111.zip
    // OP5T incr: OnePlus5TOxygen_43_OTA_047-048_patch_1904191530_e75889ee94bc4.zip
    // Groups:
    // 1: OnePlus device (eg 5T). Not present on OPX.
    // 2: OTA framework version (eg 43)
    // 3: update revision number (eg 028)
    // 4: update build date (eg 1902221932)
    $regex = '/^OnePlus([A-Za-z0-9]*)Oxygen(?:A?)_([0-9]{2})_OTA_(?:[0-9]*-?)([0-9]{3})_(?:all|patch)_([0-9]*)_(?:.*).zip$/';
    $matches = array();

    preg_match($regex, $filename, $matches);
    unset($regex);

    if (count($matches) === 5) {
        $results = array();

        for ($i = 65; $i <= 90; $i++) { // letters 'A' to 'Z'
            array_push($results,
                sprintf(
                    'OnePlus%sOxygen_%d.%s.%d_GLO_%s_%d',
                    $matches[1],
                    intval($matches[2]),
                    chr($i),
                    intval($matches[3]), // framework version in 2 decimals, e.g. 43).
                    $matches[3], // framework version in 3 numbers, e.g. 043 instead of 43).
                    intval($matches[4])
                )
            );
        }

        unset($matches);
        return $results;
    } else {
        return [];
    }
}