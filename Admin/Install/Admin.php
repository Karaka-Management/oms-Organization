<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Organization\Admin\Install
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\Organization\Admin\Install;

use phpOMS\Application\ApplicationAbstract;

/**
 * Admin class.
 *
 * @package Modules\Organization\Admin\Install
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
class Admin
{
    /**
     * Install Admin providing
     *
     * @param ApplicationAbstract $app  Application
     * @param string              $path Module path
     *
     * @return void
     *
     * @since 1.0.0
     */
    public static function install(ApplicationAbstract $app, string $path) : void
    {
        $settings = include __DIR__ . '/Admin.install.php';
        \file_put_contents(__DIR__ . '/Admin.install.json', \json_encode($settings, \JSON_PRETTY_PRINT));

        \Modules\Admin\Admin\Installer::installExternal($app, ['path' => __DIR__ . '/Admin.install.json']);
    }
}
