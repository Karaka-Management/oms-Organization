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
use Modules\Organization\Models\NullDepartment;
use Modules\Organization\Models\NullUnit;
use Modules\Organization\Models\Status;

/**
 * @internal
 */
final class DepartmentTest extends \PHPUnit\Framework\TestCase
{
    private Department $department;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->department = new Department();
    }

    /**
     * @covers Modules\Organization\Models\Department
     * @group module
     */
    public function testDefault() : void
    {
        self::assertEquals(0, $this->department->getId());
        self::assertEquals('', $this->department->name);
        self::assertEquals('', $this->department->description);
        self::assertEquals('', $this->department->descriptionRaw);
        self::assertInstanceOf(NullDepartment::class, $this->department->parent);
        self::assertEquals(0, $this->department->unit->getId());
        self::assertEquals(Status::INACTIVE, $this->department->getStatus());
    }

    /**
     * @covers Modules\Organization\Models\Department
     * @group module
     */
    public function testNameInputOutput() : void
    {
        $this->department->name = 'Name';
        self::assertEquals('Name', $this->department->name);
    }

    /**
     * @covers Modules\Organization\Models\Department
     * @group module
     */
    public function testDescriptionInputOutput() : void
    {
        $this->department->description = 'Description';
        self::assertEquals('Description', $this->department->description);
    }

    /**
     * @covers Modules\Organization\Models\Department
     * @group module
     */
    public function testDescriptionRawInputOutput() : void
    {
        $this->department->descriptionRaw = 'DescriptionRaw';
        self::assertEquals('DescriptionRaw', $this->department->descriptionRaw);
    }

    /**
     * @covers Modules\Organization\Models\Department
     * @group module
     */
    public function testStatusInputOutput() : void
    {
        $this->department->setStatus(Status::ACTIVE);
        self::assertEquals(Status::ACTIVE, $this->department->getStatus());
    }

    /**
     * @covers Modules\Organization\Models\Department
     * @group module
     */
    public function testParentInputOutput() : void
    {
        $this->department->parent = new NullDepartment(1);
        self::assertEquals(1, $this->department->parent->getId());
    }

    /**
     * @covers Modules\Organization\Models\Department
     * @group module
     */
    public function testUnitInputOutput() : void
    {
        $this->department->unit = new NullUnit(1);
        self::assertEquals(1, $this->department->unit->getId());
    }

    /**
     * @covers Modules\Organization\Models\Department
     * @group module
     */
    public function testSerialize() : void
    {
        $this->department->name           = 'Name';
        $this->department->description    = 'Description';
        $this->department->descriptionRaw = 'DescriptionRaw';
        $this->department->setStatus(Status::ACTIVE);
        $this->department->parent = ($p = new NullDepartment(1));
        $this->department->unit   = ($u = new NullUnit(1));

        self::assertEquals($this->department->toArray(), $this->department->jsonSerialize());
        self::assertEquals(
            [
                'id'             => 0,
                'name'           => 'Name',
                'status'         => Status::ACTIVE,
                'description'    => 'Description',
                'descriptionRaw' => 'DescriptionRaw',
                'parent'         => $p,
                'unit'           => $u,
            ],
            $this->department->toArray()
        );
    }
}
