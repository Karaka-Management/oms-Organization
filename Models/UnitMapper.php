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
 * @link      https://karaka.app
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
 * @link    https://karaka.app
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
        'organization_unit_id'             => ['name' => 'organization_unit_id',             'type' => 'int',    'internal' => 'id'],
        'organization_unit_name'           => ['name' => 'organization_unit_name',           'type' => 'string', 'internal' => 'name', 'autocomplete' => true],
        'organization_unit_image'          => ['name' => 'organization_unit_image',          'type' => 'int',    'internal' => 'image'],
        'organization_unit_description'    => ['name' => 'organization_unit_description',    'type' => 'string', 'internal' => 'description'],
        'organization_unit_descriptionraw' => ['name' => 'organization_unit_descriptionraw', 'type' => 'string', 'internal' => 'descriptionRaw'],
        'organization_unit_parent'         => ['name' => 'organization_unit_parent',         'type' => 'int',    'internal' => 'parent'],
        'organization_unit_status'         => ['name' => 'organization_unit_status',         'type' => 'int',    'internal' => 'status'],
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
            'external' => 'organization_unit_image',
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
            'external' => 'organization_unit_parent',
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
    public const TABLE = 'organization_unit';

    /**
     * Primary field name.
     *
     * @var string
     * @since 1.0.0
     */
    public const PRIMARYFIELD ='organization_unit_id';
}
