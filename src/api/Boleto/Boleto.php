<?php
namespace Santander\Boleto;

use Santander\Boleto\Key;
use Santander\Boleto\Payer;
use Santander\TraitEntity;

class Boleto implements \JsonSerializable
{
    use TraitEntity;

    #tipo de emissão
    const ETAPA_TESTE = "TESTE";
    const ETAPA_EFETIVO = "PRODUCAO";
    #tipo de documento
    const DOCUMENT_CPF = "CPF";
    const DOCUMENT_CNPJ = "CNPJ";
    #tipo de boleto
    const DUPLICATA_MERCANTIL = 'DUPLICATA_MERCANTIL';
    const DUPLICATA_SERVICO = 'DUPLICATA_SERVICO';

    public string $nsuDate;
    public string $issueDate;
    public string $paymentType;
    public Payer $payer;
    public Key $key;

    public function __construct(
        public string $nsuCode, public string $environment, public string $covenantCode, public int $bankNumber, public string $clientNumber, 
        public string $dueDate, public string $nominalValue, public string $documentKind, $documentType, $documentNumber, $name, $address, $neighborhood, $city, 
        $state, $zipCode, $type, $dictKey, public string $finePercentage = '0.00', public int $fineQuantityDays = 0, public string $interestPercentage = '0.00'
    )
    {
        $this->nsuDate = date("Y-m-d");
        $this->issueDate = date("Y-m-d");
        $this->paymentType = 'REGISTRO';
        $this->payer = new Payer($documentType, $documentNumber, $name, $address, $neighborhood, $city, $state, $zipCode);
        $this->key = new Key($type, $dictKey);
    }
    
}