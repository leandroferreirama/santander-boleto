<?php

use Santander\Santander;

require __DIR__."/../vendor/autoload.php";

$santander = new Santander(
    'client_id', 'client_secret', 'workspace', __DIR__.'/certificado.crt', __DIR__.'/certificado.key'
);
$response = $santander->alterarVencimento('convenio', 'nossoNumero', '2024-06-01');

var_dump($response->getStatus(), $response->getMensagem());