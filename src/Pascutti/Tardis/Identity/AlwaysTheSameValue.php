<?php

namespace Pascutti\Tardis\Identity;

class AlwaysTheSameValue implements IdentityGenerator
{
    private $value = null;

    public function __construct($returnAlwaysThisValue)
    {
        $this->value = $returnAlwaysThisValue;
    }

    public function createIdFor()
    {
        return $this->value;
    }
}
