<?php

namespace Pascutti\Tardis\Identity;

class Always
{
    private $value = null;

    public function __construct($returnAlwaysThisValue)
    {
        $this->value = $returnAlwaysThisValue;
    }

    public function createIdFor($string)
    {
        return $this->value;
    }
}
