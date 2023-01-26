<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Organization\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Organization\Models;

use Modules\Media\Models\MediaMapper;
use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Organization unit mapper class.
 *
 * @package Modules\Organization\Models
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
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
    ];

    /**
     * Has one relation.
     *
     * @var array<string, array{mapper:string, external:string, by?:string, column?:string, conditional?:bool}>
     * @since 1.0.0
     */
    public const OWNS_ONE = [
        'image'    => [
            'mapper'   => MediaMapper::class,
            'external' => 'unit_image',
        ],
    ];

    /**
     * Belongs to.
     *
     * @var array<string, array{mapper:string, external:string, column?:string, by?:string}>
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
     * @var string
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
    public const PRIMARYFIELD ='unit_id';
}
