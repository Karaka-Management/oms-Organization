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

use Modules\Media\Models\NullMedia;
use Modules\Organization\Models\NullUnit;
use Modules\Organization\Models\Status;
use Modules\Organization\Models\Unit;

/**
 * @internal
 */
class UnitTest extends \PHPUnit\Framework\TestCase
{
    private Unit $unit;

    protected function setUp() : void
    {
        $this->unit = new Unit();
    }

    /**
     * @covers Modules\Organization\Models\Unit
     * @group module
     */
    public function testDefault() : void
    {
        self::assertEquals(0, $this->unit->getId());
        self::assertEquals('', $this->unit->getName());
        self::assertEquals('', $this->unit->getDescription());
        self::assertEquals('', $this->unit->getDescriptionRaw());
        self::assertInstanceOf('Modules\Organization\Models\NullUnit', $this->unit->getParent());
        self::assertInstanceOf('Modules\Media\Models\NullMedia', $this->unit->getImage());
        self::assertEquals(Status::INACTIVE, $this->unit->getStatus());
    }

    /**
     * @covers Modules\Organization\Models\Unit
     * @group module
     */
    public function testNameInputOutput() : void
    {
        $this->unit->setName('Name');
        self::assertEquals('Name', $this->unit->getName());
    }

    /**
     * @covers Modules\Organization\Models\Unit
     * @group module
     */
    public function testStatusInputOutput() : void
    {
        $this->unit->setStatus(Status::ACTIVE);
        self::assertEquals(Status::ACTIVE, $this->unit->getStatus());
    }

    /**
     * @covers Modules\Organization\Models\Unit
     * @group module
     */
    public function testDescriptionInputOutput() : void
    {
        $this->unit->setDescription('Description');
        self::assertEquals('Description', $this->unit->getDescription());
    }

    /**
     * @covers Modules\Organization\Models\Unit
     * @group module
     */
    public function testDescriptionRawInputOutput() : void
    {
        $this->unit->setDescriptionRaw('DescriptionRaw');
        self::assertEquals('DescriptionRaw', $this->unit->getDescriptionRaw());
    }

    /**
     * @covers Modules\Organization\Models\Unit
     * @group module
     */
    public function testParentInputOutput() : void
    {
        $this->unit->setParent(new NullUnit(1));
        self::assertEquals(1, $this->unit->getParent()->getId());
    }

    /**
     * @covers Modules\Organization\Models\Unit
     * @group module
     */
    public function testImageInputOutput() : void
    {
        $this->unit->setImage(new NullMedia(1));
        self::assertEquals(1, $this->unit->getImage()->getId());
    }

    /**
     * @covers Modules\Organization\Models\Unit
     * @group module
     */
    public function testSerialize() : void
    {
        $this->unit->setName('Name');
        $this->unit->setDescription('Description');
        $this->unit->setDescriptionRaw('DescriptionRaw');
        $this->unit->setStatus(Status::ACTIVE);
        $this->unit->setParent($p = new NullUnit(1));
        $this->unit->setImage($i = new NullMedia(1));

        self::assertEquals($this->unit->toArray(), $this->unit->jsonSerialize());
        self::assertEquals(
            [
                'id'             => 0,
                'name'           => 'Name',
                'status'         => Status::ACTIVE,
                'description'    => 'Description',
                'descriptionRaw' => 'DescriptionRaw',
                'parent'         => $p,
                'image'          => $i,
            ],
            $this->unit->toArray()
        );
    }
}
