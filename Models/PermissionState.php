<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   Modules\Organization\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\Organization\Models;

use phpOMS\Stdlib\Base\Enum;

/**
 * Permision state enum.
 *
 * @package Modules\Organization\Models
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 */
abstract class PermissionState extends Enum
{
    public const UNIT = 1;

    public const DEPARTMENT = 2;

    public const POSITION = 3;

    public const ORGANIGRAM = 4;
}
