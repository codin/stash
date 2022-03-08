<?php

declare(strict_types=1);

namespace Codin\Stash\Exceptions;

use Exception;
use Psr\Cache\CacheException;

class SerialisationError extends Exception implements CacheException
{
}
