<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Organization\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Organization\Admin;

use phpOMS\DataStorage\Database\DatabasePool;
use phpOMS\Module\ModuleInfo;
use phpOMS\Module\UpdaterAbstract;
use phpOMS\System\File\Local\Directory;

/**
 * Navigation class.
 *
 * @package Modules\Organization\Admin
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class Updater extends UpdaterAbstract
{
    /**
     * {@inheritdoc}
     */
    public static function update(DatabasePool $dbPool, ModuleInfo $info) : void
    {
        Directory::delete(__DIR__ . '/Update');
        \mkdir('Update');
        parent::update($dbPool, $info);
    }
}
