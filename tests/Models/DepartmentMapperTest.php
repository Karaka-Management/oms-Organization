<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\Organization\tests\Models;

use Modules\Organization\Models\Department;
use Modules\Organization\Models\DepartmentMapper;
use Modules\Organization\Models\NullDepartment;
use Modules\Organization\Models\NullUnit;

/**
 * @internal
 */
final class DepartmentMapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\Organization\Models\DepartmentMapper
     * @group module
     */
    public function testCRUD() : void
    {
        $department              = new Department();
        $department->name        = 'Management';
        $department->description = 'Description';
        $department->unit        = new NullUnit(1);

        $id = DepartmentMapper::create($department);

        $departmentR = DepartmentMapper::get($id);
        self::assertEquals($id, $departmentR->getId());
        self::assertEquals($department->name, $departmentR->name);
        self::assertEquals($department->description, $departmentR->description);
        self::assertInstanceOf('Modules\Organization\Models\NullDepartment', $departmentR->parent);
        self::assertEquals($department->unit->getId(), $departmentR->unit->getId());
    }

    /**
     * @group         volume
     * @slowThreshold 15000
     * @group module
     * @coversNothing
     */
    public function testVolume() : void
    {
        $first = 2;

        /* 2 */
        $department              = new Department();
        $department->name        = 'HR';
        $department->description = 'Description';
        $department->parent      = new NullDepartment($first);
        $department->unit        = new NullUnit(1);
        DepartmentMapper::create($department);

        /* 3 */
        $department              = new Department();
        $department->name        = 'QM';
        $department->description = 'Description';
        $department->parent      = new NullDepartment($first);
        $department->unit        = new NullUnit(1);
        DepartmentMapper::create($department);

        /* 4 */
        $department              = new Department();
        $department->name        = 'Sales';
        $department->description = 'Description';
        $department->parent      = new NullDepartment($first);
        $department->unit        = new NullUnit(1);
        DepartmentMapper::create($department);

        /* 5 */
        $department              = new Department();
        $department->name        = 'Shipping';
        $department->description = 'Description';
        $department->parent      = new NullDepartment($first + 3);
        $department->unit        = new NullUnit(1);
        DepartmentMapper::create($department);

        /* 6 */
        $department              = new Department();
        $department->name        = 'Purchase';
        $department->description = 'Description';
        $department->parent      = new NullDepartment($first);
        $department->unit        = new NullUnit(1);
        DepartmentMapper::create($department);

        /* 7 */
        $department              = new Department();
        $department->name        = 'Arrival';
        $department->description = 'Description';
        $department->parent      = new NullDepartment($first + 5);
        $department->unit        = new NullUnit(1);
        DepartmentMapper::create($department);

        /* 8 */
        $department              = new Department();
        $department->name        = 'Accounting';
        $department->description = 'Description';
        $department->parent      = new NullDepartment($first);
        $department->unit        = new NullUnit(1);
        DepartmentMapper::create($department);

        /* 9 */
        $department              = new Department();
        $department->name        = 'Production';
        $department->description = 'Description';
        $department->parent      = new NullDepartment($first);
        $department->unit        = new NullUnit(1);
        DepartmentMapper::create($department);
    }
}
