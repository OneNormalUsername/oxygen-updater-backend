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