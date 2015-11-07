<?php
    require_once('twitteroauth/autoload.php');
    require_once('twitteroauth/src/TwitterOAuth.php');
    require_once(dirname(__FILE__) . '/config.php');
    use Abraham\TwitterOAuth\TwitterOAuth;

    $connection = new TwitterOAuth(consumer_key, consumer_secret, access_token, access_token_secret);

    $connection->setTimeouts(60, 30);
    $file_path = __DIR__ . '/image/1.jpg';
    $result = $connection->upload('media/upload', array('media' => $file_path));
    $parameters = array('status' => 'Hello World ' . time(), 'media_ids' => $result->media_id_string);
    $result = $connection->post('statuses/update', $parameters);
        
    var_dump($result);
