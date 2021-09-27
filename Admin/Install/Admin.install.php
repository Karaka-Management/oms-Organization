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

use Modules\Organization\Controller\ApiController;
use Modules\Organization\Models\SettingsEnum;

return [
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::GROUP_GENERATE_AUTOMATICALLY_UNIT,
        'content' => '1',
        'module'  => ApiController::MODULE_NAME,
    ],
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::GROUP_GENERATE_AUTOMATICALLY_DEPARTMENT,
        'content' => '1',
        'module'  => ApiController::MODULE_NAME,
    ],
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::GROUP_GENERATE_AUTOMATICALLY_POSITION,
        'content' => '1',
        'module'  => ApiController::MODULE_NAME,
    ],
];
