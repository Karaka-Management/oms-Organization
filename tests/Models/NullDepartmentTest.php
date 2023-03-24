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

use Modules\Organization\Models\NullDepartment;

/**
 * @internal
 */
final class NullDepartmentTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers Modules\Organization\Models\NullDepartment
     * @group module
     */
    public function testNull() : void
    {
        self::assertInstanceOf('\Modules\Organization\Models\Department', new NullDepartment());
    }

    /**
     * @covers Modules\Organization\Models\NullDepartment
     * @group module
     */
    public function testId() : void
    {
        $null = new NullDepartment(2);
        self::assertEquals(2, $null->getId());
    }
}
