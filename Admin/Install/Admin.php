<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\Organization\Admin\Install
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\Organization\Admin\Install;

use phpOMS\Application\ApplicationAbstract;

/**
 * Admin class.
 *
 * @package Modules\Organization\Admin\Install
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
class Admin
{
    /**
     * Install Admin providing
     *
     * @param string              $path Module path
     * @param ApplicationAbstract $app  Application
     *
     * @return void
     *
     * @since 1.0.0
     */
    public static function install(string $path, ApplicationAbstract $app) : void
    {
        $settings = include __DIR__ . '/Admin.install.php';
        \file_put_contents(__DIR__ . '/Admin.install.json', \json_encode($settings, \JSON_PRETTY_PRINT));

        \Modules\Admin\Admin\Installer::installExternal($app, ['path' => __DIR__ . '/Admin.install.json']);
    }
}
