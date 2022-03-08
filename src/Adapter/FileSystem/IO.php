<?php

declare(strict_types=1);

namespace Codin\Stash\Adapter\FileSystem;

use FilesystemIterator;
use GlobIterator;
use SplFileInfo;

class IO
{
    public function createDir(string $path, int $permissions = 0700): bool
    {
        if (!\is_dir($path)) {
            \mkdir($path, $permissions, true);
        }

        \chmod($path, $permissions);

        return \is_dir($path);
    }

    /**
     * @return array<string>
     */
    public function scanDir(string $path, string $extension): array
    {
        $it = new GlobIterator(\sprintf('%s/*.%s', $path, $extension), FilesystemIterator::SKIP_DOTS);
        $files = [];

        foreach ($it as $fileInfo) {
            $files[] = $fileInfo instanceof SplFileInfo ? $fileInfo->getPathname() : $fileInfo;
        }

        return $files;
    }

    public function readFile(string $filepath): string
    {
        if (!\is_file($filepath)) {
            return '';
        }

        if (!\is_readable($filepath)) {
            return '';
        }

        $contents = \file_get_contents($filepath);

        return false === $contents ? '' : $contents;
    }

    public function writeFile(string $filepath, string $contents): int
    {
        if (!\is_writable(dirname($filepath))) {
            return 0;
        }

        if (\is_file($filepath) && !\is_writable($filepath)) {
            return 0;
        }

        $bytes = \file_put_contents($filepath, $contents, LOCK_EX);

        return false === $bytes ? 0 : $bytes;
    }

    public function deleteFile(string $filepath): bool
    {
        if (!\is_file($filepath)) {
            return true;
        }

        if (!\is_writable($filepath)) {
            return false;
        }

        return \unlink($filepath);
    }
}
