<!DOCTYPE html>
<html>
<head>
    <?php
    require_once 'base.php';

    // Localize the page.
    //$httpLanguage = isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : 'en';
    require_once getTextFileToInclude(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2), (isset($_GET['lang'])? $_GET['lang'] : null));

    // HTML Purifier prevents XSS attacks.
    $purifier = initHtmlPurifier();

    // Database connection.
    $database = connectToDatabase();

    if($database != null) {
        // App version number and download size.
        $versionInfoQuery = $database->prepare("SELECT latest_app_version, app_download_size_megabytes FROM server_status");
        $versionInfoQuery->execute();
        $versionInfoResult = $versionInfoQuery->fetch(PDO::FETCH_ASSOC);

        $db_appVersionNumber = $versionInfoResult['latest_app_version'];
        $db_appDownloadSize = $versionInfoResult['app_download_size_megabytes'];

        // Supported devices.
        $devicesQuery = $database->prepare("SELECT id, name FROM device WHERE enabled = TRUE ORDER by name");
        $devicesQuery->execute();
        $db_devices = $devicesQuery->fetchAll(PDO::FETCH_ASSOC);
    }
    ?>


    <!-- Description and keywords -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Oxygen Updater is a free Android app to receive OxygenOS Over The Air (OTA) updates faster. Easy, safe and supports all OnePlus devices!">
    <meta name="keywords" content="Oxygen,OnePlus,OxygenOS,OS,Android,AndroidOS,System,Update,Systemupdate,OTA,Flash,Download,Faster,App">
    <meta name="author" content="Adhiraj Singh Chauhan">

    <!-- Facebook tags -->
    <meta property="og:title" content="Oxygen Updater - Receive OxygenOS system updates faster!">
    <meta property="og:image" content="https://oxygenupdater.com/img/app_icon-min.png">
    <meta property="og:description" content="Oxygen Updater is a free Android app to receive OxygenOS Over The Air (OTA) updates faster. Easy, safe and supports all OnePlus devices!">

    <!-- Twitter tags -->
    <meta name="twitter:card" content="app">
    <meta name="twitter:site" content="@IAmAscii">
    <meta name="twitter:description" content="Oxygen Updater is a free Android app to receive OxygenOS Over The Air (OTA) updates faster. Easy, safe and supports all OnePlus devices!">
    <meta name="twitter:app:name:googleplay" content="Oxygen Updater">
    <meta name="twitter:app:id:googleplay" content="com.arjanvlek.oxygenupdater">
    <meta name="twitter:app:url:googleplay" content="https://play.google.com/store/apps/details?id=com.arjanvlek.oxygenupdater">

    <!-- Page title -->
    <title><?php text('PAGE_TITLE') ?></title>

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

    <!-- Custom font -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Lato" >

    <!-- Style sheets -->
    <link rel="stylesheet"
          href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u"
          crossorigin="anonymous">

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/home.css">

    <!-- JavaScript -->
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
<nav id="nav">
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
            <img src="./img/app_icon_small-min.png" class="pull-left logo" alt="Logo">

            <!-- App name -->
            <a class="font-white navbar-brand" href="/"><?php text("APP_NAME") ?></a>
        </div>

        <!-- Navigation bar items (Download App and FAQ) -->
        <div class="collapse navbar-collapse" id="bs-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li>
                    <a class="font-white" href="https://play.google.com/store/apps/details?id=com.arjanvlek.oxygenupdater<?php text('DOWNLOAD_APP_URL_SUFFIX') ?>">
                        <?php text("DOWNLOAD_APP")?>
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
<div class="container-fluid">
    <!-- MOBILE VIEW -->
    <div class="row hidden-sm hidden-md hidden-lg text-center background-darker-grey">
        <div class="lighter-grey-box">
            <!-- Title -->
            <div class="feature-title"><?php text('FEATURE_TITLE') ?></div>

            <hr/>

            <!-- Description -->
            <div class="feature-text"><?php text('FEATURE_TEXT') ?></div>

            <!-- Google Play button -->
            <div id="download-button">
                <hr>
                <a href="https://play.google.com/store/apps/details?id=com.arjanvlek.oxygenupdater">
                    <img alt="<?php text('PLAY_BADGE_ALT') ?>"
                         src="<?php text('PLAY_BADGE_URL') ?>" width="200px"/>
                </a>

                <!-- Version number plus app download size -->
                <h6>
                    <span id="supported-device-list" class="font-white">
                        <?php if(isset($db_appVersionNumber) && isset($db_appDownloadSize)) echo "V " . $purifier->purify($db_appVersionNumber) . " - " . $purifier->purify($db_appDownloadSize) . " MB"; ?>
                    </span>
                </h6>

                <hr>

                <!-- Supported devices -->
                <div class="text-center font-white p-l-15 p-r-15">
                    <h4><b><?php text('SUPPORTED_DEVICES_TITLE') ?></b></h4>
                    <h5>
                        <?php
                        if(isset($db_devices)) {
                            foreach ($db_devices as $device) {

                                echo
                                    "<div class='media'>
                                        <div class='media-list'>
                                            <span class='glyphicon glyphicon-ok float-left p-l-15' aria-hidden='true'></span>
                                        </div>
                                        <div class='media-body'>
                                            <h4 class='media-heading p-r-15'> " . $purifier->purify($device["name"]) . "</h4>
                                        </div>
                                    </div>";
                            }
                        } else {
                            echo "Error establishing a database connection.";
                        }
                        ?>
                    </h5>
                </div>
            </div>
        </div>

        <!-- Mobile image slide show -->
        <div id="mobile-carousel" class="carousel slide col-sm-3">
            <div data-target="#mobile-carousel" class="cursor-hand" data-slide="next">
                <div class="carousel-inner shadow">
                    <div class="item active">
                        <img class="img-responsive feature-image" src="img/<?php text('CAROUSEL_IMAGE_TYPE') ?>/1-update-available-min.png"
                             alt="<?php text('CAROUSEL_ALT_1') ?>">
                    </div>
                    <div class="item">
                        <img class="img-responsive feature-image" src="img/<?php text('CAROUSEL_IMAGE_TYPE') ?>/2-install-guide-min.png"
                             alt="<?php text('CAROUSEL_ALT_2') ?>">
                    </div>
                    <div class="item">
                        <img class="img-responsive feature-image" src="img/<?php text('CAROUSEL_IMAGE_TYPE') ?>/3-up-to-date-min.png"
                             alt="<?php text('CAROUSEL_ALT_3') ?>">
                    </div>
                </div>
            </div>
        </div>
        <!-- End mobile view -->
    </div>

    <!-- DESKTOP VIEW -->
    <div class="row hidden-xs background-darker-grey">
        <div class="col-sm-12">
            <table class="full-width-table">
                <tr>
                    <td width="75%" class="feature-box">
                        <div class="lighter-grey-box">
                            <!-- Title (to the left) -->
                            <div class="feature-title"><?php text('FEATURE_TITLE') ?></div>

                            <hr class="hr-full-width"/>

                            <!-- Description (below title) -->
                            <div class="feature-text"><?php text('FEATURE_TEXT') ?></div>

                            <hr class="hr-full-width"/>

                            <table class="full-width-table">
                                <tr>
                                    <td width="50%">
                                        <!-- Google Play button -->
                                        <a href="https://play.google.com/store/apps/details?id=com.arjanvlek.oxygenupdater">
                                            <img class="center-div" alt="<?php text('PLAY_BADGE_ALT') ?>"
                                                 src="<?php text('PLAY_BADGE_URL') ?>" width="200px"/>
                                        </a>

                                        <!-- Version number plus app download size (to the right of Google Play button) -->
                                        <h6 class="text-center">
                                        <span class="font-white text-center">
                                            <?php if(isset($db_appVersionNumber) && isset($db_appDownloadSize)) echo "V " . $purifier->purify($db_appVersionNumber) . " - " . $purifier->purify($db_appDownloadSize) . " MB"; ?>
                                        </span>
                                        </h6>
                                    </td>
                                    <td width="50%">
                                        <!-- Supported devices -->
                                        <div class="text-center font-white p-l-15 p-r-15 desktop-supported-device-list">
                                            <h4><b><?php text('SUPPORTED_DEVICES_TITLE') ?></b></h4>
                                            <h5 style="margin-bottom: 15px; margin-top: 15px;"><?php text('SUPPORTED_DEVICES_SCROLL') ?> </h5>
                                            <h5>
                                                <?php
                                                if(isset($db_devices)) {
                                                    foreach ($db_devices as $device) {
                                                    echo
                                                        "<div class='media'>
                                                            <div class='media-list'>
                                                                <span class='glyphicon glyphicon-ok float-left p-l-15' aria-hidden='true'></span>
                                                            </div>
                                                            <div class='media-body'>
                                                                <h4 class='media-heading p-r-15'> " . $purifier->purify($device["name"]) . "</h4>
                                                            </div>
                                                        </div>";
                                                    }
                                                } else {
                                                    echo "Error establishing a database connection.";
                                                }
                                                ?>
                                            </h5>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                    <td width="25%">
                        <!-- Desktop image slide show (to the right of title and description)-->
                        <div id="desktop-carousel" class="carousel slide">
                            <div data-target="#desktop-carousel" class="cursor-hand" data-slide="next">
                                <div class="carousel-inner shadow">
                                    <div class="item active">
                                        <img class="img-responsive feature-image" src="img/<?php text('CAROUSEL_IMAGE_TYPE') ?>/1-update-available-min.png"
                                             alt="<?php text('CAROUSEL_ALT_1') ?>">
                                    </div>
                                    <div class="item">
                                        <img class="img-responsive feature-image" src="img/<?php text('CAROUSEL_IMAGE_TYPE') ?>/2-install-guide-min.png"
                                             alt="<?php text('CAROUSEL_ALT_2') ?>">
                                    </div>
                                    <div class="item">
                                        <img class="img-responsive feature-image" src="img/<?php text('CAROUSEL_IMAGE_TYPE') ?>/3-up-to-date-min.png"
                                             alt="<?php text('CAROUSEL_ALT_3') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <!-- End of Desktop view -->
    </div>
