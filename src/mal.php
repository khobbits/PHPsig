<?php
include 'malClient.php';

function getRawAPI($client_id, $client_secret, $redirect_uri, $challenge, $token_storage, $uri)
{
    $client = mal_client(null, $client_id, $client_secret, $redirect_uri, $challenge, $token_storage);
    $response = $client->get('https://api.myanimelist.net/v2/' . $uri);
    return ($response->getBody()->getContents());
}

function getRawUserStats($client_id, $client_secret, $redirect_uri, $challenge, $token_storage)
{
    if (useCache()) {
        return json_decode(file_get_contents('profile.json'), true);
    }

    $uri = 'users/@me?fields=anime_statistics';
    $response = getRawAPI($client_id, $client_secret, $redirect_uri, $challenge, $token_storage, $uri);

    file_put_contents('profile.json', $response);

    return json_decode($response, true);
}

function getUserStats($client_id, $client_secret, $redirect_uri, $challenge, $token_storage)
{
    $response = getRawUserStats($client_id, $client_secret, $redirect_uri, $challenge, $token_storage);

    $profile['user_name'] = $response['name'];
    $profile['user_watching'] = $response['anime_statistics']['num_items_watching'];
    $profile['user_completed'] = $response['anime_statistics']['num_items_completed'];
    $profile['user_onhold'] = $response['anime_statistics']['num_items_on_hold'];
    $profile['user_dropped'] = $response['anime_statistics']['num_items_dropped'];
    $profile['user_plantowatch'] = $response['anime_statistics']['num_items_plan_to_watch'];
    $profile['user_days_spent_watching'] = $response['anime_statistics']['num_days_watched'];

    return $profile;
}

function useCache()
{
    $timenow = date('U');
    if (file_exists('profile.json') == true) {
        if ($timenow > (filemtime('profile.json') + (60 * 30))) {
            return 0;
        } else {
            return 1;
        }
    } else {
        return 0;
    }
}