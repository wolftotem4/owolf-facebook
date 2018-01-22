<?php

namespace OWolf\Facebook;

use Illuminate\Support\ServiceProvider;
use League\OAuth2\Client\Token\AccessToken;
use OWolf\Credentials\UrlAccessToeknCredentials;
use OWolf\Laravel\UserOAuthManager;
use OWolf\Laravel\Util;

class FacebookCredentialsProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->resolving('owolf.provider', function ($manager, $app) {
            $manager->addDriver('facebook.oauth', function ($name, $config) {
                $oauth = array_get($config, 'oauth', []);

                $oauth['redirectUri'] = Util::redirectUri(array_get($oauth, 'redirectUri'), $name);

                $provider = new Facebook($oauth);
                return new FacebookOAuthHandler($provider, $name, $config);
            });
        });

        $this->app->resolving('owolf.credentials', function ($manager, $app) {
            $manager->addDriver('facebook.oauth', function ($name, $config) use ($app) {
                $manager = $this->app->make(UserOAuthManager::class);
                $session = $manager->session($name);
                return new UrlAccessToeknCredentials($session->provider(), $session->getAccessToken());
            });

            $manager->addDriver('facebook.app', function ($name, $config) {
                $oauth = array_get($config, 'oauth', []);

                $oauth['redirectUri'] = isset($oauth['redirectUri'])
                    ? value($oauth['redirectUri'])
                    : route('oauth.callback', [$name]);

                $provider   = new Facebook($oauth);
                $appId      = array_get($oauth, 'clientId');
                $appSecret  = array_get($oauth, 'clientSecret');
                $accessToken = new AccessToken([
                    'access_token' => "$appId|$appSecret",
                ]);
                return new UrlAccessToeknCredentials($provider, $accessToken);
            });
        });
    }
}
