<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Organization\Models\Attribute
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Organization\Models\Attribute;

use Modules\Attribute\Models\AttributeValue;
use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Unit mapper class.
 *
 * @package Modules\Organization\Models\Attribute
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of AttributeValue
 * @extends DataMapperFactory<T>
 */
final class UnitAttributeValueMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'unit_attr_value_id'       => ['name' => 'unit_attr_value_id',       'type' => 'int',      'internal' => 'id'],
        'unit_attr_value_default'  => ['name' => 'unit_attr_value_default',  'type' => 'bool',     'internal' => 'isDefault'],
        'unit_attr_value_valueStr' => ['name' => 'unit_attr_value_valueStr', 'type' => 'string',   'internal' => 'valueStr'],
        'unit_attr_value_valueInt' => ['name' => 'unit_attr_value_valueInt', 'type' => 'int',      'internal' => 'valueInt'],
        'unit_attr_value_valueDec' => ['name' => 'unit_attr_value_valueDec', 'type' => 'float',    'internal' => 'valueDec'],
        'unit_attr_value_valueDat' => ['name' => 'unit_attr_value_valueDat', 'type' => 'DateTime', 'internal' => 'valueDat'],
        'unit_attr_value_unit'     => ['name' => 'unit_attr_value_unit', 'type' => 'string', 'internal' => 'unit'],
        'unit_attr_value_deptype'  => ['name' => 'unit_attr_value_deptype', 'type' => 'int', 'internal' => 'dependingAttributeType'],
        'unit_attr_value_depvalue' => ['name' => 'unit_attr_value_depvalue', 'type' => 'int', 'internal' => 'dependingAttributeValue'],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:class-string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    public const HAS_MANY = [
        'l11n' => [
            'mapper'   => UnitAttributeValueL11nMapper::class,
            'table'    => 'unit_attr_value_l11n',
            'self'     => 'unit_attr_value_l11n_value',
            'column'   => 'content',
            'external' => null,
        ],
    ];

    /**
     * Model to use by the mapper.
     *
     * @var class-string<T>
     * @since 1.0.0
     */
    public const MODEL = AttributeValue::class;

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'unit_attr_value';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'unit_attr_value_id';
}
