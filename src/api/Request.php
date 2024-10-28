<?php

namespace Santander;

use Exception;
use Santander\Exception\SantanderException;

class Request
{

    public const CURL_TYPE_POST = "POST";
    public const CURL_TYPE_PUT = "PUT";
    public const CURL_TYPE_GET = "GET";
    public const CURL_TYPE_DELETE = "DELETE";

    /**
     * Request constructor.
     *
     * @param Santander $credentials
     * TODO create local variable to $credentials
     */
    public function __construct(Santander $credentials)
    {
        if (! $credentials->getAuthorizationToken()) {
            $this->auth($credentials);
        }
    }

    public function auth(Santander $credentials)
    {
        #Inicio a autenticação
        $endpoint = $credentials->getEnvironment()->getApiUrl().'/auth/oauth/v2/token';
        $headers = [
            'Content-Type: application/x-www-form-urlencoded'
        ];

        $request = [
            'grant_type' => 'client_credentials',
            'client_id' => $credentials->getClientId(),
            'client_secret' => $credentials->getClientSecret()
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $endpoint,
            CURLOPT_PORT => 443,
            CURLOPT_VERBOSE => 0,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query($request),
            CURLOPT_SSLCERT => $credentials->getCertificate(),
            CURLOPT_SSLKEY => $credentials->getCertificateKey(),
            CURLOPT_CAINFO => $credentials->getCertificate(),
            CURLOPT_SSL_VERIFYPEER => 0
        ]);

        try {
            #verifico se existe o certificado no servidor
            if(!file_exists($credentials->getCertificate())){
                $json['message'] = "[ERRO] Não localizei o certificado no servidor!";
                throw new Exception(json_encode($json));
            }
            if(!file_exists($credentials->getCertificateKey())){
                $json['message'] = "[ERRO] Não localizei a chave do certificado no servidor!";
                throw new Exception(json_encode($json));
            }

            $response = curl_exec($curl);
        } catch (Exception $e) {
            throw new SantanderException($e->getMessage(), 100);
        }
        // Verify error
        if ($response === false) {
            $errorMessage = curl_error($curl);
        }

        $statusCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($statusCode >= 400) {
            // TODO see what it means code 100
            throw new SantanderException($response, 100);
        }
        // Status code 204 don't have content. That means $response will be always false
        // Provides a custom content for $response to avoid error in the next if logic
        if ($statusCode === 204) {
            return [
                'status_code' => 204
            ];
        }

        if (! $response) {
            throw new SantanderException("Empty response, curl_error: $errorMessage", $statusCode);
        }

        $responseDecode = json_decode($response, true);

        if (is_array($responseDecode) && isset($responseDecode['error'])) {
            throw new SantanderException($responseDecode['error_description'], 100);
        }

        $credentials->setAuthorizationToken($responseDecode["access_token"]);

        return $credentials;
    }

    public function get(Santander $credentials, $fullUrl, $params = null)
    {
        return $this->send($credentials, $fullUrl, self::CURL_TYPE_GET, $params);
    }

    public function post(Santander $credentials, $fullUrl, $params)
    {
        return $this->send($credentials, $fullUrl, self::CURL_TYPE_POST, $params);
    }

    public function patch(Santander $credentials, $fullUrl, $params = null)
    {
        return $this->send($credentials, $fullUrl, 'PATCH', $params);
    }

    private function send(Santander $credentials, $fullUrl, $method, $jsonBody = null)
    {
        $curl = curl_init($fullUrl);

        $defaultCurlOptions = array(
            CURLOPT_CONNECTTIMEOUT => 60,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_VERBOSE => 0,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json; charset=utf-8'
            ),
            CURLOPT_SSLCERT => $credentials->getCertificate(),
            CURLOPT_SSLKEY => $credentials->getCertificateKey(),
            CURLOPT_CAINFO => $credentials->getCertificate(),
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => 0
        );

        $defaultCurlOptions[CURLOPT_HTTPHEADER][] = 'Authorization: Bearer ' . $credentials->getAuthorizationToken();
        $defaultCurlOptions[CURLOPT_HTTPHEADER][] = 'X-Application-Key: ' . $credentials->getClientId();

        // Add custom method
        if (in_array($method, [
            self::CURL_TYPE_DELETE,
            self::CURL_TYPE_PUT,
            self::CURL_TYPE_GET,
            'PATCH'
        ])) {
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        }

        // Add body params
        if (! empty($jsonBody)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, is_string($jsonBody) ? $jsonBody : json_encode($jsonBody));
        }

        curl_setopt_array($curl, $defaultCurlOptions);

        $response = null;
        $errorMessage = '';

        try {
            $response = curl_exec($curl);
        } catch (Exception $e) {
            throw new SantanderException("Request Exception, error: {$e->getMessage()}", 100);
        }
        // Verify error
        if ($response === false) {
            $errorMessage = curl_error($curl);
        }

        $statusCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($statusCode >= 400) {
            // TODO see what it means code 100
            throw new SantanderException($response, 100);
        }

        $responseDecode = json_decode($response, true);
        if(is_null($responseDecode)){
            $responseDecode = ['status_code' => $statusCode];
        } else {
            array_push($responseDecode, ['status_code' => $statusCode]);
        }
        
        return $responseDecode;
    }
}
