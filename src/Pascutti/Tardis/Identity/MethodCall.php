<?php

namespace Pascutti\Tardis\Identity;

use InvalidArgumentException;

class MethodCall implements IdentityGenerator
{
    /**
     * @return string
     */
    public function createIdFor()
    {
        $arguments = func_get_args();
        $this->validateAmountOfArguments($arguments);

        $methodName = array_shift($arguments);
        $methodArguments = array_shift($arguments);
        $this->validateMethodArrguments($methodArguments);

        $serializedArguments = serialize($methodArguments);

        return sha1($methodName.$serializedArguments);
    }

    private function validateAmountOfArguments($arguments)
    {
        if (count($arguments) == 2) {
            return true;
        }

        $template = 'Expected 2 arguments to create an ID. %d given.';
        $message = sprintf($template, count($arguments));

        throw new InvalidArgumentException($message);
    }

    private function validateMethodArrguments($methodArguments)
    {
        if (is_array($methodArguments)) {
            return true;
        }

        $message = 'An argument list should be always an array.';
        throw new InvalidArgumentException($message);
    }
}
