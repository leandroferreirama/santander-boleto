<?php
namespace Santander;

/**
 * Class Environment
 *
 * @package Santander
 */
class Environment
{

    private $apiUrl;
    
    /**
     *
     * @param string $api
     *
     */
    private function __construct($apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }

    /**
     *
     * @return Environment
     */
    public static function production()
    {
        return new Environment(
            'https://trust-open.api.santander.com.br'
        );
    }

    /**
     *
     * @return Environment
     */
    public static function homolog()
    {
        return new Environment(
            'https://trust-open-h.api.santander.com.br'
        );
    }

    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }
}