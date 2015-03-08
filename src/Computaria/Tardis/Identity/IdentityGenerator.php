<?php

namespace Computaria\Tardis\Identity;

interface IdentityGenerator
{
    /**
     * Given any number of arguments, create a unique identity
     * to them.
     * If the same arguments are presented again, the same identity
     * must be returned.
     *
     * @param   string  $methodName
     * @param   array   $methodArguments
     * @return  string
     */
    public function createIdFor($methodName, array $methodArguments);
}
