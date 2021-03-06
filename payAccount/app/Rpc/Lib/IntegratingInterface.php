<?php declare(strict_types=1);


namespace App\Rpc\Lib;

/**
 * Class UserInterface
 *
 * @since 2.0
 */
interface IntegratingInterface
{
    /**
     * @return array
     */
    public function add(): array;

}