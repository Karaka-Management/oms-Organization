<?php
/**
 * Jingga
 *
 * PHP Version 8.2
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
class PositionTagSelectorView extends View
{
    /**
     * Dom id
     *
     * @var string
     * @since 1.0.0
     */
    public string $id = '';

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
    public bool $isRequired = false;

    /**
     * {@inheritdoc}
     */
    public function __construct(L11nManager $l11n, RequestAbstract $request, ResponseAbstract $response)
    {
        parent::__construct($l11n, $request, $response);
        $this->setTemplate('/Modules/Organization/Theme/Backend/Components/PositionTagSelector/position-selector');

        $view = new PositionTagSelectorPopupView($l11n, $request, $response);
        $this->addData('position-selector-popup', $view);
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
    public function render(mixed ...$data) : string
    {
        /** @var array{0:string, 1:string, 2:null|bool} $data */
        $this->id         = $data[0];
        $this->name       = $data[1];
        $this->isRequired = $data[2] ?? false;

        $this->getData('position-selector-popup')->id = $this->id;

        return parent::render();
    }
}
