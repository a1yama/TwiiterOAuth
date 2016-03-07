<?php

require_once('twitteroauth/autoload.php');
require_once('twitteroauth/src/TwitterOAuth.php');
require_once(dirname(__FILE__) . '/config.php');
use Abraham\TwitterOAuth\TwitterOAuth;
require 'vendor/autoload.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// logger
$log = new Logger('logger');
$log->pushHandler(new StreamHandler(__DIR__ . '/logs/app_' . date('Ymd') . '.log', Logger::INFO));

$log->addInfo('batch start.');

$connection = new TwitterOAuth(consumer_key, consumer_secret, access_token, access_token_secret);

$fileName = __DIR__ . "/data/fav.db";

$favDatas = file_get_contents($fileName);

$favDataArray = explode(',', $favDatas);

$keyword_list = array(
    "aniera",
);
$fav_list = [];
foreach ( $keyword_list as $keyword ) {
    $statues = $connection->get("search/tweets", array(
        "q"                 => $keyword.' -from:@Aniera_Japan -from:@Gunshi320 exclude:retweets',
        "count"             => "10",
        "include_entities"  => "false",
        "lang"              => "ja",
        "locale"            => "ja",
        "result_type"       => "recent",
    ));

    foreach ($statues->statuses as $list) {
        $favFlg = true;
        foreach ($favDataArray as $oldFav) {
            if ($oldFav == @$list->id) {
                $favFlg = false;
            }
        }
        if ($favFlg) {
            $fav_list[] = @$list->id;
        }
    }
}

if (isset($fav_list) && !empty($fav_list)) {
    $fav = '';
    foreach ($fav_list as $value) {
        $statues = $connection->post("favorites/create", array(
            "id" => $value,
            "include_entities" => "false",
        ));
        $fav .= ',' . $value;

        // Slack通知
        $text = 'https://twitter.com/my/status/' . $value;
//        https://twitter.com/my/status/$value
        $text = urlencode($text);
        $url = "https://slack.com/api/chat.postMessage?token=" . SLACK_API_KEY . "&channel=%23aniera_twitter_fav&text=" . $text;
        file_get_contents($url);

        $log->addInfo($value);
    }
    file_put_contents($fileName, $fav, FILE_APPEND);
} else {
    $log->addInfo('no favorites');
}

$log->addInfo('batch end.');
