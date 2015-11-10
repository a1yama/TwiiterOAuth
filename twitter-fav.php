<?php
    require_once('twitteroauth/autoload.php');
    require_once('twitteroauth/src/TwitterOAuth.php');
    require_once(dirname(__FILE__) . '/config.php');
    use Abraham\TwitterOAuth\TwitterOAuth;

    $connection = new TwitterOAuth(consumer_key, consumer_secret, access_token, access_token_secret);
    
    $keyword_list = array(
        "ANIERA",
    );
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
            $favo_list[] = @$list->id;
        }
    }

    if (isset($favo_list)) {
        foreach ($favo_list as $value) {
            $statues = $connection->post("favorites/create", array(
                "id" => $value,
                "include_entities" => "false",
            ));
        }
    }