<!DOCTYPE html>
<html>
<head>
    <?php
    require_once 'base.php';

    // Localize the page.
    $lang_ = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2) : 'en';
    require_once getTextFileToInclude($lang_, isset($_GET['lang']) ? $_GET['lang'] : null);
    ?>

    <!-- Description and keywords -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="View information about the team behind the Oxygen Updater app">
    <meta name="keywords" content="Team,Oxygen,OnePlus,OxygenOS,OS,Android,AndroidOS,System,Update,Systemupdate,OTA,Flash,Download,Faster,App">
    <meta name="author" content="Adhiraj Singh Chauhan">

    <!-- Page title -->
    <title><?php text('TEAM_PAGE_TITLE') ?></title>

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

    <!-- Stylesheets -->
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
          crossorigin="anonymous">

    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/faq.css">

    <!-- Javascript -->
    <script src="https://code.jquery.com/jquery-1.12.4.min.js"
            integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="
            crossorigin="anonymous"></script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
            integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
            crossorigin="anonymous"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<!-- Top navigation bar -->
<nav>
    <div class="container-fluid text-center">
        <!-- Expand navigation bar icon (≡) -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-navbar-collapse-1">

                <span class="sr-only"><?php text("TOGGLE_NAVIGATION") ?></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <!-- Logo -->
            <img src="/img/app_icon_small-min.png" class="pull-left logo" alt="Logo">

            <!-- App name -->
            <a class="font-white navbar-brand" href="/"><?php text("APP_NAME") ?></a>
        </div>

        <!-- Navigation bar items (Download App and FAQ) -->
        <div class="collapse navbar-collapse" id="bs-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li>
                    <a class="font-white" href="https://play.google.com/store/apps/details?id=com.arjanvlek.oxygenupdater<?php text('DOWNLOAD_APP_URL_SUFFIX') ?>">
                        <?php text("DOWNLOAD_APP") ?>
                    </a>
                </li>
                <li>
                    <a class="font-white" href="/faq"><?php text("FAQ") ?></a>
                </li>
                <li>
                    <a class="font-white" href="/legal"><?php text("PRIVACY_POLICY") ?></a>
                </li>
            </ul>
        </div>
        <!-- /.navbar-collapse -->
    </div>
    <!-- /.container-fluid -->
</nav>

<!-- Title -->
<div class="grey-box">
    <h2 class="text-center"><?php text('TEAM_PAGE_SUBTITLE')?></h2>
</div>

<!-- "Coming soon" banner -->
<div class="container" style="margin-top: -30px !important">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-10 col-lg-8 col-md-offset-1 col-lg-offset-2" style="text-align: center;">
            <!-- https://pixabay.com/vectors/coming-soon-sign-board-3008776/ -->
            <img src="/img/coming_soon.png" style="width: 80%; max-height: 500px;"/>
        </div>
    </div>
</div>

<hr>

<!-- Info about app -->
<div class="container well">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="text-center">
                <address>
                    <strong><?php text('APP_NAME') ?></strong><br>
                    <?php text('FOOTER_DESCRIPTION') ?>
                </address>
                <address>
                    <?php text('FOOTER_DEVELOPED_BY') ?>
                    <strong><?php text('AUTHOR_NAME') ?></strong><br>
                </address>
            </div>
        </div>
    </div>
</div>

<!-- Legal notice -->
<footer class="text-center">
    <div class="container">
        <div class="row">
            <div class="col-xs-12">
                <p><?php text('FOOTER_LEGAL_ONEPLUS') ?></p>
                <p><?php text('FOOTER_LEGAL_GOOGLE') ?></p>
            </div>
        </div>
    </div>
</footer>
</body>
</html>
