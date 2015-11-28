<?php

namespace Computaria\Tardis\Identity;

class AlwaysTheSameValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function alway_returns_same_value_from_constructor()
    {
        $expectedValue = 14;
        $methodName = 'irrelevant';
        $methodArguments = [];
        $identityGenerator = new AlwaysTheSameValue($expectedValue);

        $this->assertEquals(
            $expectedValue,
            $identityGenerator->createIdFor($methodName, $methodArguments),
            'The "always" identity generator strategy should always return the same value.'
        );
    }
}
