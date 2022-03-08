<?php

require __DIR__ . '/../vendor/autoload.php';

$storage = __DIR__ . '/../tmp';

if (!is_dir($storage)) {
    mkdir($storage);
}

$pool = new Codin\Stash\Adapter\FileSystemPool(
    $storage,
    'cache',
    [],
    new Codin\Stash\Adapter\FileSystem\IO(),
    new Codin\Stash\Adapter\FileSystem\PhpSerialiser(),
    new Codin\Stash\Adapter\FileSystem\KeyHasher('testing-', null)
);

$item = new Codin\Stash\Item('foo', 'bar');
$pool->save($item);

$item = $pool->getItem('foo');
echo 'Hit: foo '.($item->isHit()?'true':'false')."\n";

$item = $pool->getItem('bar');
echo 'Hit: bar '.($item->isHit()?'true':'false')."\n";
