<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Organization
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
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
 * @license OMS License 1.0
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
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004703001, $request, $response));

        $mapper = UnitMapper::getAll()->with('parent')->with('image')->limit(25);

        if ($request->getData('ptype') === 'p') {
            $view->setData('units', $mapper->where('id', (int) ($request->getData('id') ?? 0), '<')->execute());
        } elseif ($request->getData('ptype') === 'n') {
            $view->setData('units', $mapper->where('id', (int) ($request->getData('id') ?? 0), '>')->execute());
        } else {
            $view->setData('units', $mapper->where('id', 0, '>')->execute());
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
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004703001, $request, $response));

        $selectorView = new \Modules\Organization\Theme\Backend\Components\UnitTagSelector\UnitTagSelectorView($this->app->l11nManager, $request, $response);
        $view->addData('unit-selector', $selectorView);
        $view->addData('unit', UnitMapper::get()->with('parent')->with('image')->where('id', (int) $request->getData('id'))->execute());

        $editor = new \Modules\Editor\Theme\Backend\Components\Editor\BaseView($this->app->l11nManager, $request, $response);
        $view->addData('editor', $editor);

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
        $view->setData('unitTree', $unitTree);

        /** @var Department[] $departments */
        $departments = DepartmentMapper::getAll()->with('parent')->with('unit')->execute();
        $depTree     = $this->createOrgTree($departments);
        $view->setData('departmentTree', $depTree);

        /** @var Position[] $positions */
        $positions = PositionMapper::getAll()->with('parent')->with('unit')->with('department')->execute();
        $posTree   = $this->createOrgTree($positions);
        $view->setData('positionTree', $posTree);

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
            $ref = null;
            if ($component instanceof Department) {
                $ref = $component->unit->getId();
            } elseif ($component instanceof Position) {
                $ref = $component->department->getId();
            }

            if (!isset($tree[$ref])) {
                $tree[$ref] = [];
            }

            if (!isset($tree[$ref][$component->getId()])) {
                $tree[$ref][$component->getId()] = ['obj' => null, 'children' => [], 'index' => 0];
            }

            $tree[$ref][$component->getId()]['obj'] = $component;

            $parent = $component->parent->getId();
            if ($parent !== 0
                && (!($component instanceof Position) // parent could be in different department then ignore
                    || $component->parent->department->getId() === $component->department->getId()
                )
            ) {
                if (!isset($tree[$ref][$parent])) {
                    $tree[$ref][$parent] = ['obj' => null, 'children' => [], 'index' => 0];
                }

                /** @phpstan-ignore-next-line */
                $tree[$ref][$parent]['children'][] = &$tree[$ref][$component->getId()];
            }
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
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004703001, $request, $response));

        $uploadView = new \Modules\Media\Theme\Backend\Components\InlinePreview\BaseView($this->app->l11nManager, $request, $response);
        $view->addData('media-preview-upload', $uploadView);

        $selectorView = new \Modules\Organization\Theme\Backend\Components\UnitTagSelector\UnitTagSelectorView($this->app->l11nManager, $request, $response);
        $view->addData('unit-selector', $selectorView);

        $editor = new \Modules\Editor\Theme\Backend\Components\Editor\BaseView($this->app->l11nManager, $request, $response);
        $view->addData('editor', $editor);

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
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004704001, $request, $response));

        $pageLimit = 25;
        $view->addData('pageLimit', $pageLimit);

        $mapper = DepartmentMapper::getAll()->with('parent')->with('unit')->limit($pageLimit + 1);

        /** @var \Modules\Organization\Models\Department[] $departments */
        $departments = [];
        if ($request->getData('ptype') === 'p') {
            $departments = $mapper->where('id', (int) ($request->getData('id') ?? 0), '<')->execute();
        } elseif ($request->getData('ptype') === 'n') {
            $departments = $mapper->where('id', (int) ($request->getData('id') ?? 0), '>')->execute();
        } else {
            $departments = $mapper->where('id', 0, '>')->execute();
        }

        $view->setData('hasMore', ($count = \count($departments)) > $pageLimit);

        if ($count > $pageLimit) {
            \array_pop($departments);
        }
        $view->setData('departments', $departments);

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
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004704001, $request, $response));

        $selectorView = new \Modules\Organization\Theme\Backend\Components\DepartmentTagSelector\DepartmentTagSelectorView($this->app->l11nManager, $request, $response);
        $view->addData('department-selector', $selectorView);

        $unitSelectorView = new \Modules\Organization\Theme\Backend\Components\UnitTagSelector\UnitTagSelectorView($this->app->l11nManager, $request, $response);
        $view->addData('unit-selector', $unitSelectorView);

        $view->addData('department', DepartmentMapper::get()->with('parent')->with('unit')->where('id', (int) $request->getData('id'))->execute());

        $editor = new \Modules\Editor\Theme\Backend\Components\Editor\BaseView($this->app->l11nManager, $request, $response);
        $view->addData('editor', $editor);

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
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004704001, $request, $response));

        $selectorView = new \Modules\Organization\Theme\Backend\Components\DepartmentTagSelector\DepartmentTagSelectorView($this->app->l11nManager, $request, $response);
        $view->addData('department-selector', $selectorView);

        $unitSelectorView = new \Modules\Organization\Theme\Backend\Components\UnitTagSelector\UnitTagSelectorView($this->app->l11nManager, $request, $response);
        $view->addData('unit-selector', $unitSelectorView);

        $editor = new \Modules\Editor\Theme\Backend\Components\Editor\BaseView($this->app->l11nManager, $request, $response);
        $view->addData('editor', $editor);

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
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004705001, $request, $response));

        if ($request->getData('ptype') === 'p') {
            $view->setData('positions', PositionMapper::getAll()->with('parent')->with('department')->where('id', (int) ($request->getData('id') ?? 0), '<')->limit(25)->execute());
        } elseif ($request->getData('ptype') === 'n') {
            $view->setData('positions', PositionMapper::getAll()->with('parent')->with('department')->where('id', (int) ($request->getData('id') ?? 0), '>')->limit(25)->execute());
        } else {
            $view->setData('positions', PositionMapper::getAll()->with('parent')->with('department')->where('id', 0, '>')->limit(25)->execute());
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
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004705001, $request, $response));

        $selectorView = new \Modules\Organization\Theme\Backend\Components\PositionTagSelector\PositionTagSelectorView($this->app->l11nManager, $request, $response);
        $view->addData('position-selector', $selectorView);

        $departmentSelectorView = new \Modules\Organization\Theme\Backend\Components\DepartmentTagSelector\DepartmentTagSelectorView($this->app->l11nManager, $request, $response);
        $view->addData('department-selector', $departmentSelectorView);

        $view->addData('position', PositionMapper::get()->with('parent')->with('department')->where('id', (int) $request->getData('id'))->execute());

        $editor = new \Modules\Editor\Theme\Backend\Components\Editor\BaseView($this->app->l11nManager, $request, $response);
        $view->addData('editor', $editor);

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
        $view->addData('nav', $this->app->moduleManager->get('Navigation')->createNavigationMid(1004705001, $request, $response));

        $selectorView = new \Modules\Organization\Theme\Backend\Components\PositionTagSelector\PositionTagSelectorView($this->app->l11nManager, $request, $response);
        $view->addData('position-selector', $selectorView);

        $departmentSelectorView = new \Modules\Organization\Theme\Backend\Components\DepartmentTagSelector\DepartmentTagSelectorView($this->app->l11nManager, $request, $response);
        $view->addData('department-selector', $departmentSelectorView);

        $editor = new \Modules\Editor\Theme\Backend\Components\Editor\BaseView($this->app->l11nManager, $request, $response);
        $view->addData('editor', $editor);

        return $view;
    }
}
