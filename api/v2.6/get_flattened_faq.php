<?php
include '../shared/database.php';

// Connect to the database
$database = connectToDatabase();

// Execute the query
$faqCategoriesQuery = $database->query("SELECT id, dutch_category_name as dutch_title, english_category_name as english_title, french_category_name as french_title, 'category' as type FROM faq_category WHERE enabled = 1 ORDER BY position");
$faqCategories = $faqCategoriesQuery->fetchAll(PDO::FETCH_ASSOC);

$faqResults = array();

foreach ($faqCategories as $faqCategory) {
    // FAQ Items, grouped by FAQ Category.
    $faqItemsQuery = $database->prepare("SELECT id, dutch_title, english_title, french_title, dutch_body, english_body, french_body, important, 'item' as type FROM faq_item WHERE faq_category_id = :faq_category_id AND enabled = 1 ORDER BY position");
    $faqItemsQuery->bindParam(':faq_category_id', $faqCategory['id']);
    $faqItemsQuery->execute();
    $faqResults[] = $faqCategory;
    $faqResults = array_merge($faqResults, $faqItemsQuery->fetchAll(PDO::FETCH_ASSOC));
}

// Return the output as JSON
header('Content-type: application/json');
echo (json_encode($faqResults));

// Disconnect from the database
$database = null;
