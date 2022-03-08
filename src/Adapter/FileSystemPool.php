<?php

declare(strict_types=1);

namespace Codin\Stash\Adapter;

use Codin\Stash\Item;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class FileSystemPool extends AbstractPool implements CacheItemPoolInterface
{
    protected string $path;

    protected string $extension;

    protected FileSystem\IO $io;

    protected FileSystem\Serialiser $serialiser;

    protected FileSystem\KeyHasher $keyHasher;

    /**
     * @param string $path
     * @param string $extension
     * @param array<CacheItemInterface> $deferred
     * @param FileSystem\IO|null $io
     * @param FileSystem\Serialiser|null $serialiser
     */
    public function __construct(
        string $path,
        string $extension = 'cache',
        array $deferred = [],
        ?FileSystem\IO $io = null,
        ?FileSystem\Serialiser $serialiser = null,
        ?FileSystem\KeyHasher $keyHasher = null
    ) {
        $this->path = $path;
        $this->extension = $extension;
        $this->deferred = $deferred;
        $this->io = $io ?? new FileSystem\IO();
        $this->serialiser = $serialiser ?? new FileSystem\PhpSerialiser();
        $this->keyHasher = $keyHasher ?? new FileSystem\KeyHasher();

        $this->io->createDir($path);
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($key)
    {
        $path = $this->getFilepath($key);

        $item = $this->getData($path);

        if (!$item instanceof Item || $item->hasExpired()) {
            return $this->createItem($key, null, false);
        }

        return $item;
    }

    /**
     * @param array<string> $keys
     * @return array<CacheItemInterface>
     */
    public function getItems(array $keys = [])
    {
        if (!\count($keys)) {
            $keys = $this->getKeys();
        }

        $values = [];

        foreach ($keys as $key) {
            $values[] = $this->getItem($key);
        }

        return $values;
    }

    /**
     * @return bool
     */
    public function deleteItem($key)
    {
        $filepath = $this->getFilepath($key);

        $this->io->deleteFile($filepath);

        return true;
    }

    /**
     * @return bool
     */
    public function clear()
    {
        $keys = $this->getKeys();

        $this->deleteItems($keys);

        return true;
    }

    /**
     * @return bool
     */
    public function save(CacheItemInterface $item)
    {
        $contents = $this->serialiser->encode($item);

        $filepath = $this->getFilepath($item->getKey());

        return $this->io->writeFile($filepath, $contents) > 0;
    }

    /**
     * Get file path from key hash.
     */
    protected function getFilepath(string $key): string
    {
        $hash = $this->keyHasher->create($key);

        return \sprintf('%s/%s.%s', $this->path, $hash, $this->extension);
    }

    /**
     * @return array<string>
     */
    protected function getKeys(): array
    {
        $keys = [];

        foreach ($this->io->scanDir($this->path, $this->extension) as $filepath) {
            if ($item = $this->getData($filepath)) {
                $keys[] = $item->getKey();
            }
        }

        return $keys;
    }

    protected function getData(string $filepath): ?CacheItemInterface
    {
        if ($contents = $this->io->readFile($filepath)) {
            return $this->serialiser->decode($contents);
        }

        return null;
    }
}
