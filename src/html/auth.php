<?php
include '../config.php';
include '../malClient.php';

$auth_code = isset($_GET['code']) ? $_GET['code'] : null;
$state = isset($_GET['state']) ? $_GET['state'] : null;
$state2 = isset($_SESSION['state']) ? $_SESSION['state'] : null;

// If we have no access token or refresh token, we need to get user consent to obtain one
if ($token_storage->hasToken() === false && ($auth_code === null)) {

    $_SESSION['challenge'] = $challenge = hash('sha256', time());
    $_SESSION['state'] = $state = time();

    $auth_url = 'https://myanimelist.net/v1/oauth2/authorize?' . http_build_query([
            'client_id' => $client_id,
            'redirect_uri' => $redirect_uri,
            'response_type' => 'code',
            'state' => $state,
            'code_challenge' => $challenge,
            'code_challenge_method' => 'plain'

        ]);

    echo "Go to the following link in your browser:\n\n";
    echo "<a href='$auth_url'>here</a>\n";

    die();

} elseif ($token_storage->hasToken() === false && $challenge !== null) { // && ($auth_code !== null)
    if ($state != $state2) {
        print "Session corruption found, restart from scratch!";
        unset($_SESSION['state']);
        unset($_SESSION['challenge']);
        die();
    }
} elseif ($token_storage->hasToken() === false && $challenge === null) {
    print "No session found, restart from scratch!";
    die();
}

$client = mal_client($auth_code, $client_id, $client_secret, $redirect_uri, $challenge, $token_storage);
$response = $client->get('https://api.myanimelist.net/v2/users/@me?fields=anime_statistics');

print "<pre>";
print $response->getBody();


