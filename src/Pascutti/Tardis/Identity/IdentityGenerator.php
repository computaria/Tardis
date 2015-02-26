<?php

namespace Pascutti\Tardis\Identity;

interface IdentityGenerator
{
    /**
     * Given any number of arguments, create a unique identity
     * to them.
     * If the same arguments are presented again, the same identity
     * must be returned.
     *
     * @return string
     */
    public function createIdFor();
}
