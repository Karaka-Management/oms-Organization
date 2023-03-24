<?php
/**
 * Karaka
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

use Modules\Organization\Models\NullUnit;
use Modules\Organization\Models\Unit;
use Modules\Organization\Models\UnitMapper;

/**
 * @internal
 */
final class UnitMapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\Organization\Models\UnitMapper
     * @group module
     */
    public function testCRUD() : void
    {
        $unit              = new Unit();
        $unit->name        = 'Scrooge Inc.';
        $unit->description = 'Description';
        $unit->parent      = new NullUnit(1);

        $id = UnitMapper::create()->execute($unit);

        $unitR = UnitMapper::get()->where('id', $id)->execute();
        self::assertEquals($id, $unitR->getId());
        self::assertEquals($unit->name, $unitR->name);
        self::assertEquals($unit->description, $unitR->description);
        self::assertEquals($unit->parent->getId(), $unitR->parent->getId());
    }
}
