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

use Model\Setting;
use Model\SettingMapper;
use Modules\Admin\Models\Address;
use Modules\Admin\Models\AddressMapper;
use Modules\Admin\Models\NullAddress;
use Modules\Admin\Models\SettingsEnum as ModelsSettingsEnum;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeType;
use Modules\Attribute\Models\AttributeValue;
use Modules\Attribute\Models\NullAttributeType;
use Modules\Attribute\Models\NullAttributeValue;
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
use Modules\Organization\Models\UnitAttributeMapper;
use Modules\Organization\Models\UnitAttributeTypeL11nMapper;
use Modules\Organization\Models\UnitAttributeTypeMapper;
use Modules\Organization\Models\UnitAttributeValueL11nMapper;
use Modules\Organization\Models\UnitAttributeValueMapper;
use Modules\Organization\Models\UnitMapper;
use phpOMS\Account\GroupStatus;
use phpOMS\Localization\BaseStringL11n;
use phpOMS\Localization\ISO3166TwoEnum;
use phpOMS\Localization\ISO639x1Enum;
use phpOMS\Message\Http\HttpRequest;
use phpOMS\Message\Http\HttpResponse;
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
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiUnitGet(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var Unit $unit */
        $unit = UnitMapper::get()->where('id', (int) $request->getData('id'))->execute();
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
    public function apiUnitSet(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var Unit $old */
        $old = UnitMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $new = $this->updateUnitFromRequest($request, clone $old);
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
    private function updateUnitFromRequest(RequestAbstract $request, Unit $unit) : Unit
    {
        $unit->name           = (string) ($request->getData('name') ?? $unit->name);
        $unit->descriptionRaw = (string) ($request->getData('description') ?? $unit->descriptionRaw);
        $unit->description    = Markdown::parse((string) ($request->getData('description') ?? $unit->descriptionRaw));

        $parent       = (int) $request->getData('parent');
        $unit->parent = !empty($parent) ? new NullUnit($parent) : $unit->parent;
        $unit->setStatus($request->getDataInt('status') ?? $unit->getStatus());

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
    public function apiUnitDelete(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var Unit $unit */
        $unit = UnitMapper::get()->where('id', (int) $request->getData('id'))->execute();
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
    public function apiUnitCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateUnitCreate($request))) {
            $response->data['unit_create'] = new FormValidation($val);
            $response->header->status      = RequestStatusCode::R_400;

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

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Unit', 'Unit successfully created.', $unit);
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
    public function apiUnitMainAddressSet(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateUnitMainAddressSet($request))) {
            $response->data['unit_address_set'] = new FormValidation($val);
            $response->header->status           = RequestStatusCode::R_400;

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

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Address', 'Address successfully set.', $unit);
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
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiUnitImageSet(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        $uploadedFiles = $request->files;
        if (empty($uploadedFiles)) {
            $this->fillJsonResponse($request, $response, NotificationLevel::ERROR, 'Unit', 'Invalid unit image', $uploadedFiles);
            $response->header->status = RequestStatusCode::R_400;

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
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiPositionGet(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var Position $position */
        $position = PositionMapper::get()->where('id', (int) $request->getData('id'))->execute();
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
    public function apiPositionDelete(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var Position $position */
        $position = PositionMapper::get()->where('id', (int) $request->getData('id'))->execute();
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
    public function apiPositionSet(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var Position $old */
        $old = PositionMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $new = $this->updatePositionFromRequest($request, clone $old);
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
    private function updatePositionFromRequest(RequestAbstract $request, Position $position) : Position
    {
        $position->name           = (string) ($request->getData('name') ?? $position->name);
        $position->descriptionRaw = (string) ($request->getData('description') ?? $position->descriptionRaw);
        $position->description    = Markdown::parse((string) ($request->getData('description') ?? $position->descriptionRaw));

        $parent           = (int) $request->getData('parent');
        $position->parent = !empty($parent) ? new NullPosition($parent) : $position->parent;

        $department           = (int) $request->getData('department');
        $position->department = !empty($department) ? new NullDepartment($department) : $position->department;
        $position->setStatus($request->getDataInt('status') ?? $position->getStatus());

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
    public function apiPositionCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validatePositionCreate($request))) {
            $response->data['position_create'] = new FormValidation($val);
            $response->header->status          = RequestStatusCode::R_400;

            return;
        }

        $position = $this->createPositionFromRequest($request);
        $this->createModel($request->header->account, $position, PositionMapper::class, 'position', $request->getOrigin());

        /** @var \Model\Setting $setting */
        $setting = $this->app->appSettings->get(null, SettingsEnum::GROUP_GENERATE_AUTOMATICALLY_POSITION);
        if ($setting->content === '1') {
            $newRequest                  = new HttpRequest();
            $newRequest->header->account = $request->header->account;
            $newRequest->setData('name', 'org:pos:' . \str_replace(' ', '_', \strtolower($position->name)));
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
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiDepartmentGet(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var Department $department */
        $department = DepartmentMapper::get()->where('id', (int) $request->getData('id'))->execute();
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
    public function apiDepartmentSet(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var Department $old */
        $old = DepartmentMapper::get()->where('id', (int) $request->getData('id'))->execute();
        $new = $this->updateDepartmentFromRequest($request, clone $old);
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
    private function updateDepartmentFromRequest(RequestAbstract $request, Department $department) : Department
    {
        $department->name           = (string) ($request->getData('name') ?? $department->name);
        $department->descriptionRaw = (string) ($request->getData('description') ?? $department->descriptionRaw);
        $department->description    = Markdown::parse((string) ($request->getData('description') ?? $department->descriptionRaw));

        $parent             = (int) $request->getData('parent');
        $department->parent = !empty($parent) ? new NullDepartment($parent) : $department->parent;
        $department->setStatus($request->getDataInt('status') ?? $department->getStatus());

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
    public function apiDepartmentDelete(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        /** @var Department $department */
        $department = DepartmentMapper::get()->where('id', (int) $request->getData('id'))->execute();
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
    public function apiDepartmentCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateDepartmentCreate($request))) {
            $this->fillJsonResponse($request, $response, NotificationLevel::OK, '', 'Invalid form data.', new FormValidation($val));
            $response->header->set('Content-Type', MimeType::M_JSON . '; charset=utf-8', true);
            $response->header->status = RequestStatusCode::R_400;

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
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiUnitFind(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
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
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiDepartmentFind(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
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
     * @param mixed            $data     Generic data
     *
     * @return void
     *
     * @api
     *
     * @since 1.0.0
     */
    public function apiPositionFind(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
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

    /**
     * Api method to create item attribute
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
    public function apiUnitAttributeCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateUnitAttributeCreate($request))) {
            $response->data['attribute_create'] = new FormValidation($val);
            $response->header->status           = RequestStatusCode::R_400;

            return;
        }

        $attribute = $this->createUnitAttributeFromRequest($request);
        $this->createModel($request->header->account, $attribute, UnitAttributeMapper::class, 'attribute', $request->getOrigin());

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Attribute', 'Attribute successfully created', $attribute);
    }

    /**
     * Method to create item attribute from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return Attribute
     *
     * @since 1.0.0
     */
    private function createUnitAttributeFromRequest(RequestAbstract $request) : Attribute
    {
        $attribute       = new Attribute();
        $attribute->ref  = (int) $request->getData('unit');
        $attribute->type = new NullAttributeType((int) $request->getData('type'));

        if ($request->hasData('value')) {
            $attribute->value = new NullAttributeValue((int) $request->getData('value'));
        } else {
            $newRequest = clone $request;
            $newRequest->setData('value', $request->getData('custom'), true);

            $value = $this->createUnitAttributeValueFromRequest($newRequest);

            $attribute->value = $value;
        }

        return $attribute;
    }

    /**
     * Validate unit attribute create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateUnitAttributeCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['type'] = !$request->hasData('type'))
            || ($val['value'] = (!$request->hasData('value') && !$request->hasData('custom')))
            || ($val['unit'] = !$request->hasData('unit'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create unit attribute l11n
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
    public function apiUnitAttributeTypeL11nCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateUnitAttributeTypeL11nCreate($request))) {
            $response->data['attr_type_l11n_create'] = new FormValidation($val);
            $response->header->status                = RequestStatusCode::R_400;

            return;
        }

        $attrL11n = $this->createUnitAttributeTypeL11nFromRequest($request);
        $this->createModel($request->header->account, $attrL11n, UnitAttributeTypeL11nMapper::class, 'attr_type_l11n', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Localization', 'Localization successfully created', $attrL11n);
    }

    /**
     * Method to create unit attribute l11n from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return BaseStringL11n
     *
     * @since 1.0.0
     */
    private function createUnitAttributeTypeL11nFromRequest(RequestAbstract $request) : BaseStringL11n
    {
        $attrL11n      = new BaseStringL11n();
        $attrL11n->ref = $request->getDataInt('type') ?? 0;
        $attrL11n->setLanguage(
            $request->getDataString('language') ?? $request->header->l11n->language
        );
        $attrL11n->content = $request->getDataString('title') ?? '';

        return $attrL11n;
    }

    /**
     * Validate unit attribute l11n create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateUnitAttributeTypeL11nCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['title'] = !$request->hasData('title'))
            || ($val['type'] = !$request->hasData('type'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create unit attribute type
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
    public function apiUnitAttributeTypeCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateUnitAttributeTypeCreate($request))) {
            $response->data['attr_type_create'] = new FormValidation($val);
            $response->header->status           = RequestStatusCode::R_400;

            return;
        }

        $attrType = $this->createUnitAttributeTypeFromRequest($request);
        $this->createModel($request->header->account, $attrType, UnitAttributeTypeMapper::class, 'attr_type', $request->getOrigin());

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Attribute type', 'Attribute type successfully created', $attrType);
    }

    /**
     * Method to create unit attribute from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return AttributeType
     *
     * @since 1.0.0
     */
    private function createUnitAttributeTypeFromRequest(RequestAbstract $request) : AttributeType
    {
        $attrType                    = new AttributeType($request->getDataString('name') ?? '');
        $attrType->datatype          = $request->getDataInt('datatype') ?? 0;
        $attrType->custom            = $request->getDataBool('custom') ?? false;
        $attrType->isRequired        = (bool) ($request->getData('is_required') ?? false);
        $attrType->validationPattern = $request->getDataString('validation_pattern') ?? '';
        $attrType->setL11n($request->getDataString('title') ?? '', $request->getDataString('language') ?? ISO639x1Enum::_EN);
        $attrType->setFields($request->getDataInt('fields') ?? 0);

        return $attrType;
    }

    /**
     * Validate unit attribute create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateUnitAttributeTypeCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['title'] = !$request->hasData('title'))
            || ($val['name'] = !$request->hasData('name'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create unit attribute value
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
    public function apiUnitAttributeValueCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateUnitAttributeValueCreate($request))) {
            $response->data['attr_value_create'] = new FormValidation($val);
            $response->header->status            = RequestStatusCode::R_400;

            return;
        }

        $attrValue = $this->createUnitAttributeValueFromRequest($request);
        $this->createModel($request->header->account, $attrValue, UnitAttributeValueMapper::class, 'attr_value', $request->getOrigin());

        if ($attrValue->isDefault) {
            $this->createModelRelation(
                $request->header->account,
                (int) $request->getData('type'),
                $attrValue->id,
                UnitAttributeTypeMapper::class, 'defaults', '', $request->getOrigin()
            );
        }

        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Attribute value', 'Attribute value successfully created', $attrValue);
    }

    /**
     * Method to create unit attribute value from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return AttributeValue
     *
     * @since 1.0.0
     */
    private function createUnitAttributeValueFromRequest(RequestAbstract $request) : AttributeValue
    {
        /** @var AttributeType $type */
        $type = UnitAttributeTypeMapper::get()
            ->where('id', $request->getDataInt('type') ?? 0)
            ->execute();

        $attrValue            = new AttributeValue();
        $attrValue->isDefault = $request->getDataBool('default') ?? false;
        $attrValue->setValue($request->getData('value'), $type->datatype);

        if ($request->hasData('title')) {
            $attrValue->setL11n(
                $request->getDataString('title') ?? '',
                $request->getDataString('language') ?? ISO639x1Enum::_EN
            );
        }

        return $attrValue;
    }

    /**
     * Validate unit attribute value create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateUnitAttributeValueCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['type'] = !$request->hasData('type'))
            || ($val['value'] = !$request->hasData('value'))
        ) {
            return $val;
        }

        return [];
    }

    /**
     * Api method to create unit attribute l11n
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
    public function apiUnitAttributeValueL11nCreate(RequestAbstract $request, ResponseAbstract $response, mixed $data = null) : void
    {
        if (!empty($val = $this->validateUnitAttributeValueL11nCreate($request))) {
            $response->data['attr_value_l11n_create'] = new FormValidation($val);
            $response->header->status                 = RequestStatusCode::R_400;

            return;
        }

        $attrL11n = $this->createUnitAttributeValueL11nFromRequest($request);
        $this->createModel($request->header->account, $attrL11n, UnitAttributeValueL11nMapper::class, 'attr_value_l11n', $request->getOrigin());
        $this->fillJsonResponse($request, $response, NotificationLevel::OK, 'Localization', 'Localization successfully created', $attrL11n);
    }

    /**
     * Method to create unit attribute l11n from request.
     *
     * @param RequestAbstract $request Request
     *
     * @return BaseStringL11n
     *
     * @since 1.0.0
     */
    private function createUnitAttributeValueL11nFromRequest(RequestAbstract $request) : BaseStringL11n
    {
        $attrL11n      = new BaseStringL11n();
        $attrL11n->ref = $request->getDataInt('value') ?? 0;
        $attrL11n->setLanguage(
            $request->getDataString('language') ?? $request->header->l11n->language
        );
        $attrL11n->content = $request->getDataString('title') ?? '';

        return $attrL11n;
    }

    /**
     * Validate unit attribute l11n create request
     *
     * @param RequestAbstract $request Request
     *
     * @return array<string, bool>
     *
     * @since 1.0.0
     */
    private function validateUnitAttributeValueL11nCreate(RequestAbstract $request) : array
    {
        $val = [];
        if (($val['title'] = !$request->hasData('title'))
            || ($val['value'] = !$request->hasData('value'))
        ) {
            return $val;
        }

        return [];
    }
}
