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
    // Try the modern scheme first. If it works, it will result in a single possible match.

    // 'Modern' scheme (with build letter):
    // OP7P full: OnePlus7ProOxygen_21.O.07_OTA_007_all_1905120542_aa37bad.zip
    // OP7P incr: OnePlus7ProOxygen_21.E.08_OTA_005-008_patch_1905150058_814a17.zip

    // Groups modern:
    // 1: OnePlus device (eg 7Pro).
    // 2: OTA framework version (eg 21)
    // 3: Build letter (eg E)
    // 4: update revision number (eg 007)
    // 5: update build date (eg 1905120542)
    $modernRegex = '/^OnePlus([A-Za-z0-9]*)Oxygen(?:A?)_([0-9]{2})\.([A-Z])\.(?:[0-9]){2}_OTA_(?:[0-9]*-?)([0-9]{3})_(?:all|patch)_([0-9]*)_(?:.*).zip$/';
    $matches = array();
    preg_match($modernRegex, $filename, $matches);
    unset($modernRegex);
    if (count($matches) === 6) {

        $updateRevision = intval($matches[4]);
        if ($updateRevision < 10) {
            $updateRevision = '0' . $updateRevision;
        } else {
            $updateRevision = '' . $updateRevision;
        }

        return array(
            sprintf(
                'OnePlus%sOxygen_%d.%s.%s_GLO_%s_%d',
                $matches[1],
                intval($matches[2]),
                $matches[3],
                $updateRevision, // update revision in 2 decimals, e.g. 43).
                $matches[4], // update revision in 3 numbers, e.g. 043 instead of 43).
                intval($matches[5])
            )
        );
    }

    // If not matched, try legacy file scheme. This will result in 26 possible matches, as we do not know the 'build letter' of the file.

    // 'Legacy' file scheme (without build letter):
    // OP5T full: OnePlus5TOxygen_43_OTA_047_all_1902221932_d08e3bef8111.zip
    // OP5T incr: OnePlus5TOxygen_43_OTA_047-048_patch_1904191530_e75889ee94bc4.zip

    // Groups legacy:
    // 1: OnePlus device (eg 5T). Not present on OPX.
    // 2: OTA framework version (eg 43)
    // 3: update revision number (eg 028)
    // 4: update build date (eg 1902221932)
    $legacyRegex = '/^OnePlus([A-Za-z0-9]*)Oxygen(?:A?)_([0-9]{2})_OTA_(?:[0-9]*-?)([0-9]{3})_(?:all|patch)_([0-9]*)_(?:.*).zip$/';
    $matches = array();

    preg_match($legacyRegex, $filename, $matches);
    unset($legacyRegex);

    if (count($matches) === 5) {
        $results = array();

        $updateRevision = intval($matches[3]);
        if ($updateRevision < 10) {
            $updateRevision = '0' . $updateRevision;
        } else {
            $updateRevision = '' . $updateRevision;
        }

        for ($i = 65; $i <= 90; $i++) { // add a possible match for all letters 'A' to 'Z'
            array_push($results,
                sprintf(
                    'OnePlus%sOxygen_%d.%s.%s_GLO_%s_%d',
                    $matches[1],
                    intval($matches[2]),
                    chr($i),
                    $updateRevision, // update revision in 2 decimals, e.g. 43).
                    $matches[3], // update revision in 3 numbers, e.g. 043 instead of 43).
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