</div>

<!-- "Features -->
<div class="container-fluid grey-box m-t-20">
    <h2 class="text-center display-block"><?php text('FEATURES_TITLE') ?></h2>
</div>

<!-- 3 main features -->
<div class="container-fluid">
    <div class="row text-center">
        <!-- Update notifications -->
        <div class="col-sm-4 col-md-4 col-lg-4">
            <div class="white thumbnail">
                <img src="img/logo_notifications-min.png" alt="<?php text('UPDATE_NOTIFICATIONS_TITLE') ?>"
                     class="img-responsive background-white">
                <div class="caption">
                    <h3><?php text('UPDATE_NOTIFICATIONS_TITLE') ?></h3>
                    <p><?php text('UPDATE_NOTIFICATIONS_TEXT') ?></p>
                </div>
            </div>
        </div>
        <!-- Download system updates -->
        <div class="col-sm-4 col-md-4 col-lg-4">
            <div class="thumbnail">
                <img src="img/logo_downloading_update-min.png" alt="<?php text('DOWNLOAD_SYSTEM_UPDATES_TITLE') ?>"
                     class="img-responsive">
                <div class="caption">
                    <h3><?php text('DOWNLOAD_SYSTEM_UPDATES_TITLE') ?></h3>
                    <p><?php text('DOWNLOAD_SYSTEM_UPDATES_TEXT') ?></p>
                </div>
            </div>
        </div>
        <!-- Install system updates* -->
        <div class="col-sm-4 col-md-4 col-lg-4">
            <div class="thumbnail">
                <img src="img/logo_installing_update-min.png" alt="<?php text('INSTALL_SYSTEM_UPDATES_ALT') ?>"
                     class="img-responsive background-black">
                <div class="caption">
                    <h3><?php text('INSTALL_SYSTEM_UPDATES_TITLE') ?></h3>
                    <p><?php text('INSTALL_SYSTEM_UPDATES_TEXT') ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<hr>

