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
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewUnitList(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/Organization/Theme/Backend/unit-list');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004703001, $request, $response);

        $view->data['units'] = UnitMapper::getAll()
            ->with('parent')
            ->with('image')
            ->limit(25)
            ->paginate(
                'id',
                $request->getDataString('ptype'),
                $request->getDataInt('offset')
            )->executeGetArray();

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewUnitView(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/Organization/Theme/Backend/unit-view');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004703001, $request, $response);

        $selectorView                = new \Modules\Organization\Theme\Backend\Components\UnitTagSelector\UnitTagSelectorView($this->app->l11nManager, $request, $response);
        $view->data['unit-selector'] = $selectorView;

        $view->data['unit'] = UnitMapper::get()
            ->with('parent')
            ->with('mainAddress')
            ->with('address')
            ->with('contacts')
            ->with('image')
            ->where('id', (int) $request->getData('id'))
            ->execute();

        $editor               = new \Modules\Editor\Theme\Backend\Components\Editor\BaseView($this->app->l11nManager, $request, $response);
        $view->data['editor'] = $editor;

        $view->data['address-component'] = new \Modules\Admin\Theme\Backend\Components\AddressEditor\AddressView($this->app->l11nManager, $request, $response);
        $view->data['contact-component'] = new \Modules\Admin\Theme\Backend\Components\ContactEditor\ContactView($this->app->l11nManager, $request, $response);

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewOrganigram(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $response->data['Content']->head->addAsset(AssetType::CSS, 'Modules/Organization/Theme/Backend/css/styles.css?v=' . self::VERSION);

        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/Organization/Theme/Backend/organigram');

        /** @var Unit[] $units */
        $units                  = UnitMapper::getAll()->with('parent')->executeGetArray();
        $unitTree               = $this->createOrgTree($units);
        $view->data['unitTree'] = $unitTree;

        /** @var Department[] $departments */
        $departments                  = DepartmentMapper::getAll()->with('parent')->with('unit')->executeGetArray();
        $depTree                      = $this->createOrgTree($departments);
        $view->data['departmentTree'] = $depTree;

        /** @var Position[] $positions */
        $positions                  = PositionMapper::getAll()->with('parent')->with('unit')->with('department')->executeGetArray();
        $posTree                    = $this->createOrgTree($positions);
        $view->data['positionTree'] = $posTree;

        return $view;
    }

    /**
     * Create organization tree
     *
     * @param array<int, Unit|Department|Position> $components Components to form tree for
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
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewUnitCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/Organization/Theme/Backend/unit-create');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004703001, $request, $response);

        $uploadView                         = new \Modules\Media\Theme\Backend\Components\InlinePreview\BaseView($this->app->l11nManager, $request, $response);
        $view->data['media-preview-upload'] = $uploadView;

        $selectorView                = new \Modules\Organization\Theme\Backend\Components\UnitTagSelector\UnitTagSelectorView($this->app->l11nManager, $request, $response);
        $view->data['unit-selector'] = $selectorView;

        $editor               = new \Modules\Editor\Theme\Backend\Components\Editor\BaseView($this->app->l11nManager, $request, $response);
        $view->data['editor'] = $editor;

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewDepartmentList(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/Organization/Theme/Backend/department-list');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004704001, $request, $response);

        $pageLimit               = 25;
        $view->data['pageLimit'] = $pageLimit;

        $departments = DepartmentMapper::getAll()
            ->with('parent')
            ->with('unit')
            ->limit($pageLimit + 1)
            ->paginate(
                'id',
                $request->getDataString('ptype') ?? '',
                $request->getDataInt('offset')
            )->executeGetArray();

        $view->data['hasMore'] = ($count = \count($departments)) > $pageLimit;

        if ($count > $pageLimit) {
            \array_pop($departments);
        }
        $view->data['departments'] = $departments;

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewDepartmentView(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/Organization/Theme/Backend/department-view');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004704001, $request, $response);

        $selectorView                      = new \Modules\Organization\Theme\Backend\Components\DepartmentTagSelector\DepartmentTagSelectorView($this->app->l11nManager, $request, $response);
        $view->data['department-selector'] = $selectorView;

        $unitSelectorView            = new \Modules\Organization\Theme\Backend\Components\UnitTagSelector\UnitTagSelectorView($this->app->l11nManager, $request, $response);
        $view->data['unit-selector'] = $unitSelectorView;

        $view->data['department'] = DepartmentMapper::get()->with('parent')->with('unit')->where('id', (int) $request->getData('id'))->execute();

        $editor               = new \Modules\Editor\Theme\Backend\Components\Editor\BaseView($this->app->l11nManager, $request, $response);
        $view->data['editor'] = $editor;

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewDepartmentCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/Organization/Theme/Backend/department-create');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004704001, $request, $response);

        $selectorView                      = new \Modules\Organization\Theme\Backend\Components\DepartmentTagSelector\DepartmentTagSelectorView($this->app->l11nManager, $request, $response);
        $view->data['department-selector'] = $selectorView;

        $unitSelectorView            = new \Modules\Organization\Theme\Backend\Components\UnitTagSelector\UnitTagSelectorView($this->app->l11nManager, $request, $response);
        $view->data['unit-selector'] = $unitSelectorView;

        $editor               = new \Modules\Editor\Theme\Backend\Components\Editor\BaseView($this->app->l11nManager, $request, $response);
        $view->data['editor'] = $editor;

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewPositionList(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/Organization/Theme/Backend/position-list');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004705001, $request, $response);

        $view->data['positions'] = PositionMapper::getAll()
            ->with('parent')
            ->with('department')
            ->limit(25)
            ->paginate(
                'id',
                $request->getData('ptype'),
                $request->getDataInt('offset')
            )
            ->executeGetArray();

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewPositionView(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/Organization/Theme/Backend/position-view');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004705001, $request, $response);

        $selectorView                    = new \Modules\Organization\Theme\Backend\Components\PositionTagSelector\PositionTagSelectorView($this->app->l11nManager, $request, $response);
        $view->data['position-selector'] = $selectorView;

        $departmentSelectorView            = new \Modules\Organization\Theme\Backend\Components\DepartmentTagSelector\DepartmentTagSelectorView($this->app->l11nManager, $request, $response);
        $view->data['department-selector'] = $departmentSelectorView;

        $view->data['position'] = PositionMapper::get()->with('parent')->with('department')->where('id', (int) $request->getData('id'))->execute();

        $editor               = new \Modules\Editor\Theme\Backend\Components\Editor\BaseView($this->app->l11nManager, $request, $response);
        $view->data['editor'] = $editor;

        return $view;
    }

    /**
     * Routing end-point for application behavior.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return RenderableInterface
     *
     * @since 1.0.0
     * @codeCoverageIgnore
     */
    public function viewPositionCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : RenderableInterface
    {
        $view = new View($this->app->l11nManager, $request, $response);

        $view->setTemplate('/Modules/Organization/Theme/Backend/position-create');
        $view->data['nav'] = $this->app->moduleManager->get('Navigation')->createNavigationMid(1004705001, $request, $response);

        $selectorView                    = new \Modules\Organization\Theme\Backend\Components\PositionTagSelector\PositionTagSelectorView($this->app->l11nManager, $request, $response);
        $view->data['position-selector'] = $selectorView;

        $departmentSelectorView            = new \Modules\Organization\Theme\Backend\Components\DepartmentTagSelector\DepartmentTagSelectorView($this->app->l11nManager, $request, $response);
        $view->data['department-selector'] = $departmentSelectorView;

        $editor               = new \Modules\Editor\Theme\Backend\Components\Editor\BaseView($this->app->l11nManager, $request, $response);
        $view->data['editor'] = $editor;

        return $view;
    }
}
