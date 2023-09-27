<?php
/**
 * Jingga
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

use Model\Setting;
use Model\SettingMapper;
use Modules\Admin\Models\Address;
use Modules\Admin\Models\AddressMapper;
use Modules\Admin\Models\NullAddress;
use Modules\Admin\Models\SettingsEnum as ModelsSettingsEnum;
use Modules\Media\Models\PathSettings;
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
use phpOMS\Localization\ISO3166TwoEnum;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
use phpOMS\Message\Http\RequestStatusCode;
use phpOMS\Message\RequestAbstract;
use phpOMS\Message\ResponseAbstract;
use phpOMS\System\MimeType;
use phpOMS\Utils\Parser\Markdown\Markdown;

/**
 * Organization Controller class.
 *
 * @package Modules\Organization
 * @license OMS License 2.0
 * @link    https://jingga.app
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
        if (($val['name'] = !$request->hasData('name'))
            || ($val['parent'] = (
                $request->hasData('parent')
                && !\is_numeric($request->getData('parent'))
            ))
            || ($val['status'] = (
                !$request->hasData('status')
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
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiUnitGet(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        /** @var Unit $unit */
        $unit = UnitMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->createStandardReturnResponse($request, $response, $unit);
    }

    /**
     * Api method to update a unit
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiUnitSet(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        /** @var Unit $old */
        $old = UnitMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $new = $this->updateUnitFromRequest($request, clone $old);
        $this->updateModel($request->header->account, $old, $new, UnitMapper::class, 'unit', $request->getOrigin());
        $this->createStandardUpdateResponse($request, $response, $new);
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
    private function updateUnitFromRequest(RequestAbstract $request, Unit $unit) : Unit
    {
        $unit->name           = $request->getDataString('name') ?? $unit->name;
        $unit->descriptionRaw = $request->getDataString('description') ?? $unit->descriptionRaw;
        $unit->description    = Markdown::parse($request->getDataString('description') ?? $unit->descriptionRaw);

        $parent       = $request->getDataInt('parent') ?? 0;
        $unit->parent = $parent === 0 ? $unit->parent : new NullUnit($parent);
        $unit->setStatus($request->getDataInt('status') ?? $unit->getStatus());

        return $unit;
    }

    /**
     * Api method to delete a unit
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiUnitDelete(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        /** @var Unit $unit */
        $unit = UnitMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->deleteModel($request->header->account, $unit, UnitMapper::class, 'unit', $request->getOrigin());
        $this->createStandardDeleteResponse($request, $response, $unit);
    }

    /**
     * Api method to create a unit
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiUnitCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateUnitCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $unit = $this->createUnitFromRequest($request);
        $this->createModel($request->header->account, $unit, UnitMapper::class, 'unit', $request->getOrigin());

        /** @var \Model\Setting $setting */
        $setting = $this->app->appSettings->get(null, SettingsEnum::GROUP_GENERATE_AUTOMATICALLY_UNIT);
        if ($setting->content === '1') {
            $internalResponse            = new HttpResponse();
            $newRequest                  = new HttpRequest();
            $newRequest->header->account = $request->header->account;
            $newRequest->setData('name', 'unit:' . \strtolower($unit->name));
            $newRequest->setData('status', GroupStatus::ACTIVE);

            $this->app->moduleManager->get('Admin')->apiGroupCreate($newRequest, $internalResponse, $data);

            /** @var \Modules\Admin\Models\Group $group */
            $group = $internalResponse->get($newRequest->uri->__toString())['response'];

            $content = \json_encode([$group->id]);
            if ($content === false) {
                $content = '[]';
            }

            $setting = new Setting(
                0,
                ModelsSettingsEnum::UNIT_DEFAULT_GROUPS,
                $content,
                unit: $unit->id,
                module: 'Admin'
            );
            $this->createModel($request->header->account, $setting, SettingMapper::class, 'setting', $request->getOrigin());
        }

        $this->createStandardCreateResponse($request, $response, $unit);
    }

    /**
     * Api method to create a unit
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiUnitMainAddressSet(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateUnitMainAddressSet($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidUpdateResponse($request, $response, $val);

            return;
        }

        /** @var Unit $unit */
        $unit    = UnitMapper::get()->with('mainAddress')->where('id', $request->getData('unit'))->execute();
        $oldUnit = clone $unit;

        if ($unit->mainAddress->id !== 0) {
            $oldAddr = clone $unit->mainAddress;
            $addr    = $this->updateUnitMainAddressFromRequest($request, $unit);
            $this->updateModel($request->header->account, $oldAddr, $addr, AddressMapper::class, 'address', $request->getOrigin());
        } else {
            $addr = $this->createUnitMainAddressFromRequest($request);
            $this->createModel($request->header->account, $addr, AddressMapper::class, 'address', $request->getOrigin());

            $unit->mainAddress = new NullAddress($addr->id);
            $this->updateModel($request->header->account, $oldUnit, $unit, UnitMapper::class, 'unit', $request->getOrigin());
        }

        $this->createStandardUpdateResponse($request, $response, $unit);
    }

    /**
     * Validate unit create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateUnitMainAddressSet(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['unit'] = !$request->hasData('unit'))
            || ($val['address'] = !$request->hasData('address'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Method to create unit from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Address
     *
     * @since 1.0.0
     */
    private function createUnitMainAddressFromRequest(RequestAbstract $request) : Address
    {
        $addr          = new Address();
        $addr->name    = $request->getDataString('legal') ?? '';
        $addr->address = $request->getDataString('address') ?? '';
        $addr->postal  = $request->getDataString('postal') ?? '';
        $addr->city    = $request->getDataString('city') ?? '';
        $addr->state   = $request->getDataString('state') ?? '';
        $addr->setCountry($request->getDataString('country') ?? ISO3166TwoEnum::_XXX);

        return $addr;
    }

    /**
     * Method to create unit from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Address
     *
     * @since 1.0.0
     */
    private function updateUnitMainAddressFromRequest(RequestAbstract $request, Unit $unit) : Address
    {
        $addr          = $unit->mainAddress;
        $addr->name    = $request->getDataString('legal') ?? '';
        $addr->address = $request->getDataString('address') ?? '';
        $addr->postal  = $request->getDataString('postal') ?? '';
        $addr->city    = $request->getDataString('city') ?? '';
        $addr->state   = $request->getDataString('state') ?? '';
        $addr->setCountry($request->getDataString('country') ?? ISO3166TwoEnum::_XXX);

        return $addr;
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
        $unit->descriptionRaw = $request->getDataString('description') ?? '';
        $unit->description    = Markdown::parse($request->getDataString('description') ?? '');

        $unit->parent = new NullUnit((int) $request->getData('parent'));
        $unit->setStatus((int) $request->getData('status'));

        if ($request->hasData('address')) {
            $addr          = new Address();
            $addr->name    = $request->getDataString('legal') ?? ($request->getDataString('name') ?? '');
            $addr->address = $request->getDataString('address') ?? '';
            $addr->postal  = $request->getDataString('postal') ?? '';
            $addr->city    = $request->getDataString('city') ?? '';
            $addr->state   = $request->getDataString('state') ?? '';
            $addr->setCountry($request->getDataString('country') ?? ISO3166TwoEnum::_XXX);

            $unit->mainAddress = $addr;
        }

        return $unit;
    }

    /**
     * Routing end-point for application behaviour.
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiUnitImageSet(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        $uploadedFiles = $request->files;
        if (empty($uploadedFiles)) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidUpdateResponse($request, $response, $uploadedFiles);

            return;
        }

        /** @var Unit $unit */
        $unit = UnitMapper::get()->where('id', $request->getDataInt('id') ?? 0)->execute();
        $old  = clone $unit;

        $path = '/Modules/Organization/' . $unit->name;

        $uploaded = $this->app->moduleManager->get('Media')->uploadFiles(
            $request->getDataList('names'),
            $request->getDataList('filenames'),
            $uploadedFiles,
            $request->header->account,
            __DIR__ . '/../../../Modules/Media/Files' . $path,
            $path,
            pathSettings: PathSettings::FILE_PATH
        );

        $unit->image = \reset($uploaded);

        $this->updateModel($request->header->account, $old, $unit, UnitMapper::class, 'unit', $request->getOrigin());
        $this->createStandardUpdateResponse($request, $response, $unit);
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
        if (($val['name'] = !$request->hasData('name'))
            || ($val['parent'] = (
                $request->hasData('parent')
                && !\is_numeric($request->getData('parent'))
            ))
            || ($val['status'] = (
                !$request->hasData('status')
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
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiPositionGet(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        /** @var Position $position */
        $position = PositionMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->createStandardReturnResponse($request, $response, $position);
    }

    /**
     * Api method to delete a position
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiPositionDelete(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        /** @var Position $position */
        $position = PositionMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->deleteModel($request->header->account, $position, PositionMapper::class, 'position', $request->getOrigin());
        $this->createStandardDeleteResponse($request, $response, $position);
    }

    /**
     * Api method to update a position
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiPositionSet(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        /** @var Position $old */
        $old = PositionMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $new = $this->updatePositionFromRequest($request, clone $old);
        $this->updateModel($request->header->account, $old, $new, PositionMapper::class, 'position', $request->getOrigin());
        $this->createStandardUpdateResponse($request, $response, $new);
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
    private function updatePositionFromRequest(RequestAbstract $request, Position $position) : Position
    {
        $position->name           = $request->getDataString('name') ?? $position->name;
        $position->descriptionRaw = $request->getDataString('description') ?? $position->descriptionRaw;
        $position->description    = Markdown::parse($request->getDataString('description') ?? $position->descriptionRaw);

        $parent           = (int) $request->getData('parent');
        $position->parent = empty($parent) ? $position->parent : new NullPosition($parent);

        $department           = (int) $request->getData('department');
        $position->department = empty($department) ? $position->department : new NullDepartment($department);
        $position->setStatus($request->getDataInt('status') ?? $position->getStatus());

        return $position;
    }

    /**
     * Api method to create a position
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiPositionCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validatePositionCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $position = $this->createPositionFromRequest($request);
        $this->createModel($request->header->account, $position, PositionMapper::class, 'position', $request->getOrigin());

        /** @var \Model\Setting $setting */
        $setting = $this->app->appSettings->get(null, SettingsEnum::GROUP_GENERATE_AUTOMATICALLY_POSITION);
        if ($setting->content === '1') {
            $newRequest                  = new HttpRequest();
            $newRequest->header->account = $request->header->account;
            $newRequest->setData('name', 'org:pos:' . \strtr(\strtolower($position->name), ' ', '_'));
            $newRequest->setData('status', GroupStatus::ACTIVE);
            $this->app->moduleManager->get('Admin')->apiGroupCreate($newRequest, $response, $data);
        }

        $this->createStandardCreateResponse($request, $response, $position);
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
        $position->name = $request->getDataString('name') ?? '';
        $position->setStatus($request->getDataInt('status') ?? Status::ACTIVE);
        $position->descriptionRaw = $request->getDataString('description') ?? '';
        $position->description    = Markdown::parse($request->getDataString('description') ?? '');
        $position->parent         = new NullPosition((int) $request->getData('parent'));
        $position->department     = new NullDepartment((int) $request->getData('department'));

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
        if (($val['name'] = !$request->hasData('name'))
            || ($val['parent'] = (
                $request->hasData('parent')
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
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiDepartmentGet(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        /** @var Department $department */
        $department = DepartmentMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->createStandardReturnResponse($request, $response, $department);
    }

    /**
     * Api method to update a department
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiDepartmentSet(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        /** @var Department $old */
        $old = DepartmentMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $new = $this->updateDepartmentFromRequest($request, clone $old);
        $this->updateModel($request->header->account, $old, $new, DepartmentMapper::class, 'department', $request->getOrigin());
        $this->createStandardUpdateResponse($request, $response, $new);
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
    private function updateDepartmentFromRequest(RequestAbstract $request, Department $department) : Department
    {
        $department->name           = $request->getDataString('name') ?? $department->name;
        $department->descriptionRaw = $request->getDataString('description') ?? $department->descriptionRaw;
        $department->description    = Markdown::parse($request->getDataString('description') ?? $department->descriptionRaw);

        $parent             = (int) $request->getData('parent');
        $department->parent = empty($parent) ? $department->parent : new NullDepartment($parent);
        $department->setStatus($request->getDataInt('status') ?? $department->getStatus());

        $unit             = (int) $request->getData('unit');
        $department->unit = empty($unit) ? $department->unit : new NullUnit($unit);

        return $department;
    }

    /**
     * Api method to delete a department
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiDepartmentDelete(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        /** @var Department $department */
        $department = DepartmentMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $this->deleteModel($request->header->account, $department, DepartmentMapper::class, 'department', $request->getOrigin());
        $this->createStandardDeleteResponse($request, $response, $department);
    }

    /**
     * Api method to create a department
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiDepartmentCreate(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        if (!empty($val = $this->validateDepartmentCreate($request))) {
            $response->header->status = RequestStatusCode::R_400;
            $this->createInvalidCreateResponse($request, $response, $val);

            return;
        }

        $department = $this->createDepartmentFromRequest($request);
        $this->createModel($request->header->account, $department, DepartmentMapper::class, 'department', $request->getOrigin());

        /** @var \Model\Setting $setting */
        $setting = $this->app->appSettings->get(null, SettingsEnum::GROUP_GENERATE_AUTOMATICALLY_DEPARTMENT);
        if ($setting->content === '1') {
            $newRequest                  = new HttpRequest();
            $newRequest->header->account = $request->header->account;
            $newRequest->setData('name', 'org:dep:' . \strtolower($department->name));
            $newRequest->setData('status', GroupStatus::ACTIVE);
            $this->app->moduleManager->get('Admin')->apiGroupCreate($newRequest, $response, $data);
        }

        $this->createStandardCreateResponse($request, $response, $department);
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

        $department->parent         = new NullDepartment((int) $request->getData('parent'));
        $department->unit           = new NullUnit($request->getDataInt('unit') ?? 1);
        $department->descriptionRaw = $request->getDataString('description') ?? '';
        $department->description    = Markdown::parse($request->getDataString('description') ?? '');

        return $department;
    }

    /**
     * Api method to find units
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiUnitFind(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        /** @var \Modules\Organization\Models\Unit[] $units */
        $units = UnitMapper::getAll()
            ->where('name', '%' . ($request->getDataString('search') ?? '') . '%', 'LIKE')
            ->execute();

        $response->header->set('Content-Type', MimeType::M_JSON, true);
        $response->set(
            $request->uri->__toString(),
            \array_values($units)
        );
    }

    /**
     * Api method to find departments
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiDepartmentFind(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        /** @var \Modules\Organization\Models\Department[] $departments */
        $departments = DepartmentMapper::getAll()
            ->where('name', '%' . ($request->getDataString('search') ?? '') . '%', 'LIKE')
            ->execute();

        $response->header->set('Content-Type', MimeType::M_JSON, true);
        $response->set(
            $request->uri->__toString(),
            \array_values($departments)
        );
    }

    /**
     * Api method to find positions
     *
     * @param RequestAbstract  $request  Request
     * @param ResponseAbstract $response Response
     * @param array            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiPositionFind(RequestAbstract $request, ResponseAbstract $response, array $data = []) : void
    {
        /** @var \Modules\Organization\Models\Position[] $positions */
        $positions = PositionMapper::getAll()
            ->where('name', '%' . ($request->getDataString('search') ?? '') . '%', 'LIKE')
            ->execute();

        $response->header->set('Content-Type', MimeType::M_JSON, true);
        $response->set(
            $request->uri->__toString(),
            \array_values($positions)
        );
    }
}
