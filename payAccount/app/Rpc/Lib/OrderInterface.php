<?php declare(strict_types=1);


namespace App\Rpc\Lib;

/**
 * Class UserInterface
 *
 * @since 2.0
 */
interface OrderInterface
{
    /**
     * @return array
     */
    public function update($data): array;

    public function confirmStatus($msg_id): array;
}