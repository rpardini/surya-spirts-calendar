<?php
date_default_timezone_set('Europe/Amsterdam');

define('CACHE_KEY', "clashfinder-expiring2");
define('CACHE_KEY_FALLBACK', "clashfinder-fallback2");

function getClashFinderData()
{
    $redis = new Redis();
    $redis->connect('redis');

    if ($data = $redis->get(CACHE_KEY)) {
        // cached data...
        echo "Using cached data from ClashFinder...!";
        $data = unserialize($data);
    } else {
        // get new data from there; if it fails, use fallback...
        $url = "https://clashfinder.com/data/event/" . getClashfinderID() . ".json";

        $ctxparams = null;
        if (getenv('PROXY_ADDR')) {
            $ctxparams = [
                'http' => [
                    'proxy' => 'tcp://' . getenv('PROXY_ADDR'),
                    'request_fulluri' => true,
                ],
            ];
        }
        $cxContext = stream_context_create($ctxparams);

        if ($data = file_get_contents($url, false, $cxContext)) {
            echo "Got new data from ClashFinder...!";

            if ($data = json_decode($data, true)) {
                echo "Managed to decode JSON OK!...";
                $serializedData = serialize($data);
                $redis->setex(CACHE_KEY, 60 * 5, $serializedData);
                $redis->set(CACHE_KEY_FALLBACK, $serializedData);
            } else {
                echo "Failed to decode JSON... using Fallback data...";
                $data = unserialize($redis->get(CACHE_KEY_FALLBACK));
            }
        } else {
            echo "Failed getting data from ClashFinder. Using fallback data...";
            $data = unserialize($redis->get(CACHE_KEY_FALLBACK));
        }
    }
    //print_r($data);
    return $data;
}

function getAllActsFromClashFinder()
{
    $json = getClashFinderData();
    $ret = [];

    foreach ($json['locations'] as $location) {
        $where = trim($location['name']);
        $events = $location['events'];

        $obj = array();
        foreach ($events as $event) {
            //print_r($event);
            $obj['what'] = trim($event['name']);
            $obj['where'] = $where;
            $obj['ts_start'] = parseClashFinderDate($event['start']);
            $obj['ts_end'] = parseClashFinderDate($event['end']);
            $ret[] = $obj;
        }
    }

    //print_r($ret);
    return $ret;
}

function parseClashFinderDate($str)
{
    // 2019-08-28 08:00
    $ts_startObj = date_create_from_format("Y-n-j G:i", $str);
    if ($ts_startObj === false) throw new Exception("Could not parse date: $str");

    $ts_start = $ts_startObj->getTimestamp();
    // Fix: some acts are said to end on minute 59 or 29 to "avoid clashes" but that's stupid.
    // detect that case and add a minute.

    $minute = date("i", $ts_start);
    //echo "Minute: $minute \n";
    if (($minute == 59) || ($minute == 29)) {
        $ts_start = $ts_start + 60;
    }

    $reformat = strftime("%d/%a[%H:%M]", $ts_start);
    return array('ts' => $ts_start, 'format' => $reformat);
}
