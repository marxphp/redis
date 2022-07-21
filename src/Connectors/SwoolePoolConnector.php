<?php

declare(strict_types=1);

/**
 * This file is part of MaxPHP.
 *
 * @link     https://github.com/marxphp
 * @license  https://github.com/marxphp/max/blob/master/LICENSE
 */

namespace Max\Redis\Connectors;

use Max\Redis\Contracts\ConnectorInterface;
use Max\Redis\RedisProxy;
use Swoole\Database\RedisPool;

class SwoolePoolConnector implements ConnectorInterface
{
    protected RedisPool $pool;

    public function __construct(
        protected string $host = '127.0.0.1',
        protected int $port = 6379,
        protected float $timeout = 0.0,
        protected string $reserved = '',
        protected int $retryInterval = 0,
        protected float $readTimeout = 0.0,
        protected string $auth = '',
        protected int $database = 0,
        protected int $poolSize = 32,
    ) {
        $redisConfig = (new \Swoole\Database\RedisConfig())
            ->withHost($this->host)
            ->withPort($this->port)
            ->withTimeout($this->timeout)
            ->withReadTimeout($this->readTimeout)
            ->withRetryInterval($this->retryInterval)
            ->withReserved($this->reserved)
            ->withDbIndex($this->database)
            ->withAuth($this->auth);
        $this->pool  = new RedisPool($redisConfig, $this->poolSize);
    }

    public function get()
    {
        return new RedisProxy($this, $this->pool->get());
    }

    public function release($redis)
    {
        $this->pool->put($redis);
    }
}
