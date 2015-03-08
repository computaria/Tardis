<?php

namespace Computaria\Tardis;

use InvalidArgumentException;
use Doctrine\Common\Cache\Cache;
use ProxyManager\Factory as ProxyManager;

class Factory
{
    /**
     * @var Computaria\Tardis\Proxy\PublicMethods
     */
    private $proxyFactory = null;

    public function __construct(
        ProxyManager\AbstractBaseFactory $proxyFactory,
        Cache $cacheAdapter,
        Identity\IdentityGenerator $idGenerator
    ) {
        $prefixIntercetor = new Interceptor\CacheFetch($cacheAdapter, $idGenerator);
        $sufixInterceptor = new Interceptor\CacheSave($cacheAdapter, $idGenerator);
        $this->proxyFactory = new Proxy\PublicMethods($proxyFactory, $prefixIntercetor, $sufixInterceptor);
    }

    public function cacheCallsFrom($object)
    {
        if (false === is_object($object)) {
            $message = 'Only calls to object can be proxied.';
            throw new InvalidArgumentException($message);
        }

        return $this->proxyFactory->createProxy($object);
    }
}
