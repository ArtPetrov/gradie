<?php

declare(strict_types=1);

namespace App\Helper\OAuth;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

class OdnoklassnikiProvider extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * @var string
     */
    public $clientPublic;

    /**
     * {@inheritdoc}
     */
    public function getBaseAuthorizationUrl()
    {
        return "https://connect.ok.ru/oauth/authorize";
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return "https://api.ok.ru/oauth/token.do";
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        $secretKey = md5($token->getToken() . $this->clientSecret);
        $sig = md5('application_key=' . $this->clientPublic . 'fields=uid,name,first_name,last_name,location,pic_3,gender,locale,photo_id,emailformat=jsonmethod=users.getCurrentUser' . $secretKey);
        $param = 'application_key=' . $this->clientPublic
            . '&format=json'
            . '&fields=uid,name,first_name,last_name,location,pic_3,gender,locale,photo_id,email'
            . '&method=users.getCurrentUser';
        return 'http://api.ok.ru/fb.do?' . $param . '&access_token=' . $token->getToken() . '&sig=' . $sig;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultScopes()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (isset($data['error_code'])) {
            throw new IdentityProviderException($data['error_msg'], $data['error_code'], $response);
        } elseif (isset($data['error'])) {
            throw new IdentityProviderException($data['error'] . ': ' . $data['error_description'],
                $response->getStatusCode(), $response);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new OdnoklassnikiResourceOwner($response);
    }
}
