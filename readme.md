# PSR6 Cache Implementation

![version](https://img.shields.io/github/v/tag/codin/stash)
![workflow](https://img.shields.io/github/workflow/status/codin/stash/Composer)
![license](https://img.shields.io/github/license/codin/stash)

Example

```php
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
```

## Testing

```shell
php bin/phpstan
php bin/phpspec run
```
