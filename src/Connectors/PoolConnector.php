<?php

declare(strict_types=1);

/**
 * This file is part of the Max package.
 *
 * (c) Cheng Yao <987861463@qq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Max\Redis\Connectors;

use Max\Context\Context;
use Max\Pool\Contracts\Poolable;
use Max\Pool\Contracts\PoolInterface;
use Max\Redis\RedisConfig;
use Swoole\Database\RedisPool;

class PoolConnector implements PoolInterface
{
    /**
     * @var RedisPool
     */
    protected RedisPool $pool;

    /**
     * @param RedisConfig $config
     */
    public function __construct(protected RedisConfig $config)
    {
        $this->open();
    }

    /**
     * 取
     *
     * @return \Max\Redis\Redis
     */
    public function get(): Poolable
    {
        // TOOD 多携程，多配置下会有问题
        $key = \Max\Redis\Redis::class;
        if (!Context::has($key)) {
            Context::put($key, new \Max\Redis\Redis($this, $this->pool->get()));
        }
        return Context::get($key);
    }

    public function open()
    {
        $this->pool = new RedisPool((new \Swoole\Database\RedisConfig())
            ->withHost($this->config->getHost())
            ->withPort($this->config->getPort())
            ->withAuth($this->config->getAuth())
            ->withDbIndex($this->config->getDatabase())
            ->withReadTimeout($this->config->getReadTimeout())
            ->withReserved($this->config->getReserved())
            ->withRetryInterval($this->config->getRetryInterval())
            ->withTimeout($this->config->getTimeout()),
            $this->config->getPoolSize()
        );
        if ($this->config->isAutofill()) {
            $this->pool->fill();
        }
    }

    public function close()
    {
        $this->pool->close();
    }

    public function gc()
    {
        // TODO: Implement gc() method.
    }

    /**
     * @param \Redis|Poolable|null $poolable
     *
     * @return void
     */
    public function release($poolable)
    {
        $this->pool->put($poolable);
    }
}
