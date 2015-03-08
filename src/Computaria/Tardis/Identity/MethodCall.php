<?php

namespace Computaria\Tardis\Identity;

use InvalidArgumentException;

class MethodCall implements IdentityGenerator
{
    /**
     * @inherit
     */
    public function createIdFor($methodName, array $methodArguments)
    {
        $serializedArguments = serialize($methodArguments);

        return sha1($methodName.$serializedArguments);
    }
}
