<?php
/**
 * Jingga
 *
 * PHP Version 8.2
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
 * Organization department mapper class.
 *
 * @package Modules\Organization\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 *
 * @template T of Department
 * @extends DataMapperFactory<T>
 */
final class DepartmentMapper extends DataMapperFactory
{
    /**
     * Columns.
     *
     * @var array<string, array{name:string, type:string, internal:string, autocomplete?:bool, readonly?:bool, writeonly?:bool, annotations?:array}>
     * @since 1.0.0
     */
    public const COLUMNS = [
        'organization_department_id'             => ['name' => 'organization_department_id',             'type' => 'int',    'internal' => 'id'],
        'organization_department_name'           => ['name' => 'organization_department_name',           'type' => 'string', 'internal' => 'name', 'autocomplete' => true],
        'organization_department_description'    => ['name' => 'organization_department_description',    'type' => 'string', 'internal' => 'description'],
        'organization_department_descriptionraw' => ['name' => 'organization_department_descriptionraw', 'type' => 'string', 'internal' => 'descriptionRaw'],
        'organization_department_parent'         => ['name' => 'organization_department_parent',         'type' => 'int',    'internal' => 'parent'],
        'organization_department_status'         => ['name' => 'organization_department_status',         'type' => 'int',    'internal' => 'status'],
        'organization_department_unit'           => ['name' => 'organization_department_unit',           'type' => 'int',    'internal' => 'unit'],
    ];

    /**
     * Belongs to.
     *
     * @var array<string, array{mapper:class-string, external:string, column?:string, by?:string}>
     * @since 1.0.0
     */
    public const BELONGS_TO = [
        'unit' => [
            'mapper'   => UnitMapper::class,
            'external' => 'organization_department_unit',
        ],
        'parent' => [
            'mapper'   => self::class,
            'external' => 'organization_department_parent',
        ],
    ];

    /**
     * Model to use by the mapper.
     *
     * @var class-string<T>
     * @since 1.0.0
     */
    public const MODEL = Department::class;

    /**
     * Primary table.
     *
     * @var string
     * @since 1.0.0
     */
    public const TABLE = 'organization_department';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD = 'organization_department_id';
}
