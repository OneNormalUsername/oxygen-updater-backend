<?php

function make_webhook_call(
    $webhookUrl,
    $content,
    ...$embeds
) {
    //------------------------------
    // Create webhook headers
    //------------------------------
    $headers = array(
        'Content-Type: application/json'
    );

    //------------------------------
    // Create webhook POST body
    //------------------------------
    $body = new stdClass();
    if ($content) {
        $body->content = strlen($content <= 2000) ? $content : (substr($content, 0, 1996) . ' ...'); // Limit content to 2000 characters
    }

    if (isset($embeds) && is_array($embeds)) {
        $embeds = array_filter($embeds);

        if ($embeds) {
            // `array_filter` does not reindex the array, so we must wrap it in `array_values`
            // e.g. If the original array was (0:null, 1:object, 2:null, 3:object),
            // `array_filter` would return (1:object, 3:object) which is obviously stupid af. But that's PHP for you.
            $body->embeds = array_values($embeds);
        }
    }

    //------------------------------
    // Initialize curl handle
    //------------------------------
    $ch = curl_init($webhookUrl);

    //------------------------------
    // Set request method to POST
    //------------------------------
    curl_setopt($ch, CURLOPT_POST, true);

    //------------------------------
    // Set our custom headers
    //------------------------------
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    //------------------------------
    // Get the response back as
    // string instead of printing it
    //------------------------------
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //------------------------------
    // Set post data as JSON
    //------------------------------
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));

    //------------------------------
    // Actually make the call!
    //------------------------------
    curl_exec($ch);

    //------------------------------
    // Error? Log it!
    //------------------------------
    if (curl_errno($ch)) {
        error_log("Error when making webhook call to <" . $webhookUrl . ">: "  . curl_error($ch));
    }
    //------------------------------
    // Close curl handle
    //------------------------------
    curl_close($ch);
}

function make_webhook_embed(
    $author,
    $title,
    $titleUrl,
    $description,
    $footer,
    $thumbnailUrl = null,
    $color = null,
    ...$fields
) {
    //------------------------------
    // Creates a webhook Embed object
    //------------------------------
    $embed = new stdClass();

    if ($author) {
        $embed->author = $author;
    }

    $embed->title = strlen($title <= 256) ? $title : (substr($title, 0, 252) . ' ...'); // Limit title to 256 characters
    $embed->description = strlen($description <= 2048) ? $description : (substr($description, 0, 2044) . ' ...'); // Limit description to 2048 characters

    if ($titleUrl) {
        $embed->url = $titleUrl;
    }

    if ($thumbnailUrl) {
        $embed->thumbnail = new stdClass();
        $embed->thumbnail->url = $thumbnailUrl;
    }

    if ($footer) {
        $embed->footer = $footer;
    }

    if ($color) {
        // Discord accepts only integer colours
        if (!is_numeric($color)) {
            // Assume $color was hex, and convert to int
            $color = hexdec($color);
        }

        $embed->color = $color;
    }

    if (isset($fields) && is_array($fields)) {
        $fields = array_filter($fields);

        if ($fields) {
            // `array_filter` does not reindex the array, so we must wrap it in `array_values`
            // e.g. If the original array was (0:null, 1:object, 2:null, 3:object),
            // `array_filter` would return (1:object, 3:object) which is obviously stupid af. But that's PHP for you.
            $embed->fields = array_values($fields);
        }
    }

    return $embed;
}

function make_webhook_author(
    $name = 'Oxygen Updater',
    $url = 'https://oxygenupdater.com',
    $iconUrl = 'https://github.com/oxygen-updater.png'
) {
    if ($name) {
        $author = new stdClass();
        $author->name = strlen($name <= 256) ? $name : (substr($name, 0, 252) . ' ...'); // Limit author name to 256 characters

        if ($url) {
            $author->url = $url;
        }

        if ($iconUrl) {
            $author->icon_url = $iconUrl;
        }

        return $author;
    }

    return null;
}

function make_webhook_footer(
    $text,
    $iconUrl = null
) {
    if ($text) {
        $footer = new stdClass();
        $footer->text = strlen($text <= 2048) ? $text : (substr($text, 0, 2044) . ' ...'); // Limit footer text to 2048 characters

        if ($iconUrl) {
            $footer->icon_url = $iconUrl;
        }

        return $footer;
    }

    return null;
}

function make_webhook_field(
    $name,
    $value,
    $inline = false
) {
    if ($name && $value) {
        //------------------------------
        // Creates a webhook Field object
        //------------------------------
        $field = new stdClass();

        $field->name = strlen($name <= 256) ? $name : (substr($name, 0, 252) . ' ...'); // Limit name to 256 characters
        $field->value = strlen($value <= 1024) ? $value : (substr($value, 0, 1020) . ' ...'); // Limit value to 1024 characters
        $field->inline = $inline;

        return $field;
    }

    return null;
}
