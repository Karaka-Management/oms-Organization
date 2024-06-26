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

use Modules\Admin\Models\Contact;
use Modules\Admin\Models\NullContact;
use Modules\Media\Models\Media;
use Modules\Media\Models\NullMedia;
use phpOMS\Stdlib\Base\Address;
use phpOMS\Stdlib\Base\NullAddress;

/**
 * Organization unit class.
 *
 * @package Modules\Organization\Models
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 */
class Unit implements \JsonSerializable
{
    /**
     * Unit ID.
     *
     * @var int
     * @since 1.0.0
     */
    public int $id = 0;

    /**
     * Name.
     *
     * @var string
     * @since 1.0.0
     */
    public string $name = '';

    /**
     * Unit image.
     *
     * @var Media
     * @since 1.0.0
     */
    public Media $image;

    /**
     * Parent
     *
     * @var null|Unit
     * @since 1.0.0
     */
    public ?self $parent = null;

    /**
     * Description.
     *
     * @var string
     * @since 1.0.0
     */
    public string $description = '';

    /**
     * Description.
     *
     * @var string
     * @since 1.0.0
     */
    public string $descriptionRaw = '';

    /**
     * Status
     *
     * @var int
     * @since 1.0.0
     */
    public int $status = Status::INACTIVE;

    public Address $mainAddress;

    /**
     * Contact data.
     *
     * @var \Modules\Admin\Models\Contact[]
     * @since 1.0.0
     */
    public array $contacts = [];

    public array $address = [];

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->image       = new NullMedia();
        $this->mainAddress = new NullAddress();
    }

    /**
     * Get the main contact element by type
     *
     * @param int $type Contact element type
     *
     * @return Contact
     *
     * @since 1.0.0
     */
    public function getContactByType(int $type) : Contact
    {
        foreach ($this->contacts as $element) {
            if ($element->type === $type) {
                return $element;
            }
        }

        return new NullContact();
    }

    /**
     * {@inheritdoc}
     */
    public function toArray() : array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'status'         => $this->status,
            'description'    => $this->description,
            'descriptionRaw' => $this->descriptionRaw,
            'parent'         => $this->parent,
            'image'          => $this->image,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize() : mixed
    {
        return $this->toArray();
    }

    use \Modules\Attribute\Models\AttributeHolderTrait;
}
