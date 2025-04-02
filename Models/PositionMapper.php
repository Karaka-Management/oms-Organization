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

use phpOMS\DataStorage\Database\Mapper\DataMapperFactory;

/**
 * Organization position mapper class.
 *
 * @package Modules\Organization\Models
 * @license OMS License 2.2
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of Position
 * @extends DataMapperFactory<T>
 */
final class PositionMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'organization_position_id'             => ['name' => 'organization_position_id',             'type' => 'int',    'internal' => 'id'],
        'organization_position_name'           => ['name' => 'organization_position_name',           'type' => 'string', 'internal' => 'name', 'autocomplete' => true],
        'organization_position_description'    => ['name' => 'organization_position_description',    'type' => 'string', 'internal' => 'description'],
        'organization_position_descriptionraw' => ['name' => 'organization_position_descriptionraw', 'type' => 'string', 'internal' => 'descriptionRaw'],
        'organization_position_parent'         => ['name' => 'organization_position_parent',         'type' => 'int',    'internal' => 'parent'],
        'organization_position_department'     => ['name' => 'organization_position_department',     'type' => 'int',    'internal' => 'department'],
        'organization_position_status'         => ['name' => 'organization_position_status',         'type' => 'int',    'internal' => 'status'],
    ];

    /**
     * Belongs to.
     *
     * @var array<string, array{mapper:class-string, external:string, column?:string, by?:string}>
     * @since 1.0.0
     */
    public const BELONGS_TO = [
        'parent' => [
            'mapper'   => self::class,
            'external' => 'organization_position_parent',
        ],
        'department' => [
            'mapper'   => DepartmentMapper::class,
            'external' => 'organization_position_department',
        ],
    ];

    /**
     * Model to use by the mapper.
     *
     * @var class-string<T>
     * @since 1.0.0
     */
    public const MODEL = Position::class;

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'organization_position';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'organization_position_id';
}
