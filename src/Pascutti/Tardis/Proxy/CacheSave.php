<?php

namespace Pascutti\Tardis\Proxy;

use Doctrine\Common\Cache\Cache;
use Pascutti\Tardis\Identity\IdentityGenerator;

class CacheSave implements SufixInterceptor
{
    /**
     * @var Doctrine\Common\Cache\Cache
     */
    private $cacheAdapter = null;
    /**
     * @var Pascutti\Tardis\Identity\IdentityGenerator
     */
    private $identityGenerator = null;

    public function __construct(Cache $cache, IdentityGenerator $idGenerator)
    {
        $this->cacheAdapter = $cache;
        $this->identityGenerator = $idGenerator;
    }

    public function __invoke($proxy, $instance, $methodName, $methodArguments, $returnValue, &$returnEarly)
    {
        $cacheId = $this->identityGenerator->createIdFor($methodName, $methodArguments);
        if (false == $this->cacheAdapter->contains($cacheId)) {
            $this->cacheAdapter->save($cacheId, $returnValue);
        }

        return $returnValue;
    }
}
