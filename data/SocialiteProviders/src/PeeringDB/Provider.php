<?php

namespace SocialiteProviders\PeeringDB;

use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    /**
     * Unique Provider Identifier.
     */
    const IDENTIFIER = 'PEERINGDB';

    /**
     * {@inheritdoc}
     */
//    protected $scopes = ['profile', 'email', 'networks'];
    protected $scopes = ['profile email networks'];
    /**
     * {@inheritdoc}
     */
    #[\Override]
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://auth.peeringdb.com/oauth2/authorize', $state);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    protected function getTokenUrl()
    {
        return 'https://auth.peeringdb.com/oauth2/token/';
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://auth.peeringdb.com/profile/v1', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            //'id'       => $user['id'],
            'name'     => $user['name'],
            'email'    => $user['email'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code'
        ]);
    }
}
