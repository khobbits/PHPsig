<?php
include "MALAuthorizationCode.php";

use kamermans\OAuth2\GrantType\RefreshToken;

use kamermans\OAuth2\OAuth2Middleware;
use GuzzleHttp\HandlerStack;

function mal_client($auth_code, $client_id, $client_secret, $redirect_uri, $challenge, $token_storage)
{

    $reauth_client = new GuzzleHttp\Client([
        // URL for access_token request
        'base_uri' => 'https://myanimelist.net/v1/oauth2/token',
        'debug' => false,
    ]);

    $reauth_config = [
        'code' => $auth_code,
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'grant_type' => 'authorization_code',
        'redirect_uri' => $redirect_uri,
        'code_verifier' => $challenge
    ];

    // Authorization client - this is used to request OAuth access tokens

    $grant_type = new MALAuthorizationCode($reauth_client, $reauth_config);

    $refresh_grant_type = new RefreshToken($reauth_client, $reauth_config);

    // Lets start firing up a test request

    $oauth = new OAuth2Middleware($grant_type, $refresh_grant_type);
    $oauth->setTokenPersistence($token_storage);
    $stack = HandlerStack::create();
    $stack->push($oauth);

    // This is the normal Guzzle client that you use in your application
    $client = new GuzzleHttp\Client([
        'handler' => $stack,
        'auth' => 'oauth',
        'debug' => false,
    ]);

    return $client;
}
