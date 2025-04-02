<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Organization\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Organization\Models;

use phpOMS\Stdlib\Base\Enum;

/**
 * Accept status enum.
 *
 * @package Modules\Organization\Models
 * @license OMS License 2.2
 * @link    https://jingga.app
 * @since   1.0.0
 */
abstract class Status extends Enum
{
    public const ACTIVE = 1;

    public const INACTIVE = 2;

    public const HIDDEN = 4;
}
