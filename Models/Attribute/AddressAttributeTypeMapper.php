<?php
/**
 * Jingga
 *
 * PHP Version 8.1
 *
 * @package   Modules\Organization\Models\Attribute
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Organization\Models\Attribute;

use Modules\Attribute\Models\AttributeType;
use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Address mapper class.
 *
 * @package Modules\Organization\Models\Attribute
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of AttributeType
 * @extends DataMapperFactory<T>
 */
final class AddressAttributeTypeMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'address_attr_type_id'         => ['name' => 'address_attr_type_id',       'type' => 'int',    'internal' => 'id'],
        'address_attr_type_name'       => ['name' => 'address_attr_type_name',     'type' => 'string', 'internal' => 'name', 'autocomplete' => true],
        'address_attr_type_datatype'   => ['name' => 'address_attr_type_datatype',   'type' => 'int',    'internal' => 'datatype'],
        'address_attr_type_fields'     => ['name' => 'address_attr_type_fields',   'type' => 'int',    'internal' => 'fields'],
        'address_attr_type_custom'     => ['name' => 'address_attr_type_custom',   'type' => 'bool',   'internal' => 'custom'],
        'address_attr_type_pattern'    => ['name' => 'address_attr_type_pattern',  'type' => 'string', 'internal' => 'validationPattern'],
        'address_attr_type_required'   => ['name' => 'address_attr_type_required', 'type' => 'bool',   'internal' => 'isRequired'],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:class-string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    public const HAS_MANY = [
        'l11n' => [
            'mapper'   => AddressAttributeTypeL11nMapper::class,
            'table'    => 'address_attr_type_l11n',
            'self'     => 'address_attr_type_l11n_type',
            'column'   => 'content',
            'external' => null,
        ],
        'defaults' => [
            'mapper'   => AddressAttributeValueMapper::class,
            'table'    => 'address_attr_default',
            'self'     => 'address_attr_default_type',
            'external' => 'address_attr_default_value',
        ],
    ];

    /**
     * Model to use by the mapper.
     *
     * @var class-string<T>
     * @since 1.0.0
     */
    public const MODEL = AttributeType::class;

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'address_attr_type';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'address_attr_type_id';
}
