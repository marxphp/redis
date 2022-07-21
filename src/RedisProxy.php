<?php

declare(strict_types=1);

/**
 * This file is part of MaxPHP.
 *
 * @link     https://github.com/marxphp
 * @license  https://github.com/marxphp/max/blob/master/LICENSE
 */

namespace Max\Redis;

use Max\Redis\Contracts\ConnectorInterface;
use RedisException;

class RedisProxy
{
    public function __construct(
        protected ConnectorInterface $connector,
        protected $redis
    ) {
    }

    public function __destruct()
    {
        $this->connector->release($this->redis);
    }

    /**
     * @throws RedisException
     */
    public function __call(string $name, array $arguments)
    {
        try {
            return $this->redis->{$name}(...$arguments);
        } catch (RedisException $redisException) {
            $this->redis = null;
            throw $redisException;
        }
    }

    public function getRedis()
    {
        return $this->redis;
    }
}
