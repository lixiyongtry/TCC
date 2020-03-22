<?php declare(strict_types=1);


namespace App\Rpc\Lib;

/**
 * Class UserInterface
 *
 * @since 2.0
 */
interface NotifyInterface
{
    /**
     * 发送通知
     * @return array
     */
    public function publish($data): array;




}