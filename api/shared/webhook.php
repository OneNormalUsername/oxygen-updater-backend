<?php

function make_webhook_call($webhookUrl, $text, ...$embeds) {
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
    $body->content = $text;

    if (isset($embeds) && is_array($embeds)) {
        $body->embeds = $embeds;
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

function make_webhook_embed($authorName, $authorUrl, $contentTitle, $contentDescription, $footerText, $contentUrl, $thumbnailUrl) {
    //------------------------------
    // Creates a webhook Embed object
    //------------------------------
    $result = new stdClass();

    if ($authorName && $authorUrl) {
        $result->author = new stdClass();
        $result->author->name = $authorName;
        $result->author->url = $authorUrl;
    }

    $result->title = $contentTitle;
    $result->description = strlen($contentDescription < 100) ? $contentDescription : (substr($contentDescription, 0, 100) . ' ...'); // Limit content text to 100 characters

    if ($contentUrl) {
        $result->url = $contentUrl;
    }

    if ($thumbnailUrl) {
        $result->thumbnail = new stdClass();
        $result->thumbnail->url = $thumbnailUrl;
    }

    if ($footerText) {
        $result->footer = new stdClass();
        $result->footer->text = $footerText;
    }

    return $result;
}