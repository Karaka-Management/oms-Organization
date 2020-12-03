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

use Modules\Organization\Models\NullPosition;

/**
 * @internal
 */
final class NullPositionTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\Organization\Models\NullPosition
     * @group module
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\Organization\Models\Position', new NullPosition());
    }

    /**
     * @covers Modules\Organization\Models\NullPosition
     * @group module
     */
    public function testId() : void
    {
        $null = new NullPosition(2);
        self::assertEquals(2, $null->getId());
    }
}
