<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Organization
 * @copyright Dennis Eichhorn
 * @license   OMS License 2.0
 * @version   1.0.0
 * @link      https://jingga.app
 */
declare(strict_types=1);

namespace Modules\Organization\Theme\Backend\Components\PositionTagSelector;

use phpOMS\Localization\L11nManager;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Views\View;

/**
 * Component view.
 *
 * @package Modules\Organization
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 * @codeCoverageIgnore
 */
class PositionTagSelectorPopupView extends View
{
    /**
     * Dom id.
     *
     * @var string
     * @since 1.0.0
     */
    private string $id = '';

    /**
     * {@inheritdoc}
     */
    public function __construct(L11nManager $l11n, RequestAbstract $request, ResponseAbstract $response)
    {
        parent::__construct($l11n, $request, $response);
        $this->setTemplate('/Modules/Organization/Theme/Backend/Components/PositionTagSelector/position-selector-popup');
    }

    /**
     * Set selctor id.
     *
     * @param string $id Id
     *
     * @return void
     *
     * @since 1.0.0
     */
    public function setId(string $id) : void
    {
        $this->id = $id;
    }

    /**
     * Get selector id.
     *
     * @return string
     *
     * @since 1.0.0
     */
    public function getId() : string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function render(mixed ...$data) : string
    {
        /** @var array{0:null|string} $data */
        $this->id = $data[0] ?? $this->id;

        return parent::render();
    }
}
