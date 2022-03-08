<?php

declare(strict_types=1);

namespace spec\Codin\Stash\Adapter;

use Codin\Stash\Adapter\Redis\ConnectionManager;
use Codin\Stash\Item;
use DateTimeImmutable;
use PhpSpec\ObjectBehavior;
use Redis;

class RedisPoolSpec extends ObjectBehavior
{
    public function it_should_get_item_hit(ConnectionManager $cm, Redis $redis)
    {
        $this->beConstructedWith($cm);

        $cm->getConnection()->willReturn($redis);

        $redis->get('foo')->willReturn('bar');
        $redis->ttl('foo')->willReturn(null);

        $this->getItem('foo')->shouldReturnAnInstanceOf(Item::class);
    }

    public function it_should_get_item_miss(ConnectionManager $cm, Redis $redis)
    {
        $this->beConstructedWith($cm);

        $cm->getConnection()->willReturn($redis);

        $redis->get('foo')->willReturn(false);

        $this->getItem('foo')->shouldReturnAnInstanceOf(Item::class);
    }

    public function it_should_get_all_items(ConnectionManager $cm, Redis $redis)
    {
        $this->beConstructedWith($cm);

        $cm->getConnection()->willReturn($redis);

        $redis->keys('*')->willReturn(['foo']);

        $redis->get('foo')->willReturn('bar');
        $redis->ttl('foo')->willReturn(null);

        $this->getItems()[0]->shouldReturnAnInstanceOf(Item::class);
    }

    public function it_should_get_some_items(ConnectionManager $cm, Redis $redis)
    {
        $this->beConstructedWith($cm);

        $cm->getConnection()->willReturn($redis);

        $redis->get('foo')->willReturn('bar');
        $redis->ttl('foo')->willReturn(null);

        $redis->get('bar')->willReturn('baz');
        $redis->ttl('bar')->willReturn(null);

        $redis->get('baz')->willReturn('qux');
        $redis->ttl('baz')->willReturn(null);

        $result = $this->getItems(['foo', 'bar', 'baz']);
        $result->shouldHaveCount(3);
    }

    public function it_should_clear_all_items(ConnectionManager $cm, Redis $redis)
    {
        $this->beConstructedWith($cm);

        $cm->getConnection()->willReturn($redis);

        $redis->flushAll()->shouldBeCalled();

        $this->clear();
    }

    public function it_should_delete_a_item(ConnectionManager $cm, Redis $redis)
    {
        $this->beConstructedWith($cm);

        $cm->getConnection()->willReturn($redis);

        $redis->del(['foo'])->shouldBeCalled();

        $this->deleteItem('foo');
    }

    public function it_should_delete_many_items(ConnectionManager $cm, Redis $redis)
    {
        $this->beConstructedWith($cm);

        $cm->getConnection()->willReturn($redis);

        $redis->del(['foo', 'bar'])->shouldBeCalled();

        $this->deleteItems(['foo', 'bar']);
    }

    public function it_should_save_items(Item $item, ConnectionManager $cm, Redis $redis)
    {
        $this->beConstructedWith($cm);

        $cm->getConnection()->willReturn($redis);

        $item->getKey()->willReturn('foo');

        $item->get()->willReturn('bar');

        $expires = new DateTimeImmutable();

        $item->getExpires()->willReturn($expires);

        $redis->set('foo', 'bar')->shouldBeCalled();

        $redis->expireAt('foo', (int) $expires->format('U'))->shouldBeCalled();

        $this->save($item);
    }
}
