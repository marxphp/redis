<?php

declare(strict_types=1);

/**
 * This file is part of MaxPHP.
 *
 * @link     https://github.com/marxphp
 * @license  https://github.com/marxphp/max/blob/master/LICENSE
 */

namespace Max\Redis;

use ArrayObject;
use InvalidArgumentException;
use Max\Config\Contracts\ConfigInterface;

class RedisManager
{
    protected string       $defaultConnection;

    protected array        $config      = [];

    protected ?ArrayObject $connections = null;

    public function __construct(ConfigInterface $config)
    {
        $config                  = $config->get('redis');
        $this->defaultConnection = $config['default'];
        $this->config            = $config['connections'] ?? [];
        $this->connections       = new ArrayObject();
    }

    public function connection(?string $name = null): Redis
    {
        $name ??= $this->defaultConnection;
        if (! $this->connections->offsetExists($name)) {
            if (! isset($this->config[$name])) {
                throw new InvalidArgumentException('没有相关Redis连接');
            }
            $config          = $this->config[$name];
            $connector       = $config['connector'];
            $options         = $config['options'];
            $options['name'] = $name;
            $this->connections->offsetSet($name, new $connector(new RedisConfig($options)));
        }

        return new Redis($this->connections->offsetGet($name));
    }
}
