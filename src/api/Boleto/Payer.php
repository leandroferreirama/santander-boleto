<?php

namespace Santander\Boleto;

use Santander\TraitEntity;

class Payer implements \JsonSerializable
{
    use TraitEntity;

    public function __construct(
        public string $documentType,
        public string $documentNumber,
        public string $name,
        public string $address,
        public string $neighborhood,
        public string $city,
        public string $state,
        public string $zipCode,
    )
    {}
}