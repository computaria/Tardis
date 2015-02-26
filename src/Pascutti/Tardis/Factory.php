<?php

namespace Pascutti\Tardis;

use InvalidArgumentException;
use Doctrine\Common\Cache\Cache;
use ProxyManager\Factory as ProxyManager;

class Factory
{
    /**
     * @var ProxyManager\Factory\AbstractBaseFactory
     */
    private $proxyFactory = null;
    /**
     * @var Doctrine\Common\Cache\Cache
     */
    private $cacheAdapter = null;
    /**
     * @var Pascutti\Tardis\Identity\IdentityGenerator
     */
    private $idGenerator = null;

    public function __construct(
        ProxyManager\AbstractBaseFactory $proxyFactory,
        Cache $cacheAdapter,
        Identity\IdentityGenerator $idGenerator
    ) {
        $this->proxyFactory = $proxyFactory;
        $this->cacheAdapter = $cacheAdapter;
        $this->idGenerator = $idGenerator;
    }

    public function cacheCallsFrom($object)
    {
        if (false === is_object($object)) {
            $message = 'Only calls to object can be proxied.';
            throw new InvalidArgumentException($message);
        }

        $cache = $this->cacheAdapter;
        $idGenerator = $this->idGenerator;
        $retrieveFromCache = function($proxy, $real, $method, $arguments, &$return) use ($cache, $idGenerator) {
            $cacheId = $idGenerator->createIdFor($method, $arguments);
            if ($cache->contains($cacheId)) {
                $return = true;

                return $cache->fetch($cacheId);
            }
        };

        $saveIntoCache = function ($proxy, $instance, $method, $params, $returnValue, &$returnEarly) use ($cache, $idGenerator) {
            $cacheId = $idGenerator->createIdFor($method, $params);
            $cache->save($cacheId, $returnValue);
        };

        return $this->proxyFactory->createProxy(
            $object,
            ['salute' => $retrieveFromCache],
            ['salute' => $saveIntoCache]
        );
    }
}
