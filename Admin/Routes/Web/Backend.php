<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\Organization\Controller\BackendController;
use Modules\Organization\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^/organization/organigram(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Organization\Controller\BackendController:viewOrganigram',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::ORGANIGRAM,
            ],
        ],
    ],
    '^/organization/unit/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Organization\Controller\BackendController:viewUnitList',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::UNIT,
            ],
        ],
    ],
    '^/organization/unit/view(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Organization\Controller\BackendController:viewUnitView',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::UNIT,
            ],
        ],
    ],
    '^/organization/unit/create(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Organization\Controller\BackendController:viewUnitCreate',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::UNIT,
            ],
        ],
    ],
    '^/organization/department/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Organization\Controller\BackendController:viewDepartmentList',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DEPARTMENT,
            ],
        ],
    ],
    '^/organization/department/view(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Organization\Controller\BackendController:viewDepartmentView',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DEPARTMENT,
            ],
        ],
    ],
    '^/organization/department/create(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Organization\Controller\BackendController:viewDepartmentCreate',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::DEPARTMENT,
            ],
        ],
    ],
    '^/organization/position/list(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Organization\Controller\BackendController:viewPositionList',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::POSITION,
            ],
        ],
    ],
    '^/organization/position/view(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Organization\Controller\BackendController:viewPositionView',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::POSITION,
            ],
        ],
    ],
    '^/organization/position/create(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Organization\Controller\BackendController:viewPositionCreate',
            'verb'       => RouteVerb::GET,
            'active'     => true,
            'permission' => [
                'module' => BackendController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::POSITION,
            ],
        ],
    ],
];
