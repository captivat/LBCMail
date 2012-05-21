<?php
$key = "";

$dirname = dirname(__FILE__);
require $dirname."/lib/lbc.php";
require $dirname."/ConfigManager.php";

$config = ConfigManager::load();

if (isset($_GET["key"]) && $_GET["key"] != $key) {
    return;
}
if (count($config) == 0) {
    return;
}

function mail_utf8($to, $subject = '(No subject)', $message = '')
{
    $subject = "=?UTF-8?B?".base64_encode($subject)."?=";

    $headers = "MIME-Version: 1.0" . "\r\n" .
            "Content-type: text/html; charset=UTF-8" . "\r\n";

    return mail($to, $subject, $message, $headers);
}

foreach ($config AS $i => $alert) {
    $currentTime = time();
    if (!isset($alert["time_updated"])) {
        $alert["time_updated"] = 0;
    }
    if (((int)$alert["time_updated"] + (int)$alert["interval"]*60) > $currentTime) {
        continue;
    }
    $config[$i]["time_updated"] = $currentTime;
    $content = file_get_contents($alert["url"]);
    $ads = Lbc_Parser::process($content);
    if (count($ads) == 0) {
        continue;
    }
    $cities = array();
    if (!empty($alert["cities"])) {
        $cities = explode("\n", $alert["cities"]);
    }
    $newAds = array();
    if (!isset($alert["time_last_ad"])) {
        $alert["time_last_ad"] = 0;
    }
    $time_last_ad = (int)$alert["time_last_ad"];
    foreach ($ads AS $ad) {
        if ($time_last_ad < $ad->getDate()) {
            if ($cities && !in_array($ad->getCity(), $cities)) {
                continue;
            }
            if ($ad->getPrice()) {
                if (!empty($alert["price_min"]) && $ad->getPrice() < $alert["price_min"]) {
                    continue;
                }
                if (!empty($alert["price_max"]) && $ad->getPrice() > $alert["price_max"]) {
                    continue;
                }
            }
            $newAds[] = require $dirname."/views/mail-ad.phtml";
            if (empty($config[$i]["time_last_ad"])) {
                $config[$i]["time_last_ad"] = $ad->getDate();
            } elseif ($config[$i]["time_last_ad"] < $ad->getDate()) {
                $config[$i]["time_last_ad"] = $ad->getDate();
            }
        }
    }
    if ($newAds) {
        $subject = "Alert LeBonCoin : ".$alert["title"];
        $message = '<h2>Alerte générée le '.date("d/m/Y H:i", $currentTime).'</h2>
            <p>Liste des nouvelles annonces :</p><hr /><br />'.
            implode("<br /><hr /><br />", $newAds).'<hr /><br />';
        mail_utf8($alert["email"], $subject, $message);
    }
}
ConfigManager::save($config);
