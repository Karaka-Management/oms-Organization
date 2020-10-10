<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
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
class DepartmentTest extends \PHPUnit\Framework\TestCase
{
    private Department $department;

    public function setUp() : void
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
        self::assertEquals('', $this->department->getName());
        self::assertEquals('', $this->department->getDescription());
        self::assertEquals('', $this->department->getDescriptionRaw());
        self::assertInstanceOf(NullDepartment::class, $this->department->getParent());
        self::assertEquals(0, $this->department->getUnit()->getId());
        self::assertEquals(Status::INACTIVE, $this->department->getStatus());
    }

    /**
     * @covers Modules\Organization\Models\Department
     * @group module
     */
    public function testNameInputOutput() : void
    {
        $this->department->setName('Name');
        self::assertEquals('Name', $this->department->getName());
    }

    /**
     * @covers Modules\Organization\Models\Department
     * @group module
     */
    public function testDescriptionInputOutput() : void
    {
        $this->department->setDescription('Description');
        self::assertEquals('Description', $this->department->getDescription());
    }

    /**
     * @covers Modules\Organization\Models\Department
     * @group module
     */
    public function testDescriptionRawInputOutput() : void
    {
        $this->department->setDescriptionRaw('DescriptionRaw');
        self::assertEquals('DescriptionRaw', $this->department->getDescriptionRaw());
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
        $this->department->setParent(new NullDepartment(1));
        self::assertEquals(1, $this->department->getParent()->getId());
    }

    /**
     * @covers Modules\Organization\Models\Department
     * @group module
     */
    public function testUnitInputOutput() : void
    {
        $this->department->setUnit(new NullUnit(1));
        self::assertEquals(1, $this->department->getUnit()->getId());
    }

    /**
     * @covers Modules\Organization\Models\Department
     * @group module
     */
    public function testSerialize() : void
    {
        $this->department->setName('Name');
        $this->department->setDescription('Description');
        $this->department->setDescriptionRaw('DescriptionRaw');
        $this->department->setStatus(Status::ACTIVE);
        $this->department->setParent($p = new NullDepartment(1));
        $this->department->setUnit($u = new NullUnit(1));

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
