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
    public int $bankNumber;
    public string $nsuCode;

    public function __construct(
        public string $environment, public string $covenantCode, $bankNumber, public string $clientNumber, 
        public string $dueDate, public string $nominalValue, public string $documentKind, $documentType, $documentNumber, $name, $address, $neighborhood, $city, 
        $state, $zipCode, $type, $dictKey, public string $finePercentage = '0.00', public int $fineQuantityDays = 0, public string $interestPercentage = '0.00'
    )
    {
        $this->nsuCode = self::bankNumberDv($bankNumber);
        $this->bankNumber = self::bankNumberDv($bankNumber);
        $this->nsuDate = date("Y-m-d");
        $this->issueDate = date("Y-m-d");
        $this->paymentType = 'REGISTRO';
        $this->payer = new Payer($documentType, $documentNumber, $name, $address, $neighborhood, $city, $state, $zipCode);
        $this->key = new Key($type, $dictKey);
    }

    private static function modulo11($num, $base=9, $r=0)
    {
        $soma = 0;
        $fator = 2;

        /* Separacao dos numeros */
        for ($i = strlen($num); $i > 0; $i--) {
            // pega cada numero isoladamente
            $numeros[$i] = substr($num,$i-1,1);
            // Efetua multiplicacao do numero pelo falor
            $parcial[$i] = $numeros[$i] * $fator;
            // Soma dos digitos
            $soma += $parcial[$i];
            if ($fator == $base) {
                // restaura fator de multiplicacao para 2 
                $fator = 1;
            }
            $fator++;
        }

        /* Calculo do modulo 11 */
        if ($r == 0) {
            $soma *= 10;
            $digito = $soma % 11;
            if ($digito == 10) {
                $digito = 0;
            }
            return $digito;
        } elseif ($r == 1){
            $resto = $soma % 11;
            return $resto;
        }
    }
    
    public static function bankNumberDv($bankNumber)
    {
        $resto = self::modulo11($bankNumber, 9, 1);
        $digito = 11 - $resto;
        if($digito == 10 || $digito == 11){
            $digito = 0;
        }
        return $bankNumber.$digito;
    }
}