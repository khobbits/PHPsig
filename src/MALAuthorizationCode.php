<?php

use GuzzleHttp\ClientInterface;
use kamermans\OAuth2\GrantType\GrantTypeInterface;
use kamermans\OAuth2\Utils\Helper;
use kamermans\OAuth2\Utils\Collection;
use kamermans\OAuth2\Signer\ClientCredentials\SignerInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Authorization code grant type.
 *
 * @link http://tools.ietf.org/html/rfc6749#section-4.1
 */
class MALAuthorizationCode implements GrantTypeInterface
{
    /**
     * The token endpoint client.
     *
     * @var ClientInterface
     * @var ClientInterface
     */
    private $client;

    /**
     * Configuration settings.
     *
     * @var Collection
     */
    private $config;

    public function __construct(ClientInterface $client, array $config)
    {
        $this->client = $client;
        $this->config = Collection::fromConfig(
            $config,
            // Defaults
            [
            ],
            // Required
            [
                'code',
                'client_id',
                'client_secret',
                'redirect_uri',
                'code_verifier'
            ]
        );
    }

    public function getRawData(SignerInterface $clientCredentialsSigner, $refreshToken = null)
    {

        $request = (new \GuzzleHttp\Psr7\Request('POST', $this->client->getConfig()['base_uri']))
                    ->withBody($this->getPostBody())
                    ->withHeader('Content-Type', 'application/x-www-form-urlencoded');


        $request = $clientCredentialsSigner->sign(
            $request,
            $this->config['client_id'],
            $this->config['client_secret']
        );

        $response = $this->client->send($request);

        return json_decode($response->getBody(), true);
    }

    /**
     * @return StreamInterface
     */
    protected function getPostBody()
    {

        $data = [
            'grant_type' => 'authorization_code',
            'code' => $this->config['code'],
            'code_verifier' => $this->config['code_verifier'],
            'redirect_uri' => $this->config['redirect_uri']
        ];

        return \GuzzleHttp\Psr7\stream_for(http_build_query($data, '', '&'));

    }
}
