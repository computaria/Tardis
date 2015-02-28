<?php

namespace Computaria\Tardis;

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
     * @var Computaria\Tardis\Identity\IdentityGenerator
     */
    private $idGenerator = null;
    /**
     * @var Computaria\Tardis\Proxy\SufixInterceptor
     */
    private $sufixInterceptor = null;
    /**
     * @var Computaria\Tardis\Proxy\PrefixInterceptor
     */
    private $prefixIntercetor = null;

    public function __construct(
        ProxyManager\AbstractBaseFactory $proxyFactory,
        Cache $cacheAdapter,
        Identity\IdentityGenerator $idGenerator
    ) {
        $this->proxyFactory = $proxyFactory;
        $this->cacheAdapter = $cacheAdapter;
        $this->idGenerator = $idGenerator;

        $this->prefixIntercetor = new Proxy\CacheFetch($this->cacheAdapter, $this->idGenerator);
        $this->sufixInterceptor = new Proxy\CacheSave($this->cacheAdapter, $this->idGenerator);
    }

    public function cacheCallsFrom($object)
    {
        if (false === is_object($object)) {
            $message = 'Only calls to object can be proxied.';
            throw new InvalidArgumentException($message);
        }

        return $this->proxyFactory->createProxy(
            $object,
            ['salute' => $this->prefixIntercetor],
            ['salute' => $this->sufixInterceptor]
        );
    }
}
