<?php

declare(strict_types=1);

namespace spec\Codin\Stash\Adapter\FileSystem;

use PhpSpec\ObjectBehavior;

class IOSpec extends ObjectBehavior
{
    public function it_should_create_dir()
    {
        $dir = sprintf('/tmp/test-dir-%u', random_int(1, PHP_INT_MAX));
        $this->createDir($dir)->shouldReturn(true);
        rmdir($dir);
    }

    public function it_should_scan_dir()
    {
        $dir = sprintf('/tmp/test-dir-%u', random_int(1, PHP_INT_MAX));
        mkdir($dir);
        $file = sprintf('%s/test-file.testing', $dir);
        touch($file);

        $this->scanDir($dir, 'testing')->shouldReturn([$file]);

        unlink($file);
        rmdir($dir);
    }

    public function it_should_read_files()
    {
        $dir = sprintf('/tmp/test-dir-%u', random_int(1, PHP_INT_MAX));
        mkdir($dir);

        $file = sprintf('%s/test-file.testing', $dir);

        $this->readFile($file)->shouldReturn('');

        file_put_contents($file, 'foo');
        chmod($file, 0222);

        $this->readFile($file)->shouldReturn('');

        chmod($file, 0666);

        $this->readFile($file)->shouldReturn('foo');

        unlink($file);
        rmdir($dir);
    }

    public function it_should_write_files()
    {
        $dir = sprintf('/tmp/test-dir-%u', random_int(1, PHP_INT_MAX));
        mkdir($dir);

        $file = sprintf('%s/test-file.testing', $dir);

        chmod($dir, 0555);
        $this->writeFile($file, 'foo')->shouldReturn(0);

        chmod($dir, 0755);
        touch($file);
        chmod($file, 0444);
        $this->writeFile($file, 'foo')->shouldReturn(0);
        chmod($file, 0655);
        $this->writeFile($file, 'foo')->shouldReturn(3);

        unlink($file);
        rmdir($dir);
    }

    public function it_should_delete_files()
    {
        $dir = sprintf('/tmp/test-dir-%u', random_int(1, PHP_INT_MAX));
        mkdir($dir);

        $file = sprintf('%s/test-file.testing', $dir);
        $this->deleteFile($file)->shouldReturn(true);

        touch($file);

        chmod($file, 0444);
        $this->deleteFile($file)->shouldReturn(false);

        chmod($file, 0655);
        $this->deleteFile($file)->shouldReturn(true);

        rmdir($dir);
    }
}
