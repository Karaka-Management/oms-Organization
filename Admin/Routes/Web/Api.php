<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

use Modules\Organization\Controller\ApiController;
use Modules\Organization\Models\PermissionCategory;
use phpOMS\Account\PermissionType;
use phpOMS\Router\RouteVerb;

return [
    '^.*/organization/position(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiPositionCreate',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::POSITION,
            ],
        ],
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiPositionGet',
            'verb'       => RouteVerb::GET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::POSITION,
            ],
        ],
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiPositionSet',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::POSITION,
            ],
        ],
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiPositionDelete',
            'verb'       => RouteVerb::DELETE,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::DELETE,
                'state'  => PermissionCategory::POSITION,
            ],
        ],
    ],
    '^.*/organization/department(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiDepartmentCreate',
            'verb'       => RouteVerb::PUT,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::DEPARTMENT,
            ],
        ],
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiDepartmentGet',
            'verb'       => RouteVerb::GET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DEPARTMENT,
            ],
        ],
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiDepartmentSet',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::DEPARTMENT,
            ],
        ],
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiDepartmentDelete',
            'verb'       => RouteVerb::DELETE,
            'csrf'       => true,
            'active'     => true,
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
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::UNIT,
            ],
        ],
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiUnitGet',
            'verb'       => RouteVerb::GET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::UNIT,
            ],
        ],
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiUnitSet',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::UNIT,
            ],
        ],
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiUnitDelete',
            'verb'       => RouteVerb::DELETE,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::CREATE,
                'state'  => PermissionCategory::UNIT,
            ],
        ],
    ],

    '^.*/organization/unit/address/main(\?.*|$)' => [
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiUnitMainAddressSet',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::UNIT,
            ],
        ],
    ],

    '^.*/organization/unit/image(\?.*|$)' => [
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiUnitImageSet',
            'verb'       => RouteVerb::SET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::MODIFY,
                'state'  => PermissionCategory::UNIT,
            ],
        ],
    ],

    '^.*/organization/find/unit(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiUnitFind',
            'verb'       => RouteVerb::GET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::UNIT,
            ],
        ],
    ],
    '^.*/organization/find/department(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiDepartmentFind',
            'verb'       => RouteVerb::GET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::DEPARTMENT,
            ],
        ],
    ],
    '^.*/organization/find/position(\?.*$|$)' => [
        [
            'dest'       => '\Modules\Organization\Controller\ApiController:apiPositionFind',
            'verb'       => RouteVerb::GET,
            'csrf'       => true,
            'active'     => true,
            'permission' => [
                'module' => ApiController::NAME,
                'type'   => PermissionType::READ,
                'state'  => PermissionCategory::POSITION,
            ],
        ],
    ],
];
