<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Organization\Admin
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\Organization\Controller\ApiController;
use Modules\Organization\Models\SettingsEnum;

return [
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::GROUP_GENERATE_AUTOMATICALLY_UNIT,
        'content' => '1',
        'module'  => ApiController::NAME,
    ],
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::GROUP_GENERATE_AUTOMATICALLY_DEPARTMENT,
        'content' => '1',
        'module'  => ApiController::NAME,
    ],
    [
        'type'    => 'setting',
        'name'    => SettingsEnum::GROUP_GENERATE_AUTOMATICALLY_POSITION,
        'content' => '1',
        'module'  => ApiController::NAME,
    ],
];
