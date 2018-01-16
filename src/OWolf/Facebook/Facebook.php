<?php

namespace OWolf\Facebook;

use League\OAuth2\Client\Provider\Facebook as BaseProvider;

class Facebook extends BaseProvider
{
    /**
     * @return string
     */
    public function getGraphApiVersion()
    {
        return $this->graphApiVersion;
    }

    /**
     * @return string
     */
    public function getGraphUrl()
    {
        return static::BASE_GRAPH_URL . $this->graphApiVersion . '/';
    }
}