<!-- Other features -->
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-4 col-lg-4 col-md-4">
            <div class="media-object-default">
                <!-- Update descriptions -->
                <div class="media">
                    <div class="media-left">
                        <img class="media-object" src="img/small_logo_update_description-min.png"
                             alt="<?php text('UPDATE_DESCRIPTIONS_ALT') ?>">
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading"><?php text('UPDATE_DESCRIPTIONS_TITLE') ?></h4>
                        <p><?php text('UPDATE_DESCRIPTIONS_TEXT') ?></p>
                        <h6><?php text('UPDATE_DESCRIPTIONS_NOTE') ?></h6>
                    </div>
                </div>
                <!-- Device information -->
                <div class="media">
                    <div class="media-left">
                        <img class="media-object" src="img/small_logo_device_information-min.png"
                             alt="<?php text('DEVICE_INFORMATION_ALT') ?>">
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading"><?php text('DEVICE_INFORMATION_TITLE') ?></h4>
                        <?php text('DEVICE_INFORMATION_TEXT') ?>
                    </div>
                </div>
                <p class="hidden-md hidden-lg"></p>
            </div>
        </div>
        <div class="col-sm-4 col-lg-4 col-md-4">
            <div class="media-object-default">
                <!-- All devices supported -->
                <div class="media">
                    <div class="media-left">
                        <img class="media-object" src="img/small_logo_device_support-min.png"
                             alt="<?php text('DEVICE_SUPPORT_TITLE') ?>">
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading"><?php text('DEVICE_SUPPORT_TITLE') ?></h4>
                        <?php text('DEVICE_SUPPORT_TEXT') ?>
                    </div>
                </div>
                <!-- News -->
                <div class="media">
                    <div class="media-left">
                        <img class="media-object" src="img/small_logo_news-min.png"
                             alt="<?php text('NEWS_ALT') ?>">
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading"><?php text('NEWS_TITLE') ?></h4>
                        <?php text('NEWS_TEXT') ?>
                    </div>
                </div>
                <p class="hidden-lg"></p>
            </div>
        </div>
        <div class="col-lg-4 col-md-4">
            <div class="media-object-default">
                <!-- 3 update methods supported -->
                <div class="media">
                    <div class="media-left">
                        <img class="media-object" src="img/small_logo_update_method-min.png"
                             alt="<?php text('UPDATE_METHODS_TITLE') ?>">
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading"><?php text('UPDATE_METHODS_TITLE') ?></h4>
                        <?php text('UPDATE_METHODS_TEXT') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<hr class="m-b-0">

