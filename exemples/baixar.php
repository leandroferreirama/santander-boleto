<?php

use Santander\Santander;

require __DIR__."/../vendor/autoload.php";

$santander = new Santander(
    'client_id', 'client_secret', 'workspace', __DIR__.'/certificado.crt', __DIR__.'/certificado.key'
);
$response = $santander->baixar('convenio', 'nossoNumero');

var_dump($response->getStatus(), $response->getMensagem());