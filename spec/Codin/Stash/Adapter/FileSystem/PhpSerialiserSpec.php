<?php

declare(strict_types=1);

namespace spec\Codin\Stash\Adapter\FileSystem;

use Codin\Stash\Exceptions\SerialisationError;
use Codin\Stash\Item;
use PhpSpec\ObjectBehavior;

class PhpSerialiserSpec extends ObjectBehavior
{
    public function it_should_encode_items()
    {
        $item = new Item('foo', 'bar');

        $expected = serialize($item);

        $this->encode($item)->shouldReturn($expected);
    }

    public function it_should_decode_items()
    {
        $item = new Item('foo', 'bar');

        $encoded = serialize($item);

        $this->decode($encoded)->shouldReturnAnInstanceOf(Item::class);
    }

    public function it_should_catch_decoded_unknowns()
    {
        $item = new \StdClass();

        $encoded = serialize($item);

        $this->shouldThrow(SerialisationError::class)->duringDecode($encoded);
    }
}
