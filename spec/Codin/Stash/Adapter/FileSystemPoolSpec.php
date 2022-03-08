<?php

declare(strict_types=1);

namespace spec\Codin\Stash\Adapter;

use Codin\Stash\Adapter\FileSystem\IO;
use Codin\Stash\Adapter\FileSystem\KeyHasher;
use Codin\Stash\Adapter\FileSystem\Serialiser;
use Codin\Stash\Item;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument\Token\TypeToken;

class FileSystemPoolSpec extends ObjectBehavior
{
    public function it_should_get_item(Item $item, IO $io, Serialiser $serialiser)
    {
        $io->createDir('/some/path')->shouldBeCalled();

        $io->readFile(new TypeToken('string'))->shouldBeCalled()->willReturn('foo');

        $serialiser->decode('foo')->shouldBeCalled()->willReturn($item);

        $item->hasExpired()->shouldBeCalled()->willReturn(false);

        $this->beConstructedWith('/some/path', 'my-extension', [], $io, $serialiser);

        $this->getItem('foo')->shouldReturn($item);
    }

    public function it_should_get_item_miss(Item $item, IO $io, Serialiser $serialiser)
    {
        $io->createDir('/some/path')->shouldBeCalled();

        $io->readFile(new TypeToken('string'))->shouldBeCalled()->willReturn('');

        $this->beConstructedWith('/some/path', 'my-extension', [], $io, $serialiser);

        $this->getItem('foo')->shouldReturnAnInstanceOf(Item::class);
    }

    public function it_should_get_all_items(Item $item, IO $io, Serialiser $serialiser)
    {
        $io->createDir('/some/path')->shouldBeCalled();

        $io->scanDir('/some/path', 'my-extension')->shouldBeCalled()->willReturn(['/some/path/testing.my-extension']);

        $io->readFile('/some/path/testing.my-extension')->shouldBeCalled()->willReturn('foo');

        $io->readFile(new TypeToken('string'))->shouldBeCalled()->willReturn('foo');

        $serialiser->decode('foo')->shouldBeCalled()->willReturn($item);

        $item->getKey()->shouldBeCalled()->willReturn('foo');

        $item->hasExpired()->shouldBeCalled()->willReturn(false);

        $this->beConstructedWith('/some/path', 'my-extension', [], $io, $serialiser);

        $this->getItems()->shouldReturn([$item]);
    }

    public function it_should_get_many_items(Item $item, IO $io, Serialiser $serialiser)
    {
        $io->createDir('/some/path')->shouldBeCalled();

        $io->readFile(new TypeToken('string'))->shouldBeCalled()->willReturn('foo');

        $serialiser->decode('foo')->shouldBeCalled()->willReturn($item);

        $item->hasExpired()->shouldBeCalled()->willReturn(false);

        $this->beConstructedWith('/some/path', 'my-extension', [], $io, $serialiser);

        $this->getItems(['foo'])->shouldReturn([$item]);
    }

    public function it_should_delete_item(Item $item, IO $io, Serialiser $serialiser)
    {
        $io->createDir('/some/path')->shouldBeCalled();

        $io->deleteFile(new TypeToken('string'))->shouldBeCalled();

        $this->beConstructedWith('/some/path', 'my-extension', [], $io, $serialiser);

        $this->deleteItem('foo');
    }

    public function it_should_delete_many_items(Item $item, IO $io, Serialiser $serialiser)
    {
        $io->createDir('/some/path')->shouldBeCalled();

        $io->deleteFile(new TypeToken('string'))->shouldBeCalled();

        $this->beConstructedWith('/some/path', 'my-extension', [], $io, $serialiser);

        $this->deleteItems(['foo', 'bar']);
    }

    public function it_should_clear_items(Item $item, IO $io, Serialiser $serialiser)
    {
        $io->createDir('/some/path')->shouldBeCalled();

        $io->scanDir('/some/path', 'my-extension')->shouldBeCalled()->willReturn(['/some/path/testing.my-extension']);

        $io->readFile('/some/path/testing.my-extension')->shouldBeCalled()->willReturn('foo');

        $serialiser->decode('foo')->shouldBeCalled()->willReturn($item);

        $item->getKey()->shouldBeCalled()->willReturn('foo');

        $io->deleteFile(new TypeToken('string'))->shouldBeCalled();

        $this->beConstructedWith('/some/path', 'my-extension', [], $io, $serialiser);

        $this->clear();
    }

    public function it_should_save_items(Item $item, IO $io, Serialiser $serialiser, KeyHasher $keyHasher)
    {
        $io->createDir('/some/path')->shouldBeCalled();

        $keyHasher->create('foo')->shouldBeCalled()->willReturn('foo_hash');

        $io->writeFile('/some/path/foo_hash.my-extension', 'bar')->shouldBeCalled()->willReturn(3);

        $serialiser->encode($item)->shouldBeCalled()->willReturn('bar');

        $item->getKey()->shouldBeCalled()->willReturn('foo');

        $this->beConstructedWith('/some/path', 'my-extension', [], $io, $serialiser, $keyHasher);

        $this->save($item);
    }
}
