<?php
/**
 * Orange Management
 *
 * PHP Version 7.4
 *
 * @package   Modules\Organization
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://orange-management.org
 */
declare(strict_types=1);

namespace Modules\Organization\Controller;

use Modules\Organization\Models\Department;
use Modules\Organization\Models\DepartmentMapper;
use Modules\Organization\Models\NullDepartment;
use Modules\Organization\Models\NullPosition;
use Modules\Organization\Models\NullUnit;
use Modules\Organization\Models\Position;
use Modules\Organization\Models\PositionMapper;
use Modules\Organization\Models\SettingsEnum;
use Modules\Organization\Models\Status;
use Modules\Organization\Models\Unit;
use Modules\Organization\Models\UnitMapper;
use phpOMS\Account\GroupStatus;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\NotificationLevel;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\Model\Message\FormValidation;
use phpOMS\System\MimeType;
use phpOMS\Utils\Parser\Markdown\Markdown;

/**
 * Organization Controller class.
 *
 * @package Modules\Organization
 * @license OMS License 1.0
 * @link    https://orange-management.org
 * @since   1.0.0
 */
final class ApiController extends Controller
{
    /**
     * Validate unit create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateUnitCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['name'] = empty($request->getData('name')))
            || ($val['parent'] = (
                !empty($request->getData('parent'))
                && !\is_numeric($request->getData('parent'))
            ))
            || ($val['status'] = (
                $request->getData('status') === null
                || !Status::isValidValue((int) $request->getData('status'))
            ))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to get a unit
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiUnitGet(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        /** @var Unit $unit */
        $unit = UnitMapper::get((int) $request->getData('id'));
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Unit', 'Unit successfully returned.', $unit);
    }

    /**
     * Api method to update a unit
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiUnitSet(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        /** @var Unit $old */
        $old = clone UnitMapper::get((int) $request->getData('id'));
        $new = $this->updateUnitFromRequest($request);
        $this->updateModel($request->header->account, $old, $new, UnitMapper::class, 'unit', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Unit', 'Unit successfully updated.', $new);
    }

    /**
     * Method to update unit from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Unit
     *
     * @since 1.0.0
     */
    private function updateUnitFromRequest(RequestAbstract $request) : Unit
    {
        /** @var Unit $unit */
        $unit                 = UnitMapper::get((int) $request->getData('id'));
        $unit->name           = (string) ($request->getData('name') ?? $unit->name);
        $unit->descriptionRaw = (string) ($request->getData('description') ?? $unit->descriptionRaw);
        $unit->description    = Markdown::parse((string) ($request->getData('description') ?? $unit->descriptionRaw));

        $parent       = (int) $request->getData('parent');
        $unit->parent = !empty($parent) ? new NullUnit($parent) : $unit->parent;
        $unit->setStatus((int) ($request->getData('status') ?? $unit->getStatus()));

        return $unit;
    }

    /**
     * Api method to delete a unit
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiUnitDelete(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        /** @var Unit $unit */
        $unit = UnitMapper::get((int) $request->getData('id'));
        $this->deleteModel($request->header->account, $unit, UnitMapper::class, 'unit', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Unit', 'Unit successfully deleted.', $unit);
    }

    /**
     * Api method to create a unit
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiUnitCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (!empty($val = $this->validateUnitCreate($request))) {
            $response->set('unit_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $unit = $this->createUnitFromRequest($request);
        $this->createModel($request->header->account, $unit, UnitMapper::class, 'unit', $request->getOrigin());

        if ($this->app->appSettings->get(null, SettingsEnum::GROUP_GENERATE_AUTOMATICALLY_UNIT)['content'] === '1') {
            $newRequest = new HttpRequest();
            $newRequest->setData('name', 'org:unit:' . \strtolower($unit->name));
            $newRequest->setData('status', GroupStatus::ACTIVE);
            $this->app->moduleManager->get('Admin')->apiGroupCreate($newRequest, $response, $data);
        }

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Unit', 'Unit successfully created.', $unit);
    }

    /**
     * Method to create unit from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Unit
     *
     * @since 1.0.0
     */
    private function createUnitFromRequest(RequestAbstract $request) : Unit
    {
        $unit                 = new Unit();
        $unit->name           = (string) $request->getData('name');
        $unit->descriptionRaw = (string) ($request->getData('description') ?? '');
        $unit->description    = Markdown::parse((string) ($request->getData('description') ?? ''));

        $parent       = (int) $request->getData('parent');
        $unit->parent = new NullUnit($parent);
        $unit->setStatus((int) $request->getData('status'));

        return $unit;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiUnitImageSet(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $uploadedFiles = $request->getFiles() ?? [];
        if (empty($uploadedFiles)) {
            $this->fillJsonResponse($request, $response, NotificationLevel::ERROR, 'Unit', 'Invalid unit image', $uploadedFiles);
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        /** @var Unit $unit */
        $unit = UnitMapper::get((int) ($request->getData('id') ?? 0));
        $old  = clone $unit;

        $uploaded = $this->app->moduleManager->get('Media')->uploadFiles(
            $request->getData('name') ?? '',
            $uploadedFiles,
            $request->header->account,
            'Modules/Media/Files',
            '/Modules/Organization'
        );

        $unit->image = \reset($uploaded);

        $this->updateModel($request->header->account, $old, $unit, UnitMapper::class, 'unit', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Unit', 'Unit image successfully updated', $unit);
    }

    /**
     * Validate position create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validatePositionCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['name'] = empty($request->getData('name')))
            || ($val['parent'] = (
                !empty($request->getData('parent'))
                && !\is_numeric($request->getData('parent'))
            ))
            || ($val['status'] = (
                $request->getData('status') === null
                || !Status::isValidValue((int) $request->getData('status'))
            ))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to get a position
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiPositionGet(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        /** @var Position $position */
        $position = PositionMapper::get((int) $request->getData('id'));
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Position', 'Position successfully returned.', $position);
    }

    /**
     * Api method to delete a position
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiPositionDelete(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        /** @var Position $position */
        $position = PositionMapper::get((int) $request->getData('id'));
        $this->deleteModel($request->header->account, $position, PositionMapper::class, 'position', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Position', 'Position successfully deleted.', $position);
    }

    /**
     * Api method to update a position
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiPositionSet(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        /** @var Position $old */
        $old = clone PositionMapper::get((int) $request->getData('id'));
        $new = $this->updatePositionFromRequest($request);
        $this->updateModel($request->header->account, $old, $new, PositionMapper::class, 'position', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Position', 'Position successfully updated.', $new);
    }

    /**
     * Method to update position from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Position
     *
     * @since 1.0.0
     */
    private function updatePositionFromRequest(RequestAbstract $request) : Position
    {
        /** @var Position $position */
        $position                 = PositionMapper::get((int) $request->getData('id'));
        $position->name           = (string) ($request->getData('name') ?? $position->name);
        $position->descriptionRaw = (string) ($request->getData('description') ?? $position->descriptionRaw);
        $position->description    = Markdown::parse((string) ($request->getData('description') ?? $position->descriptionRaw));

        $parent           = (int) $request->getData('parent');
        $position->parent = !empty($parent) ? new NullPosition($parent) : $position->parent;

        $department           = (int) $request->getData('department');
        $position->department = !empty($department) ? new NullDepartment($department) : $position->department;
        $position->setStatus((int) ($request->getData('status') ?? $position->getStatus()));

        return $position;
    }

    /**
     * Api method to create a position
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiPositionCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (!empty($val = $this->validatePositionCreate($request))) {
            $response->set('position_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $position = $this->createPositionFromRequest($request);
        $this->createModel($request->header->account, $position, PositionMapper::class, 'position', $request->getOrigin());

        if ($this->app->appSettings->get(null, SettingsEnum::GROUP_GENERATE_AUTOMATICALLY_POSITION)['content'] === '1') {
            $newRequest = new HttpRequest();
            $newRequest->setData('name', 'org:pos:' . \strtolower($position->name));
            $newRequest->setData('status', GroupStatus::ACTIVE);
            $this->app->moduleManager->get('Admin')->apiGroupCreate($newRequest, $response, $data);
        }

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Position', 'Position successfully created.', $position);
    }

    /**
     * Method to create position from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Position
     *
     * @since 1.0.0
     */
    private function createPositionFromRequest(RequestAbstract $request) : Position
    {
        $position       = new Position();
        $position->name = (string) ($request->getData('name'));
        $position->setStatus((int) $request->getData('status'));
        $position->descriptionRaw = (string) ($request->getData('description') ?? '');
        $position->description    = Markdown::parse((string) ($request->getData('description') ?? ''));

        $parent           = (int) $request->getData('parent');
        $position->parent = new NullPosition($parent);

        $department           = (int) $request->getData('department');
        $position->department = new NullDepartment($department);

        return $position;
    }

    /**
     * Method to validate department creation from request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateDepartmentCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['name'] = empty($request->getData('name')))
            || ($val['parent'] = (
                !empty($request->getData('parent'))
                && !\is_numeric($request->getData('parent'))
            ))
            || ($val['unit'] = (
                !\is_numeric($request->getData('unit'))
            ))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to get a department
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiDepartmentGet(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        /** @var Department $department */
        $department = DepartmentMapper::get((int) $request->getData('id'));
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Department', 'Department successfully returned.', $department);
    }

    /**
     * Api method to update a department
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiDepartmentSet(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        /** @var Department $old */
        $old = clone DepartmentMapper::get((int) $request->getData('id'));
        $new = $this->updateDepartmentFromRequest($request);
        $this->updateModel($request->header->account, $old, $new, DepartmentMapper::class, 'department', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Department', 'Department successfully updated.', $new);
    }

    /**
     * Method to update department from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Department
     *
     * @since 1.0.0
     */
    private function updateDepartmentFromRequest(RequestAbstract $request) : Department
    {
        /** @var Department $department */
        $department                 = DepartmentMapper::get((int) $request->getData('id'));
        $department->name           = (string) ($request->getData('name') ?? $department->name);
        $department->descriptionRaw = (string) ($request->getData('description') ?? $department->descriptionRaw);
        $department->description    = Markdown::parse((string) ($request->getData('description') ?? $department->descriptionRaw));

        $parent             = (int) $request->getData('parent');
        $department->parent = !empty($parent) ? new NullDepartment($parent) : $department->parent;
        $department->setStatus((int) ($request->getData('status') ?? $department->getStatus()));

        $unit             = (int) $request->getData('unit');
        $department->unit = !empty($unit) ? new NullUnit($unit) : $department->unit;

        return $department;
    }

    /**
     * Api method to delete a department
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiDepartmentDelete(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        /** @var Department $department */
        $department = DepartmentMapper::get((int) $request->getData('id'));
        $this->deleteModel($request->header->account, $department, DepartmentMapper::class, 'department', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Department', 'Department successfully deleted.', $department);
    }

    /**
     * Api method to create a department
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiDepartmentCreate(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        if (!empty($val = $this->validateDepartmentCreate($request))) {
            $response->set('department_create', new FormValidation($val));
            $response->header->status = RequestStatusCode::R_400;

            return;
        }

        $department = $this->createDepartmentFromRequest($request);
        $this->createModel($request->header->account, $department, DepartmentMapper::class, 'department', $request->getOrigin());

        if ($this->app->appSettings->get(null, SettingsEnum::GROUP_GENERATE_AUTOMATICALLY_DEPARTMENT)['content'] === '1') {
            $newRequest = new HttpRequest();
            $newRequest->setData('name', 'org:dep:' . \strtolower($department->name));
            $newRequest->setData('status', GroupStatus::ACTIVE);
            $this->app->moduleManager->get('Admin')->apiGroupCreate($newRequest, $response, $data);
        }

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Department', 'Department successfully created.', $department);
    }

    /**
     * Method to create a department from a request
     *
     * @param RequestAbstract $request Request
     *
     * @return Department
     *
     * @since 1.0.0
     */
    private function createDepartmentFromRequest(RequestAbstract $request) : Department
    {
        $department       = new Department();
        $department->name = (string) $request->getData('name');
        $department->setStatus((int) $request->getData('status'));

        $parent                     = (int) $request->getData('parent');
        $department->parent         = new NullDepartment($parent);
        $department->unit           = new NullUnit((int) ($request->getData('unit') ?? 1));
        $department->descriptionRaw = (string) ($request->getData('description') ?? '');
        $department->description    = Markdown::parse((string) ($request->getData('description') ?? ''));

        return $department;
    }

    /**
     * Api method to find units
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiUnitFind(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $response->header->set('Content-Type', MimeType::M_JSON, true);
        $response->set(
            $request->uri->__toString(),
            \array_values(
                UnitMapper::find((string) ($request->getData('search') ?? ''))
            )
        );
    }

    /**
     * Api method to find departments
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiDepartmentFind(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $response->header->set('Content-Type', MimeType::M_JSON, true);
        $response->set(
            $request->uri->__toString(),
            \array_values(
                DepartmentMapper::find((string) ($request->getData('search') ?? ''))
            )
        );
    }

    /**
     * Api method to find positions
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiPositionFind(RequestAbstract $request, ResponseAbstract $response, $data = null) : void
    {
        $response->header->set('Content-Type', MimeType::M_JSON, true);
        $response->set(
            $request->uri->__toString(),
            \array_values(
                PositionMapper::find((string) ($request->getData('search') ?? ''))
            )
        );
    }
}
