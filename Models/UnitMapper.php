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

use Modules\Admin\Models\AddressMapper;
use Modules\Media\Models\MediaMapper;
use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Organization unit mapper class.
 *
 * @package Modules\Organization\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of Unit
 * @extends DataMapperFactory<T>
 */
final class UnitMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'unit_id'             => ['name' => 'unit_id',             'type' => 'int',    'internal' => 'id'],
        'unit_name'           => ['name' => 'unit_name',           'type' => 'string', 'internal' => 'name', 'autocomplete' => true],
        'unit_image'          => ['name' => 'unit_image',          'type' => 'int',    'internal' => 'image'],
        'unit_description'    => ['name' => 'unit_description',    'type' => 'string', 'internal' => 'description'],
        'unit_descriptionraw' => ['name' => 'unit_descriptionraw', 'type' => 'string', 'internal' => 'descriptionRaw'],
        'unit_parent'         => ['name' => 'unit_parent',         'type' => 'int',    'internal' => 'parent'],
        'unit_status'         => ['name' => 'unit_status',         'type' => 'int',    'internal' => 'status'],
        'unit_address'        => ['name' => 'unit_address',    'type' => 'int',      'internal' => 'mainAddress'],
    ];

    /**
     * Has many relation.
     *
     * @var array<string, array{mapper:class-string, table:string, self?:?string, external?:?string, column?:string}>
     * @since 1.0.0
     */
    public const HAS_MANY = [
        'address' => [
            'mapper'   => AddressMapper::class,
            'table'    => 'unit_address_rel',
            'external' => 'unit_address_rel_address',
            'self'     => 'unit_address_rel_unit',
        ],
        'attributes' => [
            'mapper'      => UnitAttributeMapper::class,
            'table'       => 'unit_attr',
            'self'        => 'unit_attr_unit',
            'conditional' => true,
            'external'    => null,
        ],
    ];

    /**
     * Has one relation.
     *
     * @var array<string, array{mapper:class-string, external:string, by?:string, column?:string, conditional?:bool}>
     * @since 1.0.0
     */
    public const OWNS_ONE = [
        'mainAddress' => [
            'mapper'   => AddressMapper::class,
            'external' => 'unit_address',
        ],
        'image'    => [
            'mapper'   => MediaMapper::class,
            'external' => 'unit_image',
        ],
    ];

    /**
     * Belongs to.
     *
     * @var array<string, array{mapper:class-string, external:string, column?:string, by?:string}>
     * @since 1.0.0
     */
    public const BELONGS_TO = [
        'parent'  => [
            'mapper'   => self::class,
            'external' => 'unit_parent',
        ],
    ];

    /**
     * Model to use by the mapper.
     *
     * @var class-string<T>
     * @since 1.0.0
     */
    public const MODEL = Unit::class;

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'unit';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'unit_id';
}
