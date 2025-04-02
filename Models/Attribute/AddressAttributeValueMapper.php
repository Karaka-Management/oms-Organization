<?php
/**
 * Jingga
 *
 * PHP Version 8.2
 *
 * @package   Modules\Organization\Models\Attribute
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.2
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Organization\Models\Attribute;

use Modules\Attribute\Models\AttributeValue;
use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Address mapper class.
 *
 * @package Modules\Organization\Models\Attribute
 * @license OMS License 2.2
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of AttributeValue
 * @extends DataMapperFactory<T>
 */
final class AddressAttributeValueMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'address_attr_value_id'       => ['name' => 'address_attr_value_id',       'type' => 'int',      'internal' => 'id'],
        'address_attr_value_default'  => ['name' => 'address_attr_value_default',  'type' => 'bool',     'internal' => 'isDefault'],
        'address_attr_value_valueStr' => ['name' => 'address_attr_value_valueStr', 'type' => 'string',   'internal' => 'valueStr'],
        'address_attr_value_valueInt' => ['name' => 'address_attr_value_valueInt', 'type' => 'int',      'internal' => 'valueInt'],
        'address_attr_value_valueDec' => ['name' => 'address_attr_value_valueDec', 'type' => 'float',    'internal' => 'valueDec'],
        'address_attr_value_valueDat' => ['name' => 'address_attr_value_valueDat', 'type' => 'DateTime', 'internal' => 'valueDat'],
        'address_attr_value_unit'     => ['name' => 'address_attr_value_unit', 'type' => 'string', 'internal' => 'unit'],
        'address_attr_value_deptype'  => ['name' => 'address_attr_value_deptype', 'type' => 'int', 'internal' => 'dependingAttributeType'],
        'address_attr_value_depvalue' => ['name' => 'address_attr_value_depvalue', 'type' => 'int', 'internal' => 'dependingAttributeValue'],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:class-string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    public const HAS_MANY = [
        'l11n' => [
            'mapper'   => AddressAttributeValueL11nMapper::class,
            'table'    => 'address_attr_value_l11n',
            'self'     => 'address_attr_value_l11n_value',
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
    public const TABLE = 'address_attr_value';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'address_attr_value_id';
}
