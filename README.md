# Computaria\Tardis: Cache made easier

Cache stuff with almost no modification on your code:

```php
<?php

use Computaria\Tardis;
use MyApp\Repository;

$apc = new Doctrine\Common\Cache\ApcCache;
$cache = new Tardis\Factory($apc);

$slowRepository = new Repository\Country;
$fastRepository = $cache->callsFrom($slowRepository);

$brazil = $fastRepository->findByCode('BR');
```

Requirements: PHP >= 5.5

What happened above:

1. `$apc` is our cache storage.
2. `$cache` is a factory of [proxies][proxy], caching into `$apc`.
3. `$cache->callsFrom()` creates a new object which will cache and
   retrieve results from it.

## What cache storages are available?

[Many][doctrine-cache]:

- [Apc](http://php.net/apc)
- [Couchbase](http://www.couchbase.com/)
- Filesystem
- [Memcache](http://php.net/manual/en/book.memcached.php)
- [MongoDB](https://www.mongodb.org/)
- [Redis](http://redis.io/)
- [Riak](http://basho.com/riak/)
- [SQLite](https://sqlite.org/)
- [WinCache](http://php.net/wincache)
- [ZendData](http://files.zend.com/help/Zend-Server/content/data_cache_component.htm)
- [XCache](http://xcache.lighttpd.net/)

## Tardis limitations

- We can't cache final classes.
- We only cache public method calls.
- Methods which accept [non-serializable][serialize] arguments can't
  be cached automagically.

## FAQ

> Will the cache-enabled object Tardis create be of the same instance
  from the original object?

Yes. The object created will be from a new class extending your original
class.

> Isn't creating a class on the fly expensive and slow?

Yes, although not as slow as you might thing and it is only a once in a
time operation.

> How are you sure of what cache key to retrieve?

To create a cache key we use `Full\Class\Name::methodName` and serialize
all its arguments, in order to have a unique identity to that call.

If a method, of the same calss and with the same arguments, return different
things you are in trouble.

> How do I expire a cache entry?

You should use [Doctrine\Cache][doctrine-cache], or your own cache storage
configuration for that.

We are working on solutions for that. Have an idea? Help us!

[proxy]: http://sourcemaking.com/design_patterns/proxy
[doctrine-cache]: https://github.com/doctrine/cache
[serialize]: http://php.net/serialize
