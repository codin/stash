<?php

declare(strict_types=1);

namespace spec\Codin\Stash\Adapter;

use Codin\Stash\Item;
use PhpSpec\ObjectBehavior;

class MemoryPoolSpec extends ObjectBehavior
{
    public function it_should_get_item(Item $item)
    {
        $this->beConstructedWith(['foo' => $item]);

        $this->getItem('foo')->shouldReturn($item);
    }

    public function it_should_check_item(Item $item)
    {
        $item->isHit()->shouldBeCalled()->willReturn(true);

        $this->beConstructedWith(['foo' => $item]);

        $this->hasItem('foo')->shouldReturn(true);
    }

    public function it_should_get_empty_item()
    {
        $this->getItem('foo')->shouldReturnAnInstanceOf(Item::class);
    }

    public function it_should_get_empty_items(Item $item)
    {
        $this->beConstructedWith(['foo' => $item]);

        $this->getItems()->shouldReturn([$item]);
    }

    public function it_should_delete_item(Item $item)
    {
        $this->beConstructedWith(['foo' => $item]);

        $this->deleteItem('foo');

        $this->getItem('foo')->shouldReturnAnInstanceOf(Item::class);
    }

    public function it_should_clear_items(Item $item)
    {
        $this->beConstructedWith(['foo' => $item]);

        $this->clear();

        $this->getItems()->shouldReturn([]);
    }

    public function it_should_save_items(Item $item)
    {
        $item->getKey()->willReturn('foo');

        $this->save($item);

        $this->getItem('foo')->shouldReturn($item);
    }

    public function it_should_commit_deferred_items(Item $item)
    {
        $item->getKey()->willReturn('foo');

        $this->saveDeferred($item);

        $this->commit();
    }
}
