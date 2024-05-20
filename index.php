<?php
$isMobile = preg_match("/Mobile|iP(hone|od|ad)|Android|BlackBerry|IEMobile/", $_SERVER['HTTP_USER_AGENT']);
$isAndroid = preg_match("/Android/", $_SERVER['HTTP_USER_AGENT']);
$isChrome = preg_match("/Chrome/", $_SERVER['HTTP_USER_AGENT']);
$isPwaInstalled = @$_REQUEST['pwa'] === "true" ? 1 : 0;

$showInstallButtonTop = (!$isPwaInstalled) && (($isMobile && ($isAndroid)));
$showInstallButtonLater = (!$isPwaInstalled) && ((!$isMobile && ($isChrome)));
$showShare = $isMobile && $isAndroid;

$viewPort = $isMobile ? "width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"
    : "width=device-width, user-scalable=yes, initial-scale=1.0, maximum-scale=2.0, minimum-scale=1.0";

$baseProto = $_SERVER['HTTP_HOST'] == "localhost" ? "http" : "https";
$baseUrl = $baseProto . "://" . $_SERVER['HTTP_HOST'] . "/";
$autoEnableFluid = false; //!$isMobile;
$enableExternalFont = true;

require('clashfinder_data.php');
require('functions.php');

ob_start();

$siteDesc = getSiteTitle() . " festival app with now/next playing, lineup, timetables, calendar exports...";
$allActs = getAllActsFromClashFinder();
$allStages = getAllStages($allActs);

ob_clean();

