<?php

namespace Santander\Boleto;

use Santander\BaseResponse;

class BoletoResponse extends BaseResponse
{
    protected $qrCodePix;
    protected $txId;

    public function getTxid()
    {
        return $this->txId;
    }

    public function getPixCopiaECola()
    {
        return $this->qrCodePix;
    }
}