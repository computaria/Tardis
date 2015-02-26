<?php

namespace Pascutti\Tardis\Identity;

class AlwaysTheSameValueTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function alway_returns_same_value_from_constructor()
    {
        $expectedValue = 14;
        $identityGenerator = new AlwaysTheSameValue($expectedValue);

        $this->assertEquals(
            $expectedValue,
            $identityGenerator->createIdFor(1, 2, 3, 4),
            'The "always" identity generator strategy should always return the same value.'
        );
    }
}
