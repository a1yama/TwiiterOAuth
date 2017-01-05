<?php
    require_once('twitteroauth/autoload.php');
    require_once('twitteroauth/src/TwitterOAuth.php');
    require_once(dirname(__FILE__) . '/config.php');
    use Abraham\TwitterOAuth\TwitterOAuth;

    $connection = new TwitterOAuth(consumer_key, consumer_secret, access_token, access_token_secret);

    $connection->setTimeouts(60, 30);
    $apiReuestUrl = api_url;
    $json = file_get_contents($apiReuestUrl);
    $arr = json_decode($json,true);
    $result = $connection->upload('media/upload', array('media' => $arr['file_path'] ));
    $parameters = array('status' => $arr['status'], 'media_ids' => $result->media_id_string);
    $result = $connection->post('statuses/update', $parameters);
