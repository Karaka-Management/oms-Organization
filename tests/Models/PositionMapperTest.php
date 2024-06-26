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

use Modules\Organization\Models\NullPosition;
use Modules\Organization\Models\Position;
use Modules\Organization\Models\PositionMapper;

/**
 * @internal
 */
#[\PHPUnit\Framework\Attributes\CoversClass(\Modules\Organization\Models\PositionMapper::class)]
final class PositionMapperTest extends \PHPUnit\Framework\TestCase
{
    #[\PHPUnit\Framework\Attributes\Group('module')]
    public function testCRUD() : void
    {
        $position              = new Position();
        $position->name        = 'CEO';
        $position->description = 'Description';

        $id = PositionMapper::create()->execute($position);

        $positionR = PositionMapper::get()->where('id', $id)->execute();
        self::assertEquals($id, $positionR->id);
        self::assertEquals($position->name, $positionR->name);
        self::assertEquals($position->description, $positionR->description);
        self::assertInstanceOf('Modules\Organization\Models\NullPosition', $positionR->parent);
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

        /* 4 */
        $position              = new Position();
        $position->name        = 'CFO';
        $position->description = 'Description';
        $position->parent      = new NullPosition($first);
        $id                    = PositionMapper::create()->execute($position);

        /* 5 */
        $position              = new Position();
        $position->name        = 'Accountant';
        $position->description = 'Description';
        $position->parent      = new NullPosition($id);
        PositionMapper::create()->execute($position);

        /* 6 */
        $position              = new Position();
        $position->name        = 'Controller';
        $position->description = 'Description';
        $position->parent      = new NullPosition($id);
        PositionMapper::create()->execute($position);

        /* 7 */
        $position              = new Position();
        $position->name        = 'Sales Director';
        $position->description = 'Description';
        $position->parent      = new NullPosition($first);
        PositionMapper::create()->execute($position);

        /* 8 */
        $position              = new Position();
        $position->name        = 'Purchase Director';
        $position->description = 'Description';
        $position->parent      = new NullPosition($first);
        PositionMapper::create()->execute($position);

        /* 9 */
        $position              = new Position();
        $position->name        = 'Territory Manager';
        $position->description = 'Description';
        $position->parent      = new NullPosition($first + 4);
        PositionMapper::create()->execute($position);

        /* 10 */
        $position              = new Position();
        $position->name        = 'Territory Sales Assistant';
        $position->description = 'Description';
        $position->parent      = new NullPosition($first + 6);
        PositionMapper::create()->execute($position);

        /* 11 */
        $position              = new Position();
        $position->name        = 'Domestic Sales Manager';
        $position->description = 'Description';
        $position->parent      = new NullPosition($first + 4);
        PositionMapper::create()->execute($position);
    }
}
