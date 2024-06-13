<?php

use Santander\Boleto\Boleto;
use Santander\Santander;

require __DIR__."/../vendor/autoload.php";

$santander = new Santander(
    'client_id', 'client_secret', 'workSpace', __DIR__.'/certificado.crt', __DIR__.'/certificado.key'
);
$boleto = new Boleto(
    'Nsu', Boleto::ETAPA_EFETIVO, 'convenio', 'nossoNumero', 'seunumero', 'vencimento', 'valor', Boleto::DUPLICATA_SERVICO, Boleto::DOCUMENT_CPF,'cpf',
    'nome', 'Rua ', 'bairro', 'cidade', 'ESTADO', 'CEP', 'Tipo Chave pix', 'chevepix', 'multa', 0, 'juros'
);
$response = $santander->registrar($boleto);

var_dump($response->getStatus(), $response->getMensagem());