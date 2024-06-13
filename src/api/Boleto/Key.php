<?php

namespace Santander\Boleto;

use Santander\TraitEntity;

class Key implements \JsonSerializable
{
    use TraitEntity;

    public function __construct(
        public string $type,
        public string $dictKey,
    )
    {}
}