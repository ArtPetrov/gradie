<?php

namespace App\Helper\OAuth;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\InstagramIdentityProviderException;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class InstagramProvider extends AbstractProvider
{
    /**
     * @var string Key used in a token response to identify the resource owner.
     */
    const ACCESS_TOKEN_RESOURCE_OWNER_ID = 'user.id';

    /**
     * Default scopes
     *
     * @var array
     */
    public $defaultScopes = ['user_profile','user_media'];

    /**
     * Default host
     *
     * @var string
     */
    protected $host = 'https://api.instagram.com';

    protected $hostDetails = 'https://graph.instagram.com';

    /**
     * Gets host.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Get the string used to separate scopes.
     *
     * @return string
     */
    protected function getScopeSeparator()
    {
        return ' ';
    }

    /**
     * Get authorization url to begin OAuth flow
     *
     * @return string
     */
    public function getBaseAuthorizationUrl()
    {
        return $this->host.'/oauth/authorize';
    }

    /**
     * Get access token url to retrieve token
     *
     * @param  array $params
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->host.'/oauth/access_token';
    }

    /**
     * Get provider url to fetch user details
     *
     * @param  AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return $this->hostDetails.'/me?fields=id,username&access_token='.$token;
    }

    /**
     * Returns an authenticated PSR-7 request instance.
     *
     * @param  string $method
     * @param  string $url
     * @param  AccessToken|string $token
     * @param  array $options Any of "headers", "body", and "protocolVersion".
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public function getAuthenticatedRequest($method, $url, $token, array $options = [])
    {
        $parsedUrl = parse_url($url);
        $queryString = array();

        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $queryString);
        }

        if (!isset($queryString['access_token'])) {
            $queryString['access_token'] = (string) $token;
        }

        $url = http_build_url($url, [
            'query' => http_build_query($queryString),
        ]);

        return $this->createRequest($method, $url, null, $options);
    }

    /**
     * Get the default scopes used by this provider.
     *
     * This should not be a complete list of all scopes, but the minimum
     * required for the provider user interface!
     *
     * @return array
     */
    protected function getDefaultScopes()
    {
        return $this->defaultScopes;
    }

    /**
     * Check a provider response for errors.
     *
     * @link   https://instagram.com/developer/endpoints/
     * @throws IdentityProviderException
     * @param  ResponseInterface $response
     * @param  string $data Parsed response data
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        // Standard error response format
        if (!empty($data['meta']['error_type'])) {
            throw InstagramIdentityProviderException::clientException($response, $data);
        }

        // OAuthException error response format
        if (!empty($data['error_type'])) {
            throw InstagramIdentityProviderException::oauthException($response, $data);
        }
    }

    /**
     * Generate a user object from a successful user details request.
     *
     * @param array $response
     * @param AccessToken $token
     * @return ResourceOwnerInterface
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new InstagramResourceOwner($response);
    }

    /**
     * Sets host.
     *
     * @param string $host
     *
     * @return string
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }
}
