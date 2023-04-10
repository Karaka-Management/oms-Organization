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
 *
 * @template T of UnitAttributeType
 * @extends DataMapperFactory<T>
 */
final class UnitAttributeTypeMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'unit_attr_type_id'         => ['name' => 'unit_attr_type_id',       'type' => 'int',    'internal' => 'id'],
        'unit_attr_type_name'       => ['name' => 'unit_attr_type_name',     'type' => 'string', 'internal' => 'name', 'autocomplete' => true],
        'unit_attr_type_datatype'   => ['name' => 'unit_attr_type_datatype',   'type' => 'int',    'internal' => 'datatype'],
        'unit_attr_type_fields'     => ['name' => 'unit_attr_type_fields',   'type' => 'int',    'internal' => 'fields'],
        'unit_attr_type_custom'     => ['name' => 'unit_attr_type_custom',   'type' => 'bool',   'internal' => 'custom'],
        'unit_attr_type_pattern'    => ['name' => 'unit_attr_type_pattern',  'type' => 'string', 'internal' => 'validationPattern'],
        'unit_attr_type_required'   => ['name' => 'unit_attr_type_required', 'type' => 'bool',   'internal' => 'isRequired'],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:class-string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    public const HAS_MANY = [
        'l11n' => [
            'mapper'   => UnitAttributeTypeL11nMapper::class,
            'table'    => 'unit_attr_type_l11n',
            'self'     => 'unit_attr_type_l11n_type',
            'column'   => 'content',
            'external' => null,
        ],
        'defaults' => [
            'mapper'   => UnitAttributeValueMapper::class,
            'table'    => 'unit_attr_default',
            'self'     => 'unit_attr_default_type',
            'external' => 'unit_attr_default_value',
        ],
    ];

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'unit_attr_type';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'unit_attr_type_id';
}
