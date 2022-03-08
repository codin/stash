<?php

declare(strict_types=1);

namespace Codin\Stash\Adapter\FileSystem;

class KeyHasher
{
    protected string $prefix;

    protected ?string $hashAlgo;

    public function __construct(string $prefix = '', ?string $hashAlgo = 'sha256')
    {
        $this->prefix = $prefix;
        $this->hashAlgo = $hashAlgo;
    }

    public function create(string $key): string
    {
        if (\is_string($this->hashAlgo) && \in_array($this->hashAlgo, \hash_algos())) {
            $key = \hash($this->hashAlgo, $key);
        }

        return $this->prefix.$key;
    }
}
