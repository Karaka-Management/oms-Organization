<?php
/**
 * Karaka
 *
 * PHP Version 8.0
 *
 * @package   Modules\Organization
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Modules\Organization\Theme\Backend\Components\UnitTagSelector;

use phpOMS\Localization\L11nManager;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Views\View;

/**
 * Component view.
 *
 * @package Modules\Organization
 * @license OMS License 1.0
 * @link    https://karaka.app
 * @since   1.0.0
 * @codeCoverageIgnore
 */
class UnitTagSelectorView extends View
{
    /**
     * Dom id
     *
     * @var string
     * @since 1.0.0
     */
    private string $id = '';

    /**
     * Dom name
     *
     * @var string
     * @since 1.0.0
     */
    public string $name = '';

    /**
     * Is required
     *
     * @var bool
     * @since 1.0.0
     */
    private bool $isRequired = false;

    /**
     * {@inheritdoc}
     */
    public function __construct(L11nManager $l11n, RequestAbstract $request, ResponseAbstract $response)
    {
        parent::__construct($l11n, $request, $response);
        $this->setTemplate('/Modules/Organization/Theme/Backend/Components/UnitTagSelector/unit-selector');

        $view = new UnitTagSelectorPopupView($l11n, $request, $response);
        $this->addData('unit-selector-popup', $view);
    }

    /**
     * Get selector id
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
     * Is required?
     *
     * @return bool
     *
     * @since 1.0.0
     */
    public function isRequired() : bool
    {
        return $this->isRequired;
    }

    /**
     * {@inheritdoc}
     */
    public function render(...$data) : string
    {
        $this->id         = $data[0];
        $this->name       = $data[1];
        $this->isRequired = $data[2] ?? false;
        $this->getData('unit-selector-popup')->setId($this->id);

        return parent::render();
    }
}
