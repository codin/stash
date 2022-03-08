<?php

declare(strict_types=1);

namespace spec\Codin\Stash\Adapter\Redis;

use PhpSpec\ObjectBehavior;
use Redis;

class ConnectionResolverSpec extends ObjectBehavior
{
    public function it_should_resolve_redis(Redis $redis)
    {
        $this->beConstructedWith(static function () use ($redis): Redis {
            return $redis->getWrappedObject();
        });

        $this->getConnection()->shouldReturn($redis);
    }
}
