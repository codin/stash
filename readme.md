# PSR6 Cache Implementation

Example

	use Codin\Stash\{
		Item,
		Adapter\RedisPool
        Adapter\Redis\ConnectionResolver
	};

    $resolver = new ConnectionResolver(static function () {
        return new Redis();
    });
	$pool = new RedisPool($resolver);

	$item = new Item('my-key', 'some data');
	$item->expiresAfter(3600); // 1 hour

	$pool->save($item);

	$item = $pool->getItem('my-key');
	echo $item->get(); // some data
	echo $item->isHit(); // true

## Testing

```
php bin/phpstan
php bin/phpspec run
```