<!-- Info about app -->
<div class="container-fluid background-whitesmoke">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="text-center">
                <address>
                    <strong><?php text('APP_NAME') ?></strong><br>
                    <?php text('FOOTER_DESCRIPTION') ?>
                </address>
                <address>
                    <?php text('FOOTER_DEVELOPED_BY') ?>
                    <strong><a href="https://www.linkedin.com/in/adhirajsinghchauhan"><?php text('AUTHOR_NAME') ?></a></strong><br>
                </address>
            </div>
        </div>
    </div>
</div>

<!-- Legal notice -->
<footer class="text-center">
    <div class="container-fluid background-whitesmoke">
        <div class="row">
            <div class="col-xs-12">
                <p><?php text('FOOTER_LEGAL_ONEPLUS') ?></p>
                <p><?php text('FOOTER_LEGAL_GOOGLE') ?></p>
            </div>
        </div>
    </div>
</footer>

<script>
    // Script which auto-starts the image slideshows, and sets the slide time to 7 seconds (7000 milliseconds)

    $('#desktop-carousel').carousel({
        interval: 7000,
        cycle: true
    });

    $('#mobile-carousel').carousel({
        interval: 7000,
        cycle: true
    });
</script>

<script>
    // Script which makes the navbar transparent when scrolled down a tiny bit. Only used on the desktop website.

    var isAllowed = true; // Don't execute this too often - it slows down so much...
    var fadeStart = 10; // 100px scroll or less will equiv to 1 opacity
    var fadeUntil = 200; // 200px scroll or more will equiv to 0 opacity
    var minOpacity = 0.9; // Don't make the navbar completely transparent - only a bit.
    var navbar = $('#nav');

    function allowScrollEvent() {
        isAllowed = true;
    }

    $(window).bind('scroll', function() {
        if(isAllowed && window.innerWidth > 768) {
            var offset = $(document).scrollTop();
            var opacity = 0.9;

            if (offset <= fadeStart) {
                opacity = 1;
            } else if (offset <= fadeUntil) {
                opacity = (1 - offset / fadeUntil);
                if (opacity < minOpacity) opacity = minOpacity;
            }
            navbar.css('opacity', opacity);
            isAllowed = false;
            setTimeout(allowScrollEvent, 50);
        }
    });
</script>
</body>
</html>
