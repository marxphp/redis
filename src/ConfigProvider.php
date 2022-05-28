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

namespace Max\Redis;

class ConfigProvider
{
    public function __invoke()
    {
        return [
            'publish' => [
                [
                    'name'        => 'redis',
                    'source'      => __DIR__ . '/../publish/redis.php',
                    'destination' => dirname(__DIR__, 4) . '/config/redis.php',
                ]
            ],
        ];
    }
}