if (@strlen($_REQUEST['stage']) > 1) {
    emitIcalForEvents($allActs, $_REQUEST['stage']);
} else {
    $curTS = time() + (24 * 60 * 60 * 16) + (9329 * 2);
    if (@!$_REQUEST['fake']) $curTS = time();

    $curTS_fmt = strftime("[%a] %H:%M:%S", $curTS);

    $status = get3ActsByStage($allActs, $allStages, $curTS);
    $actsByStage = splitByStageOrderByTs($allActs, $allStages);
    header('Content-type: text/html');
    header('Cache-Control: private, max-age=0');
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <title><?= getSiteTitle() ?></title>
        <link rel="manifest" href="<?= cacheBusterLink("manifest.json.php") ?>"/>
        <meta name="theme-color" content="#0c1d2d"/>
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="<?= getSiteTitle() ?>">
        <link rel="apple-touch-icon" href="<?= cacheBusterLink("img/main.icon.png") ?>">
        <link rel="icon" href="<?= cacheBusterLink("img/main.icon.png") ?>">

        <link rel="apple-touch-startup-image" media="screen and (device-width: 430px) and (device-height: 932px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)"
              href="<?= cacheBusterLink("img/splash_screens/iPhone_15_Pro_Max__iPhone_15_Plus__iPhone_14_Pro_Max_landscape.png") ?>>
        <link rel=" apple-touch-startup-image
        " media="screen and (device-width: 393px) and (device-height: 852px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)"
        href="<?= cacheBusterLink("img/splash_screens/iPhone_15_Pro__iPhone_15__iPhone_14_Pro_landscape.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 428px) and (device-height: 926px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)"
              href="<?= cacheBusterLink("img/splash_screens/iPhone_14_Plus__iPhone_13_Pro_Max__iPhone_12_Pro_Max_landscape.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 390px) and (device-height: 844px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)"
              href="<?= cacheBusterLink("img/splash_screens/iPhone_14__iPhone_13_Pro__iPhone_13__iPhone_12_Pro__iPhone_12_landscape.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)"
              href="<?= cacheBusterLink("img/splash_screens/iPhone_13_mini__iPhone_12_mini__iPhone_11_Pro__iPhone_XS__iPhone_X_landscape.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)"
              href="<?= cacheBusterLink("img/splash_screens/iPhone_11_Pro_Max__iPhone_XS_Max_landscape.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)"
              href="<?= cacheBusterLink("img/splash_screens/iPhone_11__iPhone_XR_landscape.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)"
              href="<?= cacheBusterLink("img/splash_screens/iPhone_8_Plus__iPhone_7_Plus__iPhone_6s_Plus__iPhone_6_Plus_landscape.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)"
              href="<?= cacheBusterLink("img/splash_screens/iPhone_8__iPhone_7__iPhone_6s__iPhone_6__4.7__iPhone_SE_landscape.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)"
              href="<?= cacheBusterLink("img/splash_screens/4__iPhone_SE__iPod_touch_5th_generation_and_later_landscape.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 1032px) and (device-height: 1376px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)"
              href="<?= cacheBusterLink("img/splash_screens/13__iPad_Pro_M4_landscape.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)"
              href="<?= cacheBusterLink("img/splash_screens/12.9__iPad_Pro_landscape.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 834px) and (device-height: 1210px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)"
              href="<?= cacheBusterLink("img/splash_screens/11__iPad_Pro_M4_landscape.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 834px) and (device-height: 1194px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)"
              href="<?= cacheBusterLink("img/splash_screens/11__iPad_Pro__10.5__iPad_Pro_landscape.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 820px) and (device-height: 1180px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)"
              href="<?= cacheBusterLink("img/splash_screens/10.9__iPad_Air_landscape.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)"
              href="<?= cacheBusterLink("img/splash_screens/10.5__iPad_Air_landscape.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 810px) and (device-height: 1080px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)"
              href="<?= cacheBusterLink("img/splash_screens/10.2__iPad_landscape.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)"
              href="<?= cacheBusterLink("img/splash_screens/9.7__iPad_Pro__7.9__iPad_mini__9.7__iPad_Air__9.7__iPad_landscape.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 744px) and (device-height: 1133px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)"
              href="<?= cacheBusterLink("img/splash_screens/8.3__iPad_Mini_landscape.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 430px) and (device-height: 932px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)"
              href="<?= cacheBusterLink("img/splash_screens/iPhone_15_Pro_Max__iPhone_15_Plus__iPhone_14_Pro_Max_portrait.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 393px) and (device-height: 852px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)"
              href="<?= cacheBusterLink("img/splash_screens/iPhone_15_Pro__iPhone_15__iPhone_14_Pro_portrait.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 428px) and (device-height: 926px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)"
              href="<?= cacheBusterLink("img/splash_screens/iPhone_14_Plus__iPhone_13_Pro_Max__iPhone_12_Pro_Max_portrait.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 390px) and (device-height: 844px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)"
              href="<?= cacheBusterLink("img/splash_screens/iPhone_14__iPhone_13_Pro__iPhone_13__iPhone_12_Pro__iPhone_12_portrait.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)"
              href="<?= cacheBusterLink("img/splash_screens/iPhone_13_mini__iPhone_12_mini__iPhone_11_Pro__iPhone_XS__iPhone_X_portrait.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)"
              href="<?= cacheBusterLink("img/splash_screens/iPhone_11_Pro_Max__iPhone_XS_Max_portrait.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)"
              href="<?= cacheBusterLink("img/splash_screens/iPhone_11__iPhone_XR_portrait.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)"
              href="<?= cacheBusterLink("img/splash_screens/iPhone_8_Plus__iPhone_7_Plus__iPhone_6s_Plus__iPhone_6_Plus_portrait.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)"
              href="<?= cacheBusterLink("img/splash_screens/iPhone_8__iPhone_7__iPhone_6s__iPhone_6__4.7__iPhone_SE_portrait.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)"
              href="<?= cacheBusterLink("img/splash_screens/4__iPhone_SE__iPod_touch_5th_generation_and_later_portrait.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 1032px) and (device-height: 1376px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)"
              href="<?= cacheBusterLink("img/splash_screens/13__iPad_Pro_M4_portrait.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)"
              href="<?= cacheBusterLink("img/splash_screens/12.9__iPad_Pro_portrait.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 834px) and (device-height: 1210px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)"
              href="<?= cacheBusterLink("img/splash_screens/11__iPad_Pro_M4_portrait.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 834px) and (device-height: 1194px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)"
              href="<?= cacheBusterLink("img/splash_screens/11__iPad_Pro__10.5__iPad_Pro_portrait.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 820px) and (device-height: 1180px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)"
              href="<?= cacheBusterLink("img/splash_screens/10.9__iPad_Air_portrait.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)"
              href="<?= cacheBusterLink("img/splash_screens/10.5__iPad_Air_portrait.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 810px) and (device-height: 1080px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)"
              href="<?= cacheBusterLink("img/splash_screens/10.2__iPad_portrait.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)"
              href="<?= cacheBusterLink("img/splash_screens/9.7__iPad_Pro__7.9__iPad_mini__9.7__iPad_Air__9.7__iPad_portrait.png") ?>">
        <link rel="apple-touch-startup-image" media="screen and (device-width: 744px) and (device-height: 1133px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)"
              href="<?= cacheBusterLink("img/splash_screens/8.3__iPad_Mini_portrait.png") ?>">

        <meta charset="UTF-8">
        <meta name="viewport"
              content="<?= $viewPort ?>">
        <meta property="og:url" content="<?= $baseUrl ?>"/>
        <meta property="og:type" content="website"/>
        <meta property="og:title" content="<?= getSiteTitle() ?>"/>
        <meta property="og:description" content="<?= htmlentities($siteDesc) ?>"/>
        <meta name="Description"
              content="<?= htmlentities($siteDesc) ?>">
        <meta property="og:image" content="<?= cacheBusterLink("img/main.icon.png") ?>"/>
        <meta property="og:image:width" content="1200"/>
        <meta property="og:image:height" content="1200"/>
        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        <?php
        if ($enableExternalFont) {
            ?>
            <link href="https://fonts.googleapis.com/css?family=Barlow:400,700&display=swap" rel="stylesheet">
            <?php
        }
        ?>

        <link rel="stylesheet" type="text/css" href="<?= cacheBusterLink("styles.css") ?>">
        <link rel="stylesheet" type="text/css" href="<?= cacheBusterLink("lineup.css") ?>">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.1.2/handlebars.runtime.min.js"></script>
        <script type="text/javascript">
            async function share() {
                sharePWA("<?=getSiteTitle()?>", "Check out this <?=getSiteTitle()?> app! 😍 Touch the logo and then drag for trippy visuals! ", "<?=$baseUrl?>")
            }

            window.fakeTimeForNow = <?= (@!$_REQUEST['fake']) ? "null" : time() + (60 * 60 * 24 * 4) ?>;
            window.serviceWorkerWithCacheBuster = "<?= cacheBusterLink("serviceworker.js")?>";
            window.fluidPatternFile = "<?= cacheBusterLink("fluid/LDR_LLL1_0.png")?>";
            window.autoStartFluid = <?=$autoEnableFluid ? "true" : "false"?>;
            window.actsByStage = <?=json_encode($actsByStage)?>;
            window.stages = <?=json_encode($allStages)?>;
            console.log(window.actsByStage);
        </script>
        <?= scriptTagWithInlineScript('js/pwa.js') ?>
    </head>
    <body>

    <?php showInstallButton($showInstallButtonTop); ?>

    <canvas></canvas>

    <header>
        <div class="logo">
            <img id="logo" src="<?= cacheBusterLink("img/logo.main.png") ?>" width="458" height="284"
                 loading="eager"
                 alt="<?= getSiteTitle() ?>" <?= $autoEnableFluid ? "" : "onclick=\"startFluid()\" class=\"withPointerEvents\"" ?>/>
        </div>
    </header>

    <section id="timetable">
        <h2>
            <div class="container">Now and Next</div>
        </h2>

        <div class="container">

            <a class="button button--weather" href="https://www.buienradar.nl/weer/evergem/be/2798551/14daagse" target="buien"> Weather </a>
            <a class="button button--map" href="https://www.google.com/maps/place/Twaalfroeden+10,+9042+Gent" target="gmaps"> Festival Map </a>

            <div id="table-now-next">
                <?php if (true) {
                    ?>
                    <table>
                        <thead>
                        <th><?= $curTS_fmt ?></th>
                        <td>Now</td>
                        <td>Next</td>
                        </thead>

                        <tbody>
                        <?php
                        foreach ($status as $stage => $data) {
                            ?>
                            <tr>
                                <th><?= $stage ?></th>
                                <td data-before="Now"><?= show3Data($data['now']) ?></td>
                                <td data-before="Next"><?= show3Data($data['next']) ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                <?php } ?>
            </div>
        </div>
    </section>

    <style id="lineupstyle" type="text/css">
        .schedule {
            border: 1px solid red
        }
    </style>
    <section id="lineup">
        <h2>
            <div class="container">Lineup</div>
        </h2>

        <div class="container too_high">
            <div id="table-lineup" class="schedule">
                wtf
            </div>
        </div>
    </section>


    <section id="calendars">
        <h2>
            <div class="container">Timetable / Lineup to Google or other Calendars</div>
        </h2>

        <div class="container">
            <table>
                <thead>
                <th></th>
                <td>Google</td>
                <td>iCal/Outlook</td>
                </thead>
                <tbody>
                <?php
                foreach ($allStages as $stage) {
                    $url = "{$baseUrl}?cb=" . crc32(time()) . "&stage=" . urlencode($stage);
                    $webCal = str_replace("https://", "webcal://", $url);
                    ?>
                    <tr class="table--wide">
                        <th><?= $stage ?></th>
                        <td><a class="button"
                               href="https://www.google.com/calendar/render?cid=<?= urlencode($webCal) ?>&added_at=<?= time() ?>"
                               target="gcalendar">Add <?= $stage ?> to Google</a></td>
                        <td><a class="button" href="<?= $url ?>">Get <?= $stage ?> iCal</a></td>
                    </tr>
                    <?php
                }
                ?>
                </tbody>
            </table>
        </div>
    </section>

    <section id="text">
        <h2>
            <div class="container">Adding to your own calendar</div>
        </h2>
        <div class="container">
            <p>Above are links to each stage's timetable in iCal (ICS) format.</p>
            <p>You can import it in any calendar app you want.</p>
            <p>I recommend adding each stage as a separate calendar (so each has its own color etc), but there is also
                an <a href="#single">all-stages calendar</a> which is quite messy.</p>

            <h3>Google Calendar</h3>
            <p>Click on the "add" of the corresponding stage. Each time, a new tab/window will open in Google Calendar,
                and you just click "Yes" or "Add" in the confirmation dialog. Google will take a few seconds after that
                to display the calendar.</p>
            <p>It will auto-update every 60 minutes, so you're always set!</p>
            <p>Unfortunately Google will take a while to read the calendar's Title, and will instead show the
                webcal://xxx URL for a while. It should fix itself in a few hours.</p>

            <h3>Outlook</h3>
            <p>Just click on the links above; download the ICS file and import into Outlook.</p>
            <p>Or, use the "Add Calendar" &gt; "From Internet" and paste the URL from the link above (click and hold to
                copy); this way it will auto-update as well.</p>

        </div>
    </section>

    <section id="clashfinder">
        <h2>
            <div class="container">Backing Data</div>
        </h2>
        <div class="container">
            <p>All the backing data is stored in ClashFinder.</p>
            <p>Thanks to the people who started it and keep it updated.</p>
            <p>Check it out: <a href="https://clashfinder.com/s/<?= getClashfinderID() ?>/" target="clashfinder"><?= getSiteTitle() ?> Clashfinder</a>.</p>
            <p>You can also check all the <a href="https://clashfinder.com/l/<?= getClashfinderID() ?>/?revs" target="clashfinder">changes</a> made over time.</p>
        </div>
    </section>

    <!--    <section id="sharing">
        <h2>
            <div class="container">Share with your friends</div>
        </h2>
        <div class="container">
            <img class="qrcode color-rotate" src="<?php /*= cacheBusterLink("img/qr-code.svg") */ ?>" width="429"/>
            <?php /*if ($showShare) {
                */ ?>
                <a class="button" style="width: 30%" onclick="share()">Share</a>
                <?php
    /*            }
                */ ?>
        </div>
    </section>
-->
    <!-- <section id="single">
        <h2><div class="container">All-stages calendar</div></h2>

        <div class="container">
            <?php
    $url = "{$baseUrl}?stage=" . urlencode("ALL");
    ?>
            <p>Also, there's this version with all stages in a single calendar: <a href="<?php echo $url ?>">All stages
                    (bit confusing)</a>.</p>
            <?php showInstallButton($showInstallButtonLater); ?>

        </div>
    </section> -->

    <footer>
        <div class="container">
            <div>made with 💚 by <a href="mailto:ricardo@pardini.net">rpardini</a> & <a href="mailto:dine@dine.tk">dine</a>💚️
            </div>
            <div>fluid simulation by <a href="https://github.com/PavelDoGreat">PavelDoGreat</a></div>
        </div>
    </footer>

    <?= scriptTagWithInlineScript('js/templates.compiled.js') ?>
    <?= scriptTagWithInlineScript('js/now-next.js') ?>
    <?= scriptTagWithInlineScript('js/lineup.js') ?>

    <?= scriptTagWithInlineScript('js/fluid-config.js') ?>
    <script async src="<?= cacheBusterLink("fluid/script.js") ?>"></script>

    </body>
    </html>
    <?php
}

function showInstallButton($ifCondition)
{
    if (!$ifCondition) return;
    ?>
    <section id="install">
        <a class="button" href="javascript:installPwa()" onclick="installPwa()">Add to Home for Offline data and Fullscreen! Awesome!</a>
    </section>
    <?php
}

function show3Data($act)
{
    if (!$act) {
        return "<div class='act'>--</div>";
    }

    return "<div>" . showTimespan($act['ts_start'], $act['ts_end']) . "</div>"
        . "<div class='color-rotate'>" . $act['what'] . "</div>";
}

function showTimespan($startObj, $endObj)
{
    $dayStart = date('d', $startObj['ts']); // 1-31
    $dayEnd = date('d', $endObj['ts']); // 1-31

    // First and easiest case is if both are on the same day...
    if ($dayStart == $dayEnd) {
        return strftime("[%a] %H:%M", $startObj['ts']) . "-" . strftime("%H:%M", $endObj['ts']);
    }
    // If not on the same day gotta indicate both.
    return strftime("[%a] %H:%M", $startObj['ts']) . "-" . strftime("%H:%M[%a]", $endObj['ts']);

}