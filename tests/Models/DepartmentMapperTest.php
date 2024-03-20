<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
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
#[\PHPUnit\Framework\Attributes\CoversClass(\Modules\Organization\Models\DepartmentMapper::class)]
final class DepartmentMapperTest extends \PHPUnit\Framework\TestCase
{
    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testCRUD() : void
    {
        $department              = new Department();
        $department->name        = 'Management';
        $department->description = 'Description';
        $department->unit        = new NullUnit(1);

        $id = DepartmentMapper::create()->execute($department);

        $departmentR = DepartmentMapper::get()->where('id', $id)->execute();
        self::assertEquals($id, $departmentR->id);
        self::assertEquals($department->name, $departmentR->name);
        self::assertEquals($department->description, $departmentR->description);
        self::assertInstanceOf('Modules\Organization\Models\NullDepartment', $departmentR->parent);
        self::assertEquals($department->unit->id, $departmentR->unit->id);
    }

    /**
     * @slowThreshold 15000
     */
    #[\PHPUnit\Framework\Attributes\Group('volume')]
    #[\PHPUnit\Framework\Attributes\Group('module')]
    #[\PHPUnit\Framework\Attributes\CoversNothing]
    public function testVolume() : void
    {
        $first = 2;

        /* 2 */
        $department              = new Department();
        $department->name        = 'HR';
        $department->description = 'Description';
        $department->parent      = new NullDepartment($first);
        $department->unit        = new NullUnit(1);
        DepartmentMapper::create()->execute($department);

        /* 3 */
        $department              = new Department();
        $department->name        = 'QM';
        $department->description = 'Description';
        $department->parent      = new NullDepartment($first);
        $department->unit        = new NullUnit(1);
        DepartmentMapper::create()->execute($department);

        /* 4 */
        $department              = new Department();
        $department->name        = 'Sales';
        $department->description = 'Description';
        $department->parent      = new NullDepartment($first);
        $department->unit        = new NullUnit(1);
        DepartmentMapper::create()->execute($department);

        /* 5 */
        $department              = new Department();
        $department->name        = 'Shipping';
        $department->description = 'Description';
        $department->parent      = new NullDepartment($first + 3);
        $department->unit        = new NullUnit(1);
        DepartmentMapper::create()->execute($department);

        /* 6 */
        $department              = new Department();
        $department->name        = 'Purchase';
        $department->description = 'Description';
        $department->parent      = new NullDepartment($first);
        $department->unit        = new NullUnit(1);
        DepartmentMapper::create()->execute($department);

        /* 7 */
        $department              = new Department();
        $department->name        = 'Arrival';
        $department->description = 'Description';
        $department->parent      = new NullDepartment($first + 5);
        $department->unit        = new NullUnit(1);
        DepartmentMapper::create()->execute($department);

        /* 8 */
        $department              = new Department();
        $department->name        = 'Accounting';
        $department->description = 'Description';
        $department->parent      = new NullDepartment($first);
        $department->unit        = new NullUnit(1);
        DepartmentMapper::create()->execute($department);

        /* 9 */
        $department              = new Department();
        $department->name        = 'Production';
        $department->description = 'Description';
        $department->parent      = new NullDepartment($first);
        $department->unit        = new NullUnit(1);
        DepartmentMapper::create()->execute($department);
    }
}
