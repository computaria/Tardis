<?php

namespace Pascutti\Tardis\Tests\Unit\Identity;

use Pascutti\Tardis;

class AlwaysTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function alway_returns_same_value_from_constructor()
    {
        $expectedValue = 14;
        $valueWithoutId = 'not important value';
        $identityGenerator = new Tardis\Identity\Always($expectedValue);

        $this->assertEquals(
            $expectedValue,
            $identityGenerator->createIdFor($valueWithoutId),
            'The "always" identity generator strategy should always return the same value.'
        );
    }
}
