<?php declare(strict_types=1);

namespace Six\Rabbit;
/**
 * Class ConnectionInterface
 *
 * @since 2.0
 */
interface ConnectionInterface
{
    /**
     * @param string $data
     *
     * @return bool
     */
    public function publish(string $data): bool;

    /**
     * @return string|bool
     */
    public function consumer();
}