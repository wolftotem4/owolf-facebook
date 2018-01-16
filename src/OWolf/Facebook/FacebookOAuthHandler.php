<?php

namespace OWolf\Facebook;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use OWolf\Laravel\Contracts\OAuthHandler;
use OWolf\Laravel\ProviderHandler;
use OWolf\Laravel\Traits\OAuthProvider;

class FacebookOAuthHandler extends ProviderHandler implements OAuthHandler
{
    use OAuthProvider;

    /**
     * @param  \League\OAuth2\Client\Token\AccessToken  $token
     * @param  string  $ownerId
     * @return bool
     */
    public function revokeToken(AccessToken $token, $ownerId)
    {
        try {
            $method     = 'DELETE';
            $url        = $this->provider()->getGraphUrl() . $ownerId . '/permissions?access_token=' . $token->getToken();
            $request    = $this->provider()->getAuthenticatedRequest($method, $url, $token);
            $response   = $this->provider()->getParsedResponse($request);

            return array_get($response, 'success', false);
        } catch (IdentityProviderException $e) {
            return false;
        }
    }
}