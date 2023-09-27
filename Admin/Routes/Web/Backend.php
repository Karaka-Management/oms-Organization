<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\Organization\Controller\BackendController;
use Modules\Organization\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^.*/organization/organigram.*$' => [
        [
            'dest'       => '\Modules\Organization\Controller\BackendController:viewOrganigram',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::ORGANIGRAM,
            ],
        ],
    ],
    '^.*/organization/unit/list.*$' => [
        [
            'dest'       => '\Modules\Organization\Controller\BackendController:viewUnitList',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::UNIT,
            ],
        ],
    ],
    '^.*/organization/unit/profile.*$' => [
        [
            'dest'       => '\Modules\Organization\Controller\BackendController:viewUnitProfile',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::UNIT,
            ],
        ],
    ],
    '^.*/organization/unit/create.*$' => [
        [
            'dest'       => '\Modules\Organization\Controller\BackendController:viewUnitCreate',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::UNIT,
            ],
        ],
    ],
    '^.*/organization/department/list.*$' => [
        [
            'dest'       => '\Modules\Organization\Controller\BackendController:viewDepartmentList',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DEPARTMENT,
            ],
        ],
    ],
    '^.*/organization/department/profile.*$' => [
        [
            'dest'       => '\Modules\Organization\Controller\BackendController:viewDepartmentProfile',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DEPARTMENT,
            ],
        ],
    ],
    '^.*/organization/department/create.*$' => [
        [
            'dest'       => '\Modules\Organization\Controller\BackendController:viewDepartmentCreate',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::DEPARTMENT,
            ],
        ],
    ],
    '^.*/organization/position/list.*$' => [
        [
            'dest'       => '\Modules\Organization\Controller\BackendController:viewPositionList',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::POSITION,
            ],
        ],
    ],
    '^.*/organization/position/profile.*$' => [
        [
            'dest'       => '\Modules\Organization\Controller\BackendController:viewPositionProfile',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::POSITION,
            ],
        ],
    ],
    '^.*/organization/position/create.*$' => [
        [
            'dest'       => '\Modules\Organization\Controller\BackendController:viewPositionCreate',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::POSITION,
            ],
        ],
    ],
];
