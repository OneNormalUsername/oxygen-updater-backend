<?php

require_once 'vendor/autoload.php';
require_once 'texts.generic.php';


/**
 * Initialize the translation system by providing the appropriate text file.
 * @param $browserLanguage string Language of the web browser
 * @param $requestLanguage string Language of the web request, or null if not supplied
 * @return string Text file to import for getting translations.
 */
function initText($browserLanguage, $requestLanguage = null) {
    if ($requestLanguage != null) {
        // Returns the name of the to-be-loaded translations file based on the request param language.
        if ($requestLanguage == 'nl') {
            return 'texts.nl.php';
        } else if ($requestLanguage == 'fr') {
            return 'texts.fr.php';
        } else {
            return 'texts.en.php';
        }
    } else {
        // Returns the name of the to-be-loaded translations file based on the web browser language.
        if ($browserLanguage == 'nl' || $browserLanguage =='be') {
            return 'texts.nl.php';
        } else if ($browserLanguage == 'fr') {
            return 'texts.fr.php';
        } else {
            return 'texts.en.php';
        }
    }
}

/**
 * Prints a text value from the translations table.
 * @param $key string to lookup the texts in the translations table.
 */
function text($key) {
    echo retrieveText($key);
}

/**
 * Returns a text value from the translations table.
 * @param $key string to lookup the texts in the translations table.
 * @return string Text if found, or error text if not found.
 */
function retrieveText($key) {
    global $contents;

    if(isset($contents[$key])) {
        return $contents[$key];
    } else {
        return "<i style='color:red; text-decoration: line-through;'>" . $key . "</i>";
    }
}

/**Initialize HTML Purifier to prevent XSS.
 * @return HTMLPurifier Initialized HTML Purifier
 */
function initHtmlPurifier() {

    // Html Purifier to prevent XSS.
    $config = HTMLPurifier_Config::createDefault();
    $config->set('HTML.Allowed', 'a[href],i,b,p[class],span[class]');
    return new HTMLPurifier($config);
}

/**
 * Get a variable from the environment, and fall back to a default value if it isn't set.
 * @param $variableName string Variable name to lookup in the Apache environment.
 * @param $defaultValue string Variable name to fallback to if the variable is not present.
 * @return array|false|string Value if it exists, or defaultValue if it doesn't exist
 */
function getenvOrDefault($variableName, $defaultValue) {
    $value = getenv($variableName);
    if ($value && $value != '1') {
        return $value;
    } else {
        return $defaultValue;
    }
}

/**Connect to the Oxygen Updater database.
 * @return PDO Database connection
 */
function connectToDatabase()
{
    try {
        $databaseUsername = getenv('DATABASE_USER');
        $databasePassword = getenv('DATABASE_PASS');
        $databaseHost = getenv('DATABASE_HOST');
        $databaseName = getenv('DATABASE_NAME');
        $database = new PDO('mysql:host=' . $databaseHost . ';dbname=' . $databaseName . '', $databaseUsername, $databasePassword);
        $database->query('SET CHARACTER SET utf8');
        return $database;
    } catch (Exception $e) {
        error_log($e->getTraceAsString());
        return null;
    }
}
