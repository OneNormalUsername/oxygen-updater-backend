<!DOCTYPE html>
<html>
<head>
    <?php
    require_once 'base.php';

    // Localize the page.
    $lang_ = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : 'en';
    require_once getTextFileToInclude($lang_, isset($_GET['lang']) ? $_GET['lang'] : null);

    // Theme: Light, Dark or empty (empty only on app versions 2.7.6 and below)
    $uri = $_SERVER["REQUEST_URI"];
    $theme = $_GET["theme"];

    // HTML Purifier prevents XSS attacks.
    $purifier = initHtmlPurifier();

    // Database connection.
    $database = connectToDatabase();

    // FAQ Categories.
    $faqCategoriesQuery = $database->query('SELECT id, dutch_category_name, english_category_name, french_category_name FROM faq_category WHERE enabled = 1 ORDER BY position');
    $db_faqCategories = $faqCategoriesQuery->fetchAll(PDO::FETCH_ASSOC);

    // FAQ Items, grouped by FAQ Category.
    $db_faqItems = array();

    foreach ($db_faqCategories as $db_faqCategory) {
        $faqItemsQuery = $database->prepare('SELECT dutch_title, english_title, french_title, dutch_body, english_body, french_body, important FROM faq_item WHERE faq_category_id = :faq_category_id AND enabled = 1 ORDER BY position');
        $faqItemsQuery->bindParam(':faq_category_id', $db_faqCategory['id']);
        $faqItemsQuery->execute();
        $db_faqItems[$db_faqCategory['id']] = $faqItemsQuery->fetchAll(PDO::FETCH_ASSOC);
    }

    ?>

    <!-- Description and keywords -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="View all Frequently Asked Questions about the Oxygen Updater app">
    <meta name="keywords" content="FAQ,Oxygen,OnePlus,OxygenOS,OS,Android,AndroidOS,System,Update,Systemupdate,OTA,Flash,Download,Faster,App">
    <meta name="author" content="Arjan Vlek">

    <!-- Page title -->
    <title><?php text('FAQ_PAGE_TITLE') ?></title>

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="57x57" href="/img/favicon/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/img/favicon/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/img/favicon/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/img/favicon/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/img/favicon/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/img/favicon/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/img/favicon/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/img/favicon/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/img/favicon/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="/img/favicon/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/img/favicon/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/img/favicon/favicon-16x16.png">
    <link rel="manifest" href="/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/img/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <!-- Style sheets -->
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
          crossorigin="anonymous">

    <?php if(strpos($uri, 'inappfaq/Light/') !== FALSE || strpos($uri, 'inappfaq/Dark/') !== FALSE) { ?>
        <link rel="stylesheet" href="../../css/style.css">
        <link rel="stylesheet" href="../../css/faq.css">

    <?php } else if(strpos($uri, 'inappfaq/') !== FALSE) { ?>
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="../css/faq.css">

    <?php } else { ?>
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/faq.css">
    <?php } ?>

    <!-- Javascript -->
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"
            integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="
            crossorigin="anonymous"></script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
            crossorigin="anonymous"></script>

    <?php if($theme === 'Dark') {?>
        <style>
            body {
                background-color: #121212;
                color: white;
            }

            a:link:not(.font-white) {
                color: #f43b46 !important;
            }

            a:visited {
                background-color: #f43b46;
            }

            .panel {
                border-color: #f43b46 !important;
            }

            .panel-body {
                background-color: #1d1d1d;
                border-top-color: #1d1d1d !important;
            }

            .panel-heading {
                background-color: #f43b46 !important;
            }
        </style>
    <?php }?>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

<!-- Explanation of important items symbol -->
<!-- <div class="container">
    <div class="row">
        <div class="col-xs=12">
            <h5 class="text-center"><?php text('FAQ_IMPORTANT_EXPL_1') ?><span class="font-red glyphicon glyphicon-exclamation-sign"></span><?php text('FAQ_IMPORTANT_EXPL_2') ?></h5>
        </div>
    </div>
</div>
-->
<!-- FAQ items -->
<div class="container" style="margin-top: 0 !important">
    <div class="row">
        <div class="col-lg-12">
            <div class="panel-group" id="accordion">
                <?php

                $collapseId = 0;

                foreach ($db_faqCategories as $faqCategory) {

                    // Print category name
                    echo "<div class='faqHeader'>" . $purifier->purify($faqCategory[retrieveText('FAQ_CATEGORY_NAME_DB_COLUMN')]) . "</div>";

                    // Print items
                    foreach ($db_faqItems[$faqCategory['id']] as $faqItem) {
                        $importantCssClass = $faqItem['important'] == 1 ? ' icon-important' : '';
                        $collapseId++;

                        echo "
                            <div class='panel panel-default'>
                                <div class='panel-heading collapsed' data-toggle='collapse' data-parent='#accordion' href='#collapse" . $collapseId . "'>
                                    <span class='panel-title'>       
                                        <table class='table-borderless faq-table'>
                                            <tr>
                                                <td>
                                                    <button class='btn-text glyphicon pull-right" . $importantCssClass . "'></button>
    
                                                </td>
                                                <td>
                                                    <button class='btn-text glyphicon toggle-glyphicon pull-right collapsed' data-toggle='collapse' href='#collapse" . $collapseId . "'></button>
                                                </td>
                                            </tr>
                                        </table>
                                        <a class='accordion-toggle word-break font-white' href='#collapse" . $collapseId . "'>" . $purifier->purify($faqItem[retrieveText('FAQ_ITEM_TITLE_DB_COLUMN')]) . "</a>
                                    </span>
                                </div>
                                <div id='collapse" . $collapseId . "' class='panel-collapse collapse'>
                                    <div class='panel-body'>
                                        <p>" . nl2br($purifier->purify($faqItem[retrieveText('FAQ_ITEM_BODY_DB_COLUMN')])) . "</p>
                                    </div>
                                </div>
                            </div>";
                    }
                }?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
