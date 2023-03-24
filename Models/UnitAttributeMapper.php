<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Organization\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Organization\Models;

use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Unit mapper class.
 *
 * @package Modules\Organization\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
final class UnitAttributeMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'unit_attr_id'    => ['name' => 'unit_attr_id',    'type' => 'int', 'internal' => 'id'],
        'unit_attr_unit'  => ['name' => 'unit_attr_unit',  'type' => 'int', 'internal' => 'unit'],
        'unit_attr_type'  => ['name' => 'unit_attr_type',  'type' => 'int', 'internal' => 'type'],
        'unit_attr_value' => ['name' => 'unit_attr_value', 'type' => 'int', 'internal' => 'value'],
    ];

    /**
     * Has one relation.
     *
     * @var array<string, array{mapper:class-string, external:string, by?:string, column?:string, conditional?:bool}>
     * @since 1.0.0
     */
    public const OWNS_ONE = [
        'type' => [
            'mapper'   => UnitAttributeTypeMapper::class,
            'external' => 'unit_attr_type',
        ],
        'value' => [
            'mapper'   => UnitAttributeValueMapper::class,
            'external' => 'unit_attr_value',
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'unit_attr';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'unit_attr_id';
}
