<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\Organization\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\Organization\Admin;

use Modules\Organization\Models\Unit;
use Modules\Organization\Models\UnitMapper;
use phpOMS\Config\SettingsInterface;
use phpOMS\DataStorage\Database\DatabasePool;
use phpOMS\Module\InstallerAbstract;
use phpOMS\Module\ModuleInfo;

/**
 * Installer class.
 *
 * @package Modules\Organization\Admin
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
final class Installer extends InstallerAbstract
{
    /**
     * Path of the file
     *
     * @var string
     * @since 1.0.0
     */
    public const PATH = __DIR__;

    /**
     * {@inheritdoc}
     */
    public static function install(DatabasePool $dbPool, ModuleInfo $info, SettingsInterface $cfgHandler) : void
    {
        parent::install($dbPool, $info, $cfgHandler);

        self::installDefaultUnit();
    }

    /**
     * Install default unit
     *
     * @return void
     *
     * @since 1.0.0
     */
    private static function installDefaultUnit() : void
    {
        $unit       = new Unit();
        $unit->name = 'Orange Management';

        UnitMapper::create($unit);
    }
}
