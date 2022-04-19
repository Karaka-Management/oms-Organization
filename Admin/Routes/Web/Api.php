<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

use Modules\Organization\Controller\ApiController;
use Modules\Organization\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^.*/organization/position.*$' => [
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiPositionCreate',
            'verb'       => RouteVerb::PUT,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::POSITION,
            ],
        ],
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiPositionGet',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::POSITION,
            ],
        ],
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiPositionSet',
            'verb'       => RouteVerb::SET,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::POSITION,
            ],
        ],
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiPositionDelete',
            'verb'       => RouteVerb::DELETE,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::DELETE,
                'state'  => PermissionCategory::POSITION,
            ],
        ],
    ],
    '^.*/organization/department.*$' => [
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiDepartmentCreate',
            'verb'       => RouteVerb::PUT,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::DEPARTMENT,
            ],
        ],
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiDepartmentGet',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DEPARTMENT,
            ],
        ],
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiDepartmentSet',
            'verb'       => RouteVerb::SET,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::DEPARTMENT,
            ],
        ],
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiDepartmentDelete',
            'verb'       => RouteVerb::DELETE,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::DELETE,
                'state'  => PermissionCategory::DEPARTMENT,
            ],
        ],
    ],
    '^.*/organization/unit(\?.*|$)' => [
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiUnitCreate',
            'verb'       => RouteVerb::PUT,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::UNIT,
            ],
        ],
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiUnitGet',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::UNIT,
            ],
        ],
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiUnitSet',
            'verb'       => RouteVerb::SET,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::UNIT,
            ],
        ],
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiUnitDelete',
            'verb'       => RouteVerb::DELETE,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::UNIT,
            ],
        ],
    ],

    '^.*/organization/unit/image(\?.*|$)' => [
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiUnitImageSet',
            'verb'       => RouteVerb::SET,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::UNIT,
            ],
        ],
    ],

    '^.*/organization/find/unit.*$' => [
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiUnitFind',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::UNIT,
            ],
        ],
    ],
    '^.*/organization/find/department.*$' => [
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiDepartmentFind',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DEPARTMENT,
            ],
        ],
    ],
    '^.*/organization/find/position.*$' => [
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiPositionFind',
            'verb'       => RouteVerb::GET,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::POSITION,
            ],
        ],
    ],
];
