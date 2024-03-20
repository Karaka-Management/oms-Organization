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

use Modules\Organization\Models\NullDepartment;
use Modules\Organization\Models\NullPosition;
use Modules\Organization\Models\Position;
use Modules\Organization\Models\Status;

/**
 * @internal
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\Modules\Organization\Models\Position::class)]
final class PositionTest extends \PHPUnit\Framework\TestCase
{
    private Position $position;

    /**
     * {@inheritdoc}
     */
    protected function setUp() : void
    {
        $this->position = new Position();
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testDefault() : void
    {
        self::assertEquals(0, $this->position->id);
        self::assertEquals('', $this->position->name);
        self::assertEquals('', $this->position->description);
        self::assertEquals('', $this->position->descriptionRaw);
        self::assertNull($this->position->parent);
        self::assertEquals(0, $this->position->department->id);
        self::assertEquals(Status::INACTIVE, $this->position->status);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testNameInputOutput() : void
    {
        $this->position->name = 'Name';
        self::assertEquals('Name', $this->position->name);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testDescriptionInputOutput() : void
    {
        $this->position->description = 'Description';
        self::assertEquals('Description', $this->position->description);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testDescriptionRawInputOutput() : void
    {
        $this->position->descriptionRaw = 'DescriptionRaw';
        self::assertEquals('DescriptionRaw', $this->position->descriptionRaw);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testParentInputOutput() : void
    {
        $this->position->parent = new NullPosition(1);
        self::assertEquals(1, $this->position->parent->id);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testDepartmentInputOutput() : void
    {
        $this->position->department = new NullDepartment(1);
        self::assertEquals(1, $this->position->department->id);
    }

    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testSerialize() : void
    {
        $this->position->name           = 'Name';
        $this->position->description    = 'Description';
        $this->position->descriptionRaw = 'DescriptionRaw';
        $this->position->status         = Status::ACTIVE;
        $this->position->parent         = ($p = new NullPosition(1));
        $this->position->department     = ($d = new NullDepartment(1));

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
