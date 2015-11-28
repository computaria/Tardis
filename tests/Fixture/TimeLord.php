<?php

namespace Computaria\Tardis\Tests\Fixture;

class TimeLord
{
    private $name = null;
    private $greet = null;

    public function __construct($name, $greet)
    {
        $this->name = $name;
        $this->greet = $greet;
    }

    public function salute($name = 'Impossible Girl')
    {
        $greeting = '%s %s! I\'m the %s!';

        return sprintf($greeting, $this->greet, $name, $this->name);
    }
}
