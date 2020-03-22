<?php declare(strict_types=1);

namespace Swoft\Devtool;

use Swoft\Helper\ComposerJSON;
use Swoft\SwoftComponent;

/**
 * Class AutoLoader
 * @since 2.0
 * @package Swoft\Devtool
 */
class AutoLoader extends SwoftComponent
{
    /**
     * Class constructor.
     */
    public function __construct()
    {
        parent::__construct();

        \Swoft::setAlias('@devtool', \dirname(__DIR__, 2));
    }

    /**
     *
     *
     * @return bool
     */
    public function enable(): bool
    {
        return (int)\env('SWOFT_DEBUG', 0) > 0;
    }

    /**
     * @return array
     */
    public function beans(): array
    {
        return [];
    }

    /**
     * Get namespace and dir
     *
     * @return array
     * [
     *     namespace => dir path
     * ]
     */
    public function getPrefixDirs(): array
    {
        return [
            __NAMESPACE__ => __DIR__,
        ];
    }

    /**
     * Metadata information for the component.
     *
     * Quick config:
     *
     * ```php
     * $jsonFile = \dirname(__DIR__) . '/composer.json';
     *
     * return ComposerJSON::open($jsonFile)->getMetadata();
     * ```
     *
     * @return array
     * @see ComponentInterface::getMetadata()
     */
    public function metadata(): array
    {
        $jsonFile = \dirname(__DIR__) . '/composer.json';

        return ComposerJSON::open($jsonFile)->getMetadata();
    }
}
