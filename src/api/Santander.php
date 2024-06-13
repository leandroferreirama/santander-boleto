<?php

namespace Santander;

use Exception;
use Santander\Boleto\BoletoResponse;
use Santander\Boleto\Boleto;

class Santander
{
    private string $client_id;
    private string $client_secret;
    private string $work_space;
    private string $certificate;
    private string $certificateKey;
    private $environment;
    private $authorizationToken;
    private $debug = false;

    public function __construct(string $client_id, string $client_secret, string $work_space, string $certificate, string $certificateKey)
    {
        $this->setClientId($client_id);
        $this->setClientSecret($client_secret);
        $this->setWorkSpace($work_space);
        $this->setCertificate($certificate);
        $this->setCertificateKey($certificateKey);
        $this->setEnvironment(Environment::production());
    }

    /**
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->client_id;
    }

    /**
     *
     * @param string $client_id
     */
    public function setClientId($client_id)
    {
        $this->client_id = (string) $client_id;

        return $this;
    }

    /**
     *
     * @return mixed
     */
    public function getClientSecret()
    {
        return $this->client_secret;
    }

    /**
     *
     * @param mixed $client_secret
     */
    public function setClientSecret($client_secret)
    {
        $this->client_secret = (string) $client_secret;

        return $this;
    }

    /**
     *
     * @return mixed
     */
    public function getWorkSapce()
    {
        return $this->work_space;
    }

    /**
     *
     * @param mixed $work_space
     */
    public function setWorkSpace($work_space)
    {
        $this->work_space = (string) $work_space;

        return $this;
    }

    public function getCertificate()
    {
        return $this->certificate;
    }

    public function setCertificate($certificate)
    {
        $this->certificate = (string) $certificate;

        return $this;
    }

    public function getCertificateKey()
    {
        return $this->certificateKey;
    }

    public function setCertificateKey($certificateKey)
    {
        $this->certificateKey = (string) $certificateKey;

        return $this;
    }

    public function getEnvironment(): Environment
    {
        return $this->environment;
    }

    public function setEnvironment(Environment $environment): self
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     *
     * @return mixed
     */
    public function getAuthorizationToken()
    {
        return $this->authorizationToken;
    }

    /**
     *
     * @param mixed $authorizationToken
     */
    public function setAuthorizationToken($authorizationToken)
    {
        $this->authorizationToken = (string) $authorizationToken;

        return $this;
    }

    /**
     *
     * @return bool|null
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     *
     * @param bool|null $debug
     */
    public function setDebug($debug = false)
    {
        $this->debug = $debug;

        return $this;
    }

    public function registrar(Boleto $boleto): BoletoResponse
    {
        $boletoResponse = new BoletoResponse;

        try{
            if ($this->debug) {
                print $boleto->toJSON();
            }

            $request = new Request($this);
            $response = $request->post($this, "{$this->getEnvironment()->getApiUrl()}/collection_bill_management/v2/workspaces/{$this->work_space}/bank_slips", $boleto->toJSON());

            // Add fields do not return in response
            $boletoResponse->mapperJson($boleto->toArray());
            // Add response fields
            $boletoResponse->mapperJson($response);
            $boletoResponse->setStatus(BaseResponse::STATUS_CONFIRMED);
            return $boletoResponse;
        } catch(Exception $e){
            return $this->generateErrorResponse($boletoResponse, $e);
        }
    }

    public function alterarVencimento($covenantCode, $bankNumber, $date): BoletoResponse
    {
        $json['covenantCode'] = $covenantCode;
        $json['bankNumber'] = $bankNumber;
        $json['dueDate'] = $date;

        return $this->update(json_encode($json));
    }

    public function baixar($covenantCode, $bankNumber): BoletoResponse
    {
        $json['covenantCode'] = $covenantCode;
        $json['bankNumber'] = $bankNumber;
        $json['operation'] = 'BAIXAR';
        
        return $this->update(json_encode($json));
    }

    public function prazoBaixa($covenantCode, $bankNumber, $days): BoletoResponse
    {
        $json['covenantCode'] = $covenantCode;
        $json['bankNumber'] = $bankNumber;
        $json['writeOffQuantityDays'] = $days;
        
        return $this->update(json_encode($json));
    }

    private function update($json): BoletoResponse
    {
        $boletoResponse = new BoletoResponse;

        try{
            if ($this->debug) {
                print $json;
            }

            $request = new Request($this);
            $response = $request->patch($this, "{$this->getEnvironment()->getApiUrl()}/collection_bill_management/v2/workspaces/{$this->work_space}/bank_slips", $json);

            // Add fields do not return in response
            $boletoResponse->mapperJson(json_decode($json));
            // Add response fields
            $boletoResponse->mapperJson($response);
            $boletoResponse->setStatus(BaseResponse::STATUS_CONFIRMED);
            return $boletoResponse;
        } catch(Exception $e){
            return $this->generateErrorResponse($boletoResponse, $e);
        }
    }

    private function generateErrorResponse(BaseResponse $baseResponse, $e)
    {
        $baseResponse->mapperJson(json_decode($e->getMessage(), true));        
        if (empty($baseResponse->getStatus())) {
            $baseResponse->setStatus(BaseResponse::STATUS_ERROR);
        }
        
        return $baseResponse;
    }

}