<?php
/**
 * Orange Management
 *
 * PHP Version 8.0
 *
 * @package   Modules\Organization\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\Organization\Models;

use Modules\Media\Models\Media;
use Modules\Media\Models\NullMedia;
use phpOMS\Contract\ArrayableInterface;

/**
 * Organization unit class.
 *
 * @package Modules\Organization\Models
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
class Unit implements \JsonSerializable, ArrayableInterface
{
    /**
     * Article ID.
     *
     * @var int
     * @since 1.0.0
     */
    protected int $id = 0;

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
    protected int $status = Status::INACTIVE;

    /**
     * Media files
     *
     * @var array
     * @since 1.0.0
     */
    protected array $files = [];

    /**
     * Constructor.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->image  = new NullMedia();
        $this->parent = new NullUnit();
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
     * Get media.
     *
     * @return array
     *
     * @since 1.0.0
     */
    public function getFiles() : array
    {
        return $this->files;
    }

    /**
     * Add media.
     *
     * @param Media $file Media
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function addFile(Media $file) : void
    {
        $this->files[] = $file;
    }

    /**
     * Get media file by type
     *
     * @param string $type Media type
     *
     * @return Media
     *
     * @since 1.0.0
     */
    public function getFileByType(string $type) : Media
    {
        foreach ($this->files as $file) {
            if ($file->type === $type) {
                return $file;
            }
        }

        return new NullMedia();
    }

    /**
     * Get all media files by type
     *
     * @param string $type Media type
     *
     * @return Media[]
     *
     * @since 1.0.0
     */
    public function getFilesByType(string $type) : array
    {
        $files = [];
        foreach ($this->files as $file) {
            if ($file->type === $type) {
                $files[] = $file;
            }
        }

        return $files;
    }

    /**
     * Get status
     *
     * @return int
     *
     * @since 1.0.0
     */
    public function getStatus() : int
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param int $status Status
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setStatus(int $status) : void
    {
        $this->status = $status;
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
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
