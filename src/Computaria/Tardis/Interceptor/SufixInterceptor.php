<?php

namespace Computaria\Tardis\Interceptor;

/**
 * Actions to be called after the method call on the proxied object
 * should implement this interface.
 */
interface SufixInterceptor
{
    /**
    * @var object $proxy            The proxy that intercepted the method call
    * @var object $instance         The wrapped instance within the proxy
    * @var string $methodName       Name of the called method
    * @var array  $methodArguments  Sorted array of parameters passed to the intercepted
    *                               method, indexed by parameter name
    * @var mixed  $returnValue      The return value of the intercepted method
    * @var bool   $returnEarly      Flag to tell the proxy to return early, returning the interceptor's
    *                               return value instead of the value produced by the method
    * @return mixed
    */
    public function __invoke($proxy, $instance, $methodName, $methodArguments, $returnValue, &$returnEarly);
}
