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

/**
 * Unit class.
 *
 * @package Modules\Organization\Models
 * @license OMS License 1.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class UnitAttribute implements \JsonSerializable
{
    /**
     * Id.
     *
     * @var int
     * @since 1.0.0
     */
    protected int $id = 0;

    /**
     * Unit this attribute belongs to
     *
     * @var int
     * @since 1.0.0
     */
    public int $unit = 0;

    /**
     * Attribute type the attribute belongs to
     *
     * @var UnitAttributeType
     * @since 1.0.0
     */
    public UnitAttributeType $type;

    /**
     * Attribute value the attribute belongs to
     *
     * @var UnitAttributeValue
     * @since 1.0.0
     */
    public UnitAttributeValue $value;

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->type  = new NullUnitAttributeType();
        $this->value = new NullUnitAttributeValue();
    }

    /**
     * Get id
     *
     * @return int
     *
     * @since 1.0.0
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        return [
            'id'    => $this->id,
            'unit'  => $this->unit,
            'type'  => $this->type,
            'value' => $this->value,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize() : mixed
    {
        return $this->toArray();
    }
}
