<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   tests
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
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
final class UnitTest extends \PHPUnit\Framework\TestCase
{
    private Unit $unit;

    /**
     * {@inheritdoc}
     */
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
        self::assertEquals(0, $this->unit->id);
        self::assertEquals('', $this->unit->name);
        self::assertEquals('', $this->unit->description);
        self::assertEquals('', $this->unit->descriptionRaw);
        self::assertNull($this->unit->parent);
        self::assertInstanceOf('Modules\Media\Models\NullMedia', $this->unit->image);
        self::assertEquals(Status::INACTIVE, $this->unit->status);
    }

    /**
     * @covers Modules\Organization\Models\Unit
     * @group module
     */
    public function testNameInputOutput() : void
    {
        $this->unit->name = 'Name';
        self::assertEquals('Name', $this->unit->name);
    }

    /**
     * @covers Modules\Organization\Models\Unit
     * @group module
     */
    public function testDescriptionInputOutput() : void
    {
        $this->unit->description = 'Description';
        self::assertEquals('Description', $this->unit->description);
    }

    /**
     * @covers Modules\Organization\Models\Unit
     * @group module
     */
    public function testDescriptionRawInputOutput() : void
    {
        $this->unit->descriptionRaw = 'DescriptionRaw';
        self::assertEquals('DescriptionRaw', $this->unit->descriptionRaw);
    }

    /**
     * @covers Modules\Organization\Models\Unit
     * @group module
     */
    public function testParentInputOutput() : void
    {
        $this->unit->parent = new NullUnit(1);
        self::assertEquals(1, $this->unit->parent->id);
    }

    /**
     * @covers Modules\Organization\Models\Unit
     * @group module
     */
    public function testImageInputOutput() : void
    {
        $this->unit->image = new NullMedia(1);
        self::assertEquals(1, $this->unit->image->id);
    }

    /**
     * @covers Modules\Organization\Models\Unit
     * @group module
     */
    public function testSerialize() : void
    {
        $this->unit->name           = 'Name';
        $this->unit->description    = 'Description';
        $this->unit->descriptionRaw = 'DescriptionRaw';
        $this->unit->status         = Status::ACTIVE;
        $this->unit->parent         = ($p = new NullUnit(1));
        $this->unit->image          = ($i = new NullMedia(1));

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
