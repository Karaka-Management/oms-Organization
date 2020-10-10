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

use Modules\Organization\Models\NullDepartment;
use Modules\Organization\Models\NullPosition;
use Modules\Organization\Models\Position;
use Modules\Organization\Models\Status;

/**
 * @internal
 */
class PositionTest extends \PHPUnit\Framework\TestCase
{
    private Position $position;

    protected function setUp() : void
    {
        $this->position = new Position();
    }

    /**
     * @covers Modules\Organization\Models\Position
     * @group module
     */
    public function testDefault() : void
    {
        self::assertEquals(0, $this->position->getId());
        self::assertEquals('', $this->position->getName());
        self::assertEquals('', $this->position->getDescription());
        self::assertEquals('', $this->position->getDescriptionRaw());
        self::assertInstanceOf(NullPosition::class, $this->position->getParent());
        self::assertEquals(0, $this->position->getDepartment()->getId());
        self::assertEquals(Status::INACTIVE, $this->position->getStatus());
    }

    /**
     * @covers Modules\Organization\Models\Position
     * @group module
     */
    public function testNameInputOutput() : void
    {
        $this->position->setName('Name');
        self::assertEquals('Name', $this->position->getName());
    }

    /**
     * @covers Modules\Organization\Models\Position
     * @group module
     */
    public function testDescriptionInputOutput() : void
    {
        $this->position->setDescription('Description');
        self::assertEquals('Description', $this->position->getDescription());
    }

    /**
     * @covers Modules\Organization\Models\Position
     * @group module
     */
    public function testDescriptionRawInputOutput() : void
    {
        $this->position->setDescriptionRaw('DescriptionRaw');
        self::assertEquals('DescriptionRaw', $this->position->getDescriptionRaw());
    }

    /**
     * @covers Modules\Organization\Models\Position
     * @group module
     */
    public function testStatusInputOutput() : void
    {
        $this->position->setStatus(Status::ACTIVE);
        self::assertEquals(Status::ACTIVE, $this->position->getStatus());
    }

    /**
     * @covers Modules\Organization\Models\Position
     * @group module
     */
    public function testParentInputOutput() : void
    {
        $this->position->setParent(new NullPosition(1));
        self::assertEquals(1, $this->position->getParent()->getId());
    }

    /**
     * @covers Modules\Organization\Models\Position
     * @group module
     */
    public function testDepartmentInputOutput() : void
    {
        $this->position->setDepartment(new NullDepartment(1));
        self::assertEquals(1, $this->position->getDepartment()->getId());
    }

    /**
     * @covers Modules\Organization\Models\Position
     * @group module
     */
    public function testSerialize() : void
    {
        $this->position->setName('Name');
        $this->position->setDescription('Description');
        $this->position->setDescriptionRaw('DescriptionRaw');
        $this->position->setStatus(Status::ACTIVE);
        $this->position->setParent($p = new NullPosition(1));
        $this->position->setDepartment($d = new NullDepartment(1));

        self::assertEquals($this->position->toArray(), $this->position->jsonSerialize());
        self::assertEquals(
            [
                'id'             => 0,
                'name'           => 'Name',
                'status'         => Status::ACTIVE,
                'description'    => 'Description',
                'descriptionRaw' => 'DescriptionRaw',
                'parent'         => $p,
                'department'     => $d,
            ],
            $this->position->toArray()
        );
    }
}
