<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
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
#[\PHPUnit\Framework\Attributes\CoversClass(\Modules\Organization\Models\Department::class)]
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

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testDefault() : void
    {
        self::assertEquals(0, $this->department->id);
        self::assertEquals('', $this->department->name);
        self::assertEquals('', $this->department->description);
        self::assertEquals('', $this->department->descriptionRaw);
        self::assertNull($this->department->parent);
        self::assertEquals(0, $this->department->unit->id);
        self::assertEquals(Status::INACTIVE, $this->department->status);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testNameInputOutput() : void
    {
        $this->department->name = 'Name';
        self::assertEquals('Name', $this->department->name);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testDescriptionInputOutput() : void
    {
        $this->department->description = 'Description';
        self::assertEquals('Description', $this->department->description);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testDescriptionRawInputOutput() : void
    {
        $this->department->descriptionRaw = 'DescriptionRaw';
        self::assertEquals('DescriptionRaw', $this->department->descriptionRaw);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testParentInputOutput() : void
    {
        $this->department->parent = new NullDepartment(1);
        self::assertEquals(1, $this->department->parent->id);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testUnitInputOutput() : void
    {
        $this->department->unit = new NullUnit(1);
        self::assertEquals(1, $this->department->unit->id);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testSerialize() : void
    {
        $this->department->name           = 'Name';
        $this->department->description    = 'Description';
        $this->department->descriptionRaw = 'DescriptionRaw';
        $this->department->status         = Status::ACTIVE;
        $this->department->parent         = ($p = new NullDepartment(1));
        $this->department->unit           = ($u = new NullUnit(1));

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
