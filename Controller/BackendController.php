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

namespace Modules\Organization\Controller;

use Modules\Organization\Models\Department;
use Modules\Organization\Models\DepartmentMapper;
use Modules\Organization\Models\Position;
use Modules\Organization\Models\PositionMapper;
use Modules\Organization\Models\Unit;
use Modules\Organization\Models\UnitMapper;
use phpOMS\Asset\AssetType;
use phpOMS\Contract\RenderableInterface;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Views\View;

/**
 * Organization Controller class.
 *
 * @package Modules\Organization
 * @license OMS License 2.0
 * @link    https://jingga.app
 * @since   1.0.0
 * @codeCoverageIgnore
 */
final class BackendController extends Controller
{
    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewUnitList(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/Organization/Theme/Backend/unit-list');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004703001, $request, $response);

        $mapper = UnitMapper::getAll()
            ->with('parent')
            ->with('image')
            ->limit(25);

        if ($request->getData('ptype') === 'p') {
            $view->data['units'] = $mapper->where('id', $request->getDataInt('id') ?? 0, '<')->execute();
        } elseif ($request->getData('ptype') === 'n') {
            $view->data['units'] = $mapper->where('id', $request->getDataInt('id') ?? 0, '>')->execute();
        } else {
            $view->data['units'] = $mapper->where('id', 0, '>')->execute();
        }

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewUnitProfile(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/Organization/Theme/Backend/unit-profile');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004703001, $request, $response);

        $selectorView = new \Modules\Organization\Theme\Backend\Components\UnitTagSelector\UnitTagSelectorView($this->app->l11nManager, $request, $response);
        $view->data['unit-selector'] = $selectorView;

        $unit = UnitMapper::get()
            ->with('parent')
            ->with('mainAddress')
            ->with('image')
            ->where('id', (int) $request->getData('id'))
            ->execute();

        $view->data['unit'] = $unit;

        $editor = new \Modules\Editor\Theme\Backend\Components\Editor\BaseView($this->app->l11nManager, $request, $response);
        $view->data['editor'] = $editor;

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewOrganigram(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $response->get('Content')
            ->getData('head')
            ->addAsset(AssetType::CSS, 'Modules/Organization/Theme/Backend/css/styles.css?v=1.0.0');

        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/Organization/Theme/Backend/organigram');

        /** @var Unit[] $units */
        $units    = UnitMapper::getAll()->with('parent')->execute();
        $unitTree = $this->createOrgTree($units);
        $view->data['unitTree'] = $unitTree;

        /** @var Department[] $departments */
        $departments = DepartmentMapper::getAll()->with('parent')->with('unit')->execute();
        $depTree     = $this->createOrgTree($departments);
        $view->data['departmentTree'] = $depTree;

        /** @var Position[] $positions */
        $positions = PositionMapper::getAll()->with('parent')->with('unit')->with('department')->execute();
        $posTree   = $this->createOrgTree($positions);
        $view->data['positionTree'] = $posTree;

        return $view;
    }

    /**
     * Create organization tree
     *
     * @param array<int, Unit|Department|Position> $components Componants to form tree for
     *
     * @return array
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    private function createOrgTree(array $components) : array
    {
        $tree = [];
        foreach ($components as $component) {
            $ref = 0;
            if ($component instanceof Department) {
                $ref = $component->unit->id;
            } elseif ($component instanceof Position) {
                $ref = $component->department->id;
            }

            $tree[$component->id] = [
                'obj' => $component,
                'ref' => $ref,
            ];
        }

        return $tree;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewUnitCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/Organization/Theme/Backend/unit-create');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004703001, $request, $response);

        $uploadView = new \Modules\Media\Theme\Backend\Components\InlinePreview\BaseView($this->app->l11nManager, $request, $response);
        $view->data['media-preview-upload'] = $uploadView;

        $selectorView = new \Modules\Organization\Theme\Backend\Components\UnitTagSelector\UnitTagSelectorView($this->app->l11nManager, $request, $response);
        $view->data['unit-selector'] = $selectorView;

        $editor = new \Modules\Editor\Theme\Backend\Components\Editor\BaseView($this->app->l11nManager, $request, $response);
        $view->data['editor'] = $editor;

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewDepartmentList(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/Organization/Theme/Backend/department-list');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004704001, $request, $response);

        $pageLimit = 25;
        $view->data['pageLimit'] = $pageLimit;

        $mapper = DepartmentMapper::getAll()->with('parent')->with('unit')->limit($pageLimit + 1);

        if ($request->getData('ptype') === 'p') {
            $mapper->where('id', $request->getDataInt('id') ?? 0, '<');
        } elseif ($request->getData('ptype') === 'n') {
            $mapper->where('id', $request->getDataInt('id') ?? 0, '>');
        } else {
            $mapper->where('id', 0, '>');
        }

        /** @var \Modules\Organization\Models\Department[] $departments */
        $departments = $mapper->execute();

        $view->data['hasMore'] = ($count = \count($departments)) > $pageLimit;

        if ($count > $pageLimit) {
            \array_pop($departments);
        }
        $view->data['departments'] = $departments;

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewDepartmentProfile(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/Organization/Theme/Backend/department-profile');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004704001, $request, $response);

        $selectorView = new \Modules\Organization\Theme\Backend\Components\DepartmentTagSelector\DepartmentTagSelectorView($this->app->l11nManager, $request, $response);
        $view->data['department-selector'] = $selectorView;

        $unitSelectorView = new \Modules\Organization\Theme\Backend\Components\UnitTagSelector\UnitTagSelectorView($this->app->l11nManager, $request, $response);
        $view->data['unit-selector'] = $unitSelectorView;

        $view->data['department'] = DepartmentMapper::get()->with('parent')->with('unit')->where('id', (int) $request->getData('id'))->execute();

        $editor = new \Modules\Editor\Theme\Backend\Components\Editor\BaseView($this->app->l11nManager, $request, $response);
        $view->data['editor'] = $editor;

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewDepartmentCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/Organization/Theme/Backend/department-create');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004704001, $request, $response);

        $selectorView = new \Modules\Organization\Theme\Backend\Components\DepartmentTagSelector\DepartmentTagSelectorView($this->app->l11nManager, $request, $response);
        $view->data['department-selector'] = $selectorView;

        $unitSelectorView = new \Modules\Organization\Theme\Backend\Components\UnitTagSelector\UnitTagSelectorView($this->app->l11nManager, $request, $response);
        $view->data['unit-selector'] = $unitSelectorView;

        $editor = new \Modules\Editor\Theme\Backend\Components\Editor\BaseView($this->app->l11nManager, $request, $response);
        $view->data['editor'] = $editor;

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewPositionList(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/Organization/Theme/Backend/position-list');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004705001, $request, $response);

        if ($request->getData('ptype') === 'p') {
            $view->data['positions'] = PositionMapper::getAll()->with('parent')->with('department')->where('id', $request->getDataInt('id') ?? 0, '<')->limit(25)->execute();
        } elseif ($request->getData('ptype') === 'n') {
            $view->data['positions'] = PositionMapper::getAll()->with('parent')->with('department')->where('id', $request->getDataInt('id') ?? 0, '>')->limit(25)->execute();
        } else {
            $view->data['positions'] = PositionMapper::getAll()->with('parent')->with('department')->where('id', 0, '>')->limit(25)->execute();
        }

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewPositionProfile(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/Organization/Theme/Backend/position-profile');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004705001, $request, $response);

        $selectorView = new \Modules\Organization\Theme\Backend\Components\PositionTagSelector\PositionTagSelectorView($this->app->l11nManager, $request, $response);
        $view->data['position-selector'] = $selectorView;

        $departmentSelectorView = new \Modules\Organization\Theme\Backend\Components\DepartmentTagSelector\DepartmentTagSelectorView($this->app->l11nManager, $request, $response);
        $view->data['department-selector'] = $departmentSelectorView;

        $view->data['position'] = PositionMapper::get()->with('parent')->with('department')->where('id', (int) $request->getData('id'))->execute();

        $editor = new \Modules\Editor\Theme\Backend\Components\Editor\BaseView($this->app->l11nManager, $request, $response);
        $view->data['editor'] = $editor;

        return $view;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewPositionCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/Organization/Theme/Backend/position-create');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004705001, $request, $response);

        $selectorView = new \Modules\Organization\Theme\Backend\Components\PositionTagSelector\PositionTagSelectorView($this->app->l11nManager, $request, $response);
        $view->data['position-selector'] = $selectorView;

        $departmentSelectorView = new \Modules\Organization\Theme\Backend\Components\DepartmentTagSelector\DepartmentTagSelectorView($this->app->l11nManager, $request, $response);
        $view->data['department-selector'] = $departmentSelectorView;

        $editor = new \Modules\Editor\Theme\Backend\Components\Editor\BaseView($this->app->l11nManager, $request, $response);
        $view->data['editor'] = $editor;

        return $view;
    }
}